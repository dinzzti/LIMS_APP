<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreThermalLogRequest;
use App\Models\Sample;
use App\Models\ThermalLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Carbon\Carbon;

class ThermalController extends Controller
{
    public function create(): View
    {
        $samples = Sample::where('status', 'waiting_thermal')->get();

        $thermalLogs = ThermalLog::with('sample')
            ->latest()
            ->take(10)
            ->get();

        return view('thermal.create', compact('samples', 'thermalLogs'));
    }

    public function store(StoreThermalLogRequest $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validated();
        $validated['started_at'] = Carbon::now();

        $thermalLog = ThermalLog::create($validated);

        Sample::find($validated['sample_id'])
            ->update(['status' => 'processing']);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Proses pemanasan dimulai.',
                'data'    => $thermalLog,
            ], 201);
        }

        return redirect()->route('thermal.timer', $thermalLog->id)
            ->with('success', 'Timer pemanasan dimulai.');
    }

    public function timer(ThermalLog $thermalLog): View
    {
        $thermalLog->load('sample');

        $endsAt        = Carbon::parse($thermalLog->started_at)
            ->addMinutes($thermalLog->duration_minutes);
        $remainSeconds = max(0, (int) Carbon::now()->diffInSeconds($endsAt, false));

        return view('thermal.timer', compact('thermalLog', 'remainSeconds', 'endsAt'));
    }

    /**
     * Selesaikan proses pemanasan dan pindahkan sampel ke tahap PCR (US 2.5)
     * Hanya bisa dipanggil jika timer sudah benar-benar habis (server-side check).
     */
    public function complete(ThermalLog $thermalLog): RedirectResponse|JsonResponse
    {
        $endsAt = Carbon::parse($thermalLog->started_at)
            ->addMinutes($thermalLog->duration_minutes);

        // Guard: tolak jika timer belum habis (antisipasi manipulasi request)
        if (Carbon::now()->lessThan($endsAt)) {
            $remainSeconds = (int) Carbon::now()->diffInSeconds($endsAt, false);

            if (request()->expectsJson()) {
                return response()->json([
                    'message' => 'Timer belum selesai.',
                    'remain_seconds' => $remainSeconds,
                ], 422);
            }

            return redirect()->route('thermal.timer', $thermalLog->id)
                ->withErrors(['timer' => 'Proses pemanasan belum selesai. Timer masih berjalan.']);
        }

        // Pindahkan status sampel → ready_pcr
        $thermalLog->sample->update(['status' => 'ready_pcr']);

        if (request()->expectsJson()) {
            return response()->json([
                'message' => 'Sampel berhasil dipindahkan ke tahap PCR.',
                'sample'  => $thermalLog->sample,
            ]);
        }

        return redirect()->route('thermal.queue')
            ->with(
                'success',
                "Sampel {$thermalLog->sample->sample_code} ({$thermalLog->sample->patient_name}) "
                    . "berhasil dipindahkan ke tabel Siap Uji PCR."
            );
    }
}
