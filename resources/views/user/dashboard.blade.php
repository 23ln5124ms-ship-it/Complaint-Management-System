<x-layout title="Dashboard">
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
    </x-slot>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3 mb-8">
        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
            <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide">My Complaints</h2>
            <p class="mt-4 text-3xl font-bold text-gray-900">{{ auth()->user()->complaints()->count() }}</p>
            <p class="mt-2 text-sm text-gray-500">Total complaints submitted by your account.</p>
        </div>

        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
            <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Open Tickets</h2>
            <p class="mt-4 text-3xl font-bold text-gray-900">{{ auth()->user()->complaints()->whereIn('status', ['pending', 'in_progress', 'open'])->count() }}</p>
            <p class="mt-2 text-sm text-gray-500">Complaints that still need your attention.</p>
        </div>

        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
            <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Resolved</h2>
            <p class="mt-4 text-3xl font-bold text-gray-900">{{ auth()->user()->complaints()->where('status', 'resolved')->count() }}</p>
            <p class="mt-2 text-sm text-gray-500">Complaints marked as resolved.</p>
        </div>
    </div>

    <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
        <div class="flex items-center justify-between gap-4 mb-6">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">Quick Actions</h2>
                <p class="text-sm text-gray-500">Jump to the pages you use most.</p>
            </div>
            <a href="{{ route('user.complaints.create') }}" class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500">
                Submit new complaint
            </a>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <a href="{{ route('user.complaints.index') }}" class="block rounded-xl border border-gray-200 bg-slate-50 p-5 text-sm font-medium text-gray-900 hover:border-indigo-300 hover:bg-white">
                View my complaints
            </a>
            <a href="{{ route('user.complaints.create') }}" class="block rounded-xl border border-gray-200 bg-slate-50 p-5 text-sm font-medium text-gray-900 hover:border-indigo-300 hover:bg-white">
                Create a new complaint
            </a>
        </div>
    </div>
</x-layout>
