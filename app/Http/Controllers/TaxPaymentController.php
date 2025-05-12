<?php

namespace App\Http\Controllers;

use App\Models\Entrepreneur;
use App\Models\TaxPayment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TaxPaymentController extends Controller
{
    public function index(Entrepreneur $entrepreneur, Request $request)
    {
        $year = $request->get('year', now()->year);
        $search = $request->get('search');
        $entrepreneurs = Entrepreneur::orderBy('name')->get();

        $payments = TaxPayment::where('id_entrepreneurs', $entrepreneur->id_entrepreneurs)
            ->whereYear('date', $year)
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('description', 'like', "%{$search}%")
                        ->orWhere('amount', 'like', "%{$search}%");
                });
            })
            ->orderBy('date', 'desc')
            ->get()
            ->groupBy(function ($payment) {
                return $payment->date->format('m'); // Use format() instead of direct month access
            });

        $monthlyTotals = $payments->map(function ($monthPayments) {
            return $monthPayments->sum('amount');
        });

        return view('entrepreneurs.tax-payments', compact(
            'entrepreneur',
            'payments',
            'monthlyTotals',
            'year',
            'entrepreneurs'
        ));
    }

    // Update the store method in TaxPaymentController.php
    public function store(Request $request, Entrepreneur $entrepreneur)
    {
        try {
            $validated = $request->validate([
                'date' => 'required|date',
                'amount' => 'required|numeric|min:0',
                'description' => 'nullable|string'
            ]);

            // Format amount to ensure proper decimal handling
            $validated['amount'] = number_format((float)$validated['amount'], 2, '.', '');

            // Create the payment
            $payment = $entrepreneur->taxPayments()->create($validated);

            return response()->json([
                'success' => true,
                'payment' => $payment,
                'formatted_date' => $payment->date->format('Y-m-d'),
                'formatted_amount' => number_format($payment->amount, 2)
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Failed to create tax payment: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create tax payment'
            ], 500);
        }
    }



    public function update(Request $request, TaxPayment $payment)
    {
        try {
            // Validate only the field being updated
            $rules = [];
            if ($request->has('date')) {
                $rules['date'] = 'required|date';
            }
            if ($request->has('amount')) {
                $rules['amount'] = 'required|numeric|min:0';
            }
            if ($request->has('description')) {
                $rules['description'] = 'nullable|string';
            }

            $validated = $request->validate($rules);

            // Update only the validated fields
            $payment->update($validated);

            return response()->json([
                'success' => true,
                'payment' => $payment,
                'formatted_date' => $payment->date->format('Y-m-d'),
                'formatted_amount' => number_format($payment->amount, 2)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update payment: ' . $e->getMessage()
            ], 422);
        }
    }

    public function destroy(TaxPayment $payment)
    {
        $payment->delete();
        return response()->json(['success' => true]);
    }
}
