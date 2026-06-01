<?php

namespace App\Http\Controllers\Admin;

use App\Models\Complaint;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

class DashboardController
{
    public function index()
    {
        $stats = [
            [
                'label' => 'Total Complaints',
                'value' => Complaint::count(),
                'color' => 'bg-blue-500',
            ],
            [
                'label' => 'Pending',
                'value' => Complaint::where('status', 'pending')->count(),
                'color' => 'bg-yellow-500',
            ],
            [
                'label' => 'In Progress',
                'value' => Complaint::where('status', 'in_progress')->count(),
                'color' => 'bg-orange-500',
            ],
            [
                'label' => 'Resolved',
                'value' => Complaint::where('status', 'resolved')->count(),
                'color' => 'bg-green-500',
            ],
        ];

        $recentComplaints = Complaint::with(['user', 'category'])
                                      ->latest()
                                      ->take(10)
                                      ->get();

        $categoryStats = Category::withCount('complaints')
                                  ->orderByDesc('complaints_count')
                                  ->take(5)
                                  ->get();

        return view('admin.dashboard', compact('stats', 'recentComplaints', 'categoryStats'));
    }
}
