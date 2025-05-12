<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use App\Models\Entrepreneur;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::orderBy('name')->get();
        $entrepreneurs = Entrepreneur::orderBy('name')->get();
        return view('settings.index', compact('settings', 'entrepreneurs'));
    }

    public function update(Request $request, Setting $setting)
    {
        $validated = $request->validate([
            'value' => 'required|string',
        ]);

        $setting->update($validated);

        return redirect()->route('settings.index')
            ->with('success', 'Setting updated successfully');
    }
}