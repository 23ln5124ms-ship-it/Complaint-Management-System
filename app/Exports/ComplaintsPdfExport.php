<?php

namespace App\Exports;

use Illuminate\Support\Collection;

class ComplaintsPdfExport
{
    protected $complaints;

    public function __construct(Collection $complaints)
    {
        $this->complaints = $complaints;
    }

    public function generate()
    {
        $html = '<html><head><style>
            table { border-collapse: collapse; width: 100%; }
            th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
            th { background-color: #f2f2f2; font-weight: bold; }
            tr:nth-child(even) { background-color: #f9f9f9; }
        </style></head><body>';

        $html .= '<h2>Complaint Report</h2>';
        $html .= '<p>Generated on: ' . now()->format('Y-m-d H:i:s') . '</p>';
        $html .= '<table>';
        $html .= '<thead><tr>
                    <th>Ticket Number</th>
                    <th>Title</th>
                    <th>User</th>
                    <th>Category</th>
                    <th>Status</th>
                    <th>Priority</th>
                    <th>Created At</th>
                    <th>Resolved At</th>
                  </tr></thead>';
        $html .= '<tbody>';

        foreach ($this->complaints as $complaint) {
            $html .= '<tr>';
            $html .= '<td>' . $complaint->ticket_number . '</td>';
            $html .= '<td>' . $complaint->title . '</td>';
            $html .= '<td>' . $complaint->user->name . '</td>';
            $html .= '<td>' . $complaint->category->name . '</td>';
            $html .= '<td>' . ucfirst(str_replace('_', ' ', $complaint->status)) . '</td>';
            $html .= '<td>' . ucfirst($complaint->priority) . '</td>';
            $html .= '<td>' . $complaint->created_at->format('Y-m-d') . '</td>';
            $html .= '<td>' . ($complaint->resolved_at?->format('Y-m-d') ?? 'N/A') . '</td>';
            $html .= '</tr>';
        }

        $html .= '</tbody></table>';
        $html .= '</body></html>';

        return $html;
    }
}
