<?php

namespace App\Http\Controllers;

use App\Models\Kved;
use Illuminate\Http\Request;

class KvedController extends Controller
{
    public function index()
    {
        $kveds = Kved::orderBy('number')->paginate(15);
        return view('kveds.index', compact('kveds'));
    }

    public function create()
    {
        return view('kveds.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'number' => 'required|string|unique:kveds',
            'name' => 'required|string|max:255'
        ]);

        Kved::create($validated);

        return redirect()->route('kveds.index')
            ->with('success', 'KVED created successfully.');
    }
}