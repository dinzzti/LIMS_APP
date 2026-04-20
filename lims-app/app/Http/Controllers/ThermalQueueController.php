<?php

namespace App\Http\Controllers;

use App\Models\Sample;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ThermalQueueController extends Controller
{
    public function index(Request $request): View
    {
        $perPage = $request->get('per_page', 10);
        $search  = $request->get('search');

        $waitingQuery = Sample::where('status', 'waiting_thermal');

        if (!empty($search)) {
            $waitingQuery->where('patient_name', 'like', "%{$search}%");
        }

        $waitingQueue = $waitingQuery
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        $processingQueue = Sample::where('status', 'processing')
            ->with(['thermalLogs' => fn($q) => $q->latest()->take(1)])
            ->latest()
            ->get();

        $readyPcrQueue = Sample::where('status', 'ready_pcr')
            ->whereDate('updated_at', today())
            ->latest()
            ->get();

        $completedToday = Sample::where('status', 'ready_pcr')
            ->whereDate('updated_at', today())
            ->count();

        return view('thermal.queue', compact(
            'waitingQueue',
            'processingQueue',
            'readyPcrQueue',
            'completedToday'
        ));
    }
}
