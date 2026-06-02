<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ComplaintsExport;

class ReportController extends Controller
{
    // ─── Analytics Dashboard ─────────────────────────────────────────────────

    public function index(Request $request)
    {
        $from = $request->from ?? now()->startOfMonth()->toDateString();
        $to   = $request->to ?? now()->toDateString();

        $complaints = Complaint::with(['category', 'user'])
            ->whereBetween('created_at', [$from, "{$to} 23:59:59"])
            ->get();

        $stats = [
            'total'            => $complaints->count(),
            'resolved'         => $complaints->whereIn('status', ['resolved', 'closed'])->count(),
            'pending'          => $complaints->where('status', 'pending')->count(),
            'avg_response_hrs' => $this->avgResponseTime($from, $to),
        ];

        $byStatus = $complaints->groupBy('status')
            ->map->count();

        $byPriority = $complaints->groupBy('priority')
            ->map->count();

        $byCategory = $complaints->groupBy('category.name')
            ->map->count();

        return view('admin.reports.index', compact(
            'stats', 'byStatus', 'byPriority', 'byCategory', 'from', 'to'
        ));
    }

    // ─── Export: PDF ─────────────────────────────────────────────────────────

    public function exportPdf(Request $request)
    {
        $complaints = $this->filteredComplaints($request);

        $pdf = Pdf::loadView('admin.reports.pdf', compact('complaints'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('complaints-report-' . now()->format('Y-m-d') . '.pdf');
    }

    // ─── Export: CSV ─────────────────────────────────────────────────────────

    public function exportCsv(Request $request)
    {
        $complaints = $this->filteredComplaints($request);

        $filename = 'complaints-report-' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ];

        $columns = ['Ticket #', 'Title', 'User', 'Category', 'Status', 'Priority', 'Submitted', 'Resolved'];

        $callback = function () use ($complaints, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($complaints as $c) {
                fputcsv($file, [
                    $c->ticket_number,
                    $c->title,
                    $c->user->name,
                    $c->category->name,
                    $c->status,
                    $c->priority,
                    $c->created_at->format('Y-m-d'),
                    $c->resolved_at?->format('Y-m-d') ?? 'N/A',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // ─── Export: XLSX ────────────────────────────────────────────────────────

    public function exportXlsx(Request $request)
    {
        return Excel::download(
            new ComplaintsExport($request->from, $request->to),
            'complaints-report-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    // ─── Export: JSON ────────────────────────────────────────────────────────

    public function exportJson(Request $request)
    {
        $complaints = $this->filteredComplaints($request)->map(fn($c) => [
            'ticket'    => $c->ticket_number,
            'title'     => $c->title,
            'user'      => $c->user->name,
            'category'  => $c->category->name,
            'status'    => $c->status,
            'priority'  => $c->priority,
            'submitted' => $c->created_at->toDateString(),
            'resolved'  => $c->resolved_at?->toDateString(),
        ]);

        return response()->json(['data' => $complaints])
            ->header('Content-Disposition', 'attachment; filename=complaints-report.json');
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    private function filteredComplaints(Request $request)
    {
        return Complaint::with(['user', 'category'])
            ->when($request->from && $request->to, fn($q) =>
                $q->whereBetween('created_at', [$request->from, $request->to . ' 23:59:59'])
            )
            ->when($request->status, fn($q, $s) => $q->byStatus($s))
            ->latest()
            ->get();
    }

    private function avgResponseTime(string $from, string $to): float
    {
        $driver = DB::getDriverName();
        $expression = match ($driver) {
            'sqlite' => 'AVG((julianday(responses.created_at) - julianday(complaints.created_at)) * 24)',
            default => 'AVG(TIMESTAMPDIFF(HOUR, complaints.created_at, responses.created_at))',
        };

        return round(
            DB::table('complaints')
                ->join('responses', 'complaints.id', '=', 'responses.complaint_id')
                ->whereBetween('complaints.created_at', [$from, "{$to} 23:59:59"])
                ->selectRaw("{$expression} as avg_hours")
                ->value('avg_hours') ?? 0,
            1
        );
    }
}

