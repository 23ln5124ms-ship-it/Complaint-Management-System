<?php

namespace App\Exports;

use App\Models\Complaint;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ComplaintsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    public function __construct(
        private ?string $from = null,
        private ?string $to = null,
    ) {}

    public function collection()
    {
        return Complaint::with(['user', 'category'])
            ->when($this->from && $this->to, fn($q) =>
                $q->whereBetween('created_at', [$this->from, $this->to . ' 23:59:59'])
            )
            ->latest()
            ->get();
    }

    public function headings(): array
    {
        return [
            'Ticket #',
            'Title',
            'Submitted By',
            'Email',
            'Category',
            'Status',
            'Priority',
            'Date Submitted',
            'Date Resolved',
            '# Responses',
        ];
    }

    public function map($complaint): array
    {
        return [
            $complaint->ticket_number,
            $complaint->title,
            $complaint->user->name,
            $complaint->user->email,
            $complaint->category->name,
            strtoupper($complaint->status),
            strtoupper($complaint->priority),
            $complaint->created_at->format('Y-m-d'),
            $complaint->resolved_at?->format('Y-m-d') ?? '—',
            $complaint->responses->count(),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill'      => ['fillType' => 'solid', 'startColor' => ['argb' => 'FF4F46E5']],
                'alignment' => ['horizontal' => 'center'],
            ],
        ];
    }
}
