<x-layout title="All Complaints">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900">All Complaints</h1>
            <a href="{{ route('admin.reports.index') }}"
               class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                📊 Reports
            </a>
        </div>
    </x-slot>

    {{-- Filters --}}
    <form method="GET" class="mb-6 flex flex-wrap gap-3 rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-200">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by ticket, title, user…"
               class="flex-1 rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 min-w-48">

        <select name="status" class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
            <option value="">All Statuses</option>
            @foreach(['pending','open','in_progress','resolved','closed','rejected'] as $s)
                <option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
            @endforeach
        </select>

        <select name="priority" class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
            <option value="">All Priorities</option>
            @foreach(['low','medium','high','urgent'] as $p)
                <option value="{{ $p }}" @selected(request('priority') === $p)>{{ ucfirst($p) }}</option>
            @endforeach
        </select>

        <select name="category_id" class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
            <option value="">All Categories</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}" @selected(request('category_id') == $cat->id)>{{ $cat->name }}</option>
            @endforeach
        </select>

        <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Filter</button>
        <a href="{{ route('admin.complaints.index') }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm text-gray-600 hover:bg-gray-50">Reset</a>
    </form>

    {{-- Table --}}
    <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-200 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Ticket</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Title</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">User</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Category</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Priority</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Status</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Date</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-600">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($complaints as $complaint)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 font-mono text-xs text-gray-500">{{ $complaint->ticket_number }}</td>
                        <td class="px-4 py-3 max-w-xs">
                            <a href="{{ route('admin.complaints.show', $complaint) }}"
                               class="font-medium text-gray-900 hover:text-indigo-600 line-clamp-1">
                                {{ $complaint->title }}
                            </a>
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ $complaint->user->name }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center gap-1 text-xs text-gray-600">
                                <span class="h-2 w-2 rounded-full" style="background-color:{{ $complaint->category->color }}"></span>
                                {{ $complaint->category->name }}
                            </span>
                        </td>
                        <td class="px-4 py-3"><x-status-badge :status="$complaint->priority" type="priority" /></td>
                        <td class="px-4 py-3"><x-status-badge :status="$complaint->status" /></td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $complaint->created_at->format('M d, Y') }}</td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admin.complaints.show', $complaint) }}"
                               class="text-indigo-600 hover:text-indigo-500 text-xs font-medium">View →</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="py-12 text-center text-gray-400">No complaints found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($complaints->hasPages())
            <div class="border-t border-gray-200 px-4 py-3">
                {{ $complaints->withQueryString()->links() }}
            </div>
        @endif
    </div>
</x-layout>
