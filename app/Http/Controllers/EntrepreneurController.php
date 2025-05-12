<?php

namespace App\Http\Controllers;

use App\Models\Entrepreneur;
use App\Models\Kved;
use App\Models\Key;
use App\Models\Setting;
use App\Models\Report;
use App\Models\ReportEntrepreneur;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class EntrepreneurController extends Controller
{
    public function index()
    {
        $entrepreneurs = Entrepreneur::orderBy('name')->paginate(15);
        return view('entrepreneurs.index', compact('entrepreneurs'));
    }

    public function create()
    {
        $entrepreneurs = Entrepreneur::orderBy('name')->get();
        $kveds = Kved::orderBy('number')->take(10)->get();
        return view('entrepreneurs.create', compact('entrepreneurs', 'kveds'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'ipn' => 'required|string|unique:entrepreneurs',
            'iban' => 'required|string',
            'tax_office_name' => 'required|string',
            'group' => 'required|string',
            'kveds' => 'nullable|array',
            'kveds.*' => 'exists:kveds,id_kved',
            'private_key_start' => 'nullable|date',
            'private_key_end' => 'nullable|date|after:private_key_start',
            'asc_key_start' => 'nullable|date',
            'asc_key_end' => 'nullable|date|after:asc_key_start',
        ]);

        $entrepreneurData = collect($validated)->except(['kveds', 'private_key_start', 'private_key_end', 'asc_key_start', 'asc_key_end'])->toArray();
        $entrepreneur = Entrepreneur::create($entrepreneurData);

        if ($request->has('kveds')) {
            $entrepreneur->kveds()->attach($request->kveds);
        }

        // Create private key if dates are provided
        if ($request->filled('private_key_start') && $request->filled('private_key_end')) {
            $entrepreneur->keys()->create([
                'type' => 'private',
                'date_start' => $request->private_key_start,
                'date_end' => $request->private_key_end,
            ]);
        }

        // Create ASC key if dates are provided
        if ($request->filled('asc_key_start') && $request->filled('asc_key_end')) {
            $entrepreneur->keys()->create([
                'type' => 'asc',
                'date_start' => $request->asc_key_start,
                'date_end' => $request->asc_key_end,
            ]);
        }

        return redirect()->route('entrepreneurs.show', $entrepreneur)
            ->with('success', 'Entrepreneur created successfully.');
    }

    public function show(Entrepreneur $entrepreneur)
    {
        $entrepreneurs = Entrepreneur::orderBy('name')->get();
        $allowedSettingIds = [];
        switch ($entrepreneur->group) {
            case '1':
                $allowedSettingIds = [4, 5, 2];
                break;
            case '2':
                $allowedSettingIds = [1, 2, 5];
                break;
            case '3':
                $allowedSettingIds = [6, 3];
                break;
        }

        $settings = Setting::whereIn('id', $allowedSettingIds)
            ->orderBy('name')
            ->get();
        $reports = Report::orderBy('name')->get();

        return view('entrepreneurs.show', compact('entrepreneur', 'entrepreneurs', 'settings', 'reports'));
    }

    public function updateReport(Request $request)
    {
        \Log::info('Update report request received', [
            'data' => $request->all()
        ]);

        try {
            // Convert done to boolean before validation
            $data = $request->all();
            $data['done'] = filter_var($data['done'], FILTER_VALIDATE_BOOLEAN);

            $validated = Validator::make($data, [
                'entrepreneur_id' => 'required|integer|exists:entrepreneurs,id_entrepreneurs',
                'report_id' => 'required|integer|exists:reports,id_report',
                'quarter' => 'required|integer|between:1,4',
                'year' => 'required|integer|min:2020',
                'done' => 'boolean'
            ])->validate();

            $reportEntrepreneur = ReportEntrepreneur::updateOrCreate(
                [
                    'id_entrepreneurs' => $validated['entrepreneur_id'],
                    'id_report' => $validated['report_id'],
                    'quarter' => $validated['quarter'],
                    'year' => $validated['year']
                ],
                ['done' => $validated['done']]
            );

            return response()->json([
                'success' => true,
                'message' => 'Report status updated successfully',
                'data' => $reportEntrepreneur
            ]);
        } catch (\Exception $e) {
            \Log::error('Error updating report status', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update report status: ' . $e->getMessage()
            ], 500);
        }
    }

    public function edit(Entrepreneur $entrepreneur)
    {
        $entrepreneurs = Entrepreneur::orderBy('name')->get();

        // Get all selected KVEDs
        $selectedKveds = $entrepreneur->kveds;

        // Get additional KVEDs up to 10 total if needed
        $additionalKvedsNeeded = max(0, 10 - $selectedKveds->count());
        $additionalKveds = collect();

        if ($additionalKvedsNeeded > 0) {
            $additionalKveds = Kved::whereNotIn('id_kved', $selectedKveds->pluck('id_kved'))
                ->orderBy('number')
                ->take($additionalKvedsNeeded)
                ->get();
        }

        // Merge selected and additional KVEDs
        $kveds = $selectedKveds->concat($additionalKveds);

        return view('entrepreneurs.edit', compact('entrepreneur', 'entrepreneurs', 'kveds'));
    }

    public function update(Request $request, Entrepreneur $entrepreneur)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'ipn' => ['required', 'string', Rule::unique('entrepreneurs')->ignore($entrepreneur->id_entrepreneurs, 'id_entrepreneurs')],
            'iban' => 'required|string',
            'tax_office_name' => 'required|string',
            'group' => 'required|string',
            'kveds' => 'nullable|array',
            'kveds.*' => 'exists:kveds,id_kved',
            'private_key_start' => 'nullable|date',
            'private_key_end' => 'nullable|date|after:private_key_start',
            'asc_key_start' => 'nullable|date',
            'asc_key_end' => 'nullable|date|after:asc_key_start',

        ]);

        $entrepreneurData = collect($validated)->except(['kveds', 'private_key_start', 'private_key_end', 'asc_key_start', 'asc_key_end'])->toArray();
        $entrepreneur->update($entrepreneurData);
        $entrepreneur->kveds()->sync($request->kveds ?? []);

        // Update private key if dates are provided, or delete if empty
        if ($request->filled('private_key_start') && $request->filled('private_key_end')) {
            $entrepreneur->keys()->updateOrCreate(
                ['type' => 'private'],
                [
                    'date_start' => $request->private_key_start,
                    'date_end' => $request->private_key_end,
                ]
            );
        } else {
            $entrepreneur->keys()->where('type', 'private')->delete();
        }

        // Update ASC key if dates are provided, or delete if empty
        if ($request->filled('asc_key_start') && $request->filled('asc_key_end')) {
            $entrepreneur->keys()->updateOrCreate(
                ['type' => 'asc'],
                [
                    'date_start' => $request->asc_key_start,
                    'date_end' => $request->asc_key_end,
                ]
            );
        } else {
            $entrepreneur->keys()->where('type', 'asc')->delete();
        }

        return redirect()->route('entrepreneurs.show', $entrepreneur)
            ->with('success', 'Entrepreneur updated successfully.');
    }

    public function keysOverview()
    {
        $expiringKeys = Key::with('entrepreneur')
            ->whereDate('date_end', '>=', now())
            ->orderBy('date_end')
            ->take(5)
            ->get();

        $currentQuarter = ceil(now()->month / 3);
        $currentYear = now()->year;

        $entrepreneurs = Entrepreneur::orderBy('name')->get();
        $reports = Report::orderBy('name')->get();

        // Get all report statuses for the current quarter
        $reportStatuses = ReportEntrepreneur::where('quarter', $currentQuarter)
            ->where('year', $currentYear)
            ->get()
            ->groupBy(['id_entrepreneurs', 'id_report']);

        return view('entrepreneurs.keys-overview', compact(
            'expiringKeys',
            'entrepreneurs',
            'reports',
            'currentQuarter',
            'currentYear',
            'reportStatuses'
        ));
    }

    public function destroy(Entrepreneur $entrepreneur)
    {
        $entrepreneur->delete();
        return redirect()->route('entrepreneurs.index')
            ->with('success', 'Entrepreneur deleted successfully.');
    }

    public function search(Request $request)
    {
        $query = $request->get('query');

        $entrepreneurs = Entrepreneur::where('name', 'LIKE', "%{$query}%")
            ->orWhere('ipn', 'LIKE', "%{$query}%")
            ->orderBy('name')
            ->get();

        return response()->json($entrepreneurs);
    }

    public function searchKveds(Request $request)
    {
        $query = $request->get('query');
        $selectedKvedIds = $request->get('selected_kveds', []);

        $kveds = Kved::where(function ($q) use ($query) {
            $q->where('number', 'LIKE', "%{$query}%")
                ->orWhere('name', 'LIKE', "%{$query}%");
        });

        if (!empty($selectedKvedIds)) {
            // Put selected KVEDs first in the results
            $kveds = $kveds->orderByRaw("FIELD(id_kved, " . implode(',', $selectedKvedIds) . ") DESC, number ASC");
        } else {
            $kveds = $kveds->orderBy('number');
        }

        $kveds = $kveds->take(10)->get();

        return response()->json([
            'kveds' => $kveds,
            'selected' => $selectedKvedIds
        ]);
    }
}
