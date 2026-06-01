<?php

namespace App\Http\Controllers\Admin;

use App\Models\Complaint;
use Illuminate\Http\Request;

class ReportController
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', now()->subMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));
        $status = $request->input('status');

        $query = Complaint::whereBetween('created_at', [$startDate, $endDate]);

        if ($status) {
            $query->where('status', $status);
        }

        $complaints = $query->with(['user', 'category'])->get();
        $stats = $this->generateStats($complaints);

        return view('admin.reports.index', compact('complaints', 'stats', 'startDate', 'endDate', 'status'));
    }

    public function exportPdf(Request $request)
    {
        $startDate = $request->input('start_date', now()->subMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));

        $complaints = Complaint::whereBetween('created_at', [$startDate, $endDate])
                                ->with(['user', 'category'])
                                ->get();

        return view('admin.reports.pdf', compact('complaints', 'startDate', 'endDate'));
    }

    public function generateStats($complaints)
    {
        return [
            'total' => $complaints->count(),
            'by_status' => $complaints->groupBy('status')->map->count(),
            'by_priority' => $complaints->groupBy('priority')->map->count(),
            'by_category' => $complaints->groupBy('category.name')->map->count(),
            'resolved' => $complaints->where('status', 'resolved')->count(),
            'pending' => $complaints->where('status', 'pending')->count(),
        ];
    }
}
