<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreThermalLogRequest;
use App\Models\Sample;
use App\Models\ThermalLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ThermalController extends Controller
{
    /**
     * Show the form for creating a new thermal log.
     */
    public function create(): View
    {
        $samples = Sample::all();
        
        // Ambil data thermal logs terbaru dengan eager loading untuk efisiensi
        $thermalLogs = ThermalLog::with('sample')
            ->latest() // Urutkan berdasarkan created_at descending
            ->take(10) // Ambil 10 data terbaru untuk performa
            ->get();
        
        return view('thermal.create', compact('samples', 'thermalLogs'));
    }


    /**
     * Store a newly created thermal log in storage.
     */
    public function store(StoreThermalLogRequest $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validated();

        $thermalLog = ThermalLog::create($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Data suhu pemanasan berhasil disimpan.',
                'data' => $thermalLog,
            ], 201);
        }

        return redirect()->route('thermal.create')
            ->with('success', 'Data suhu pemanasan berhasil disimpan.');
    }
}