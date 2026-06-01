<x-layout title="My Complaints">
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">My Complaints</h1>
                <p class="text-sm text-gray-500">Track your submitted complaints and see responses.</p>
            </div>
            <a href="{{ route('user.complaints.create') }}"
               class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                Submit Complaint
            </a>
        </div>
    </x-slot>

    <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-200 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Ticket</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Title</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Category</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Status</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Updated</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-600">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                @forelse($complaints as $complaint)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 font-mono text-xs text-gray-500">{{ $complaint->ticket_number }}</td>
                        <td class="px-4 py-3 max-w-xs">
                            <a href="{{ route('user.complaints.show', $complaint) }}"
                               class="font-medium text-gray-900 hover:text-indigo-600 line-clamp-1">
                                {{ $complaint->title }}
                            </a>
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ $complaint->category->name }}</td>
                        <td class="px-4 py-3"><x-status-badge :status="$complaint->status" /></td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $complaint->updated_at->format('M d, Y') }}</td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('user.complaints.show', $complaint) }}"
                               class="text-indigo-600 hover:text-indigo-500 text-xs font-medium">View →</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-12 text-center text-sm text-gray-500">You have not submitted any complaints yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="border-t border-gray-200 px-4 py-3">
            {{ $complaints->links() }}
        </div>
    </div>
</x-layout>
