<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Complaints Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #111; }
        h1 { font-size: 20px; margin-bottom: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { border: 1px solid #ccc; padding: 8px 10px; text-align: left; font-size: 12px; }
        th { background: #f4f4f4; }
        .small { font-size: 11px; color: #555; }
    </style>
</head>
<body>
    <h1>Complaints Report</h1>
    <p class="small">Generated on {{ now()->format('Y-m-d H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th>Ticket</th>
                <th>Title</th>
                <th>User</th>
                <th>Category</th>
                <th>Status</th>
                <th>Priority</th>
                <th>Submitted</th>
                <th>Resolved</th>
            </tr>
        </thead>
        <tbody>
            @forelse($complaints as $complaint)
                <tr>
                    <td>{{ $complaint->ticket_number }}</td>
                    <td>{{ $complaint->title }}</td>
                    <td>{{ $complaint->user->name }}</td>
                    <td>{{ $complaint->category->name }}</td>
                    <td>{{ ucfirst(str_replace('_', ' ', $complaint->status)) }}</td>
                    <td>{{ ucfirst($complaint->priority) }}</td>
                    <td>{{ $complaint->created_at->format('Y-m-d') }}</td>
                    <td>
                        @php
                            $resolvedDate = $complaint->resolved_at
                                ?? ($complaint->status === 'resolved' || $complaint->status === 'closed'
                                    ? ($complaint->updated_at ?? $complaint->created_at)
                                    : null);
                        @endphp
                        {{ $resolvedDate ? $resolvedDate->format('Y-m-d') : '—' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align:center; padding: 20px;">No complaints found for the selected date range.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
