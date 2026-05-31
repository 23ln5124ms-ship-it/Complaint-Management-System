<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'ComplainHub') }} — {{ $title ?? 'Dashboard' }}</title>

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    {{-- Tailwind CSS CDN (for quick setup; replace with Vite in production) --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: {
                        primary: {
                            50:  '#eef2ff',
                            100: '#e0e7ff',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                        }
                    }
                }
            }
        }
    </script>

    {{-- Alpine.js --}}
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @stack('styles')
</head>
<body class="h-full font-sans antialiased">

<div class="min-h-full">
    {{-- ── Navigation ──────────────────────────────────────────────────────── --}}
    <nav class="bg-primary-600 shadow-sm" x-data="{ mobileOpen: false }">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 items-center justify-between">
                {{-- Logo --}}
                <div class="flex items-center gap-3">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-white/20">
                        <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <a href="{{ auth()->user()?->isAdmin() ? route('admin.dashboard') : route('user.dashboard') }}"
                       class="text-lg font-semibold text-white">ComplainHub</a>
                </div>

                {{-- Desktop Nav --}}
                <div class="hidden md:flex md:items-center md:gap-2">
                    @auth
                        @if(auth()->user()->isAdmin())
                            <x-nav-link :href="route('admin.dashboard')"     :active="request()->routeIs('admin.dashboard')">Dashboard</x-nav-link>
                            <x-nav-link :href="route('admin.complaints.index')" :active="request()->routeIs('admin.complaints.*')">All Complaints</x-nav-link>
                            <x-nav-link :href="route('admin.categories.index')" :active="request()->routeIs('admin.categories.*')">Categories</x-nav-link>
                            <x-nav-link :href="route('admin.reports.index')"  :active="request()->routeIs('admin.reports.*')">Reports</x-nav-link>
                        @else
                            <x-nav-link :href="route('user.dashboard')"         :active="request()->routeIs('user.dashboard')">Dashboard</x-nav-link>
                            <x-nav-link :href="route('user.complaints.index')"  :active="request()->routeIs('user.complaints.index') || request()->routeIs('user.complaints.show') || request()->routeIs('user.complaints.edit')">My Complaints</x-nav-link>
                            <x-nav-link :href="route('user.complaints.create')" :active="request()->routeIs('user.complaints.create')">Submit Complaint</x-nav-link>
                        @endif

                        {{-- User Menu --}}
                        <div class="relative ml-4" x-data="{ open: false }">
                            <button @click="open = !open"
                                    class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium text-white/90 hover:bg-white/10 hover:text-white">
                                <span class="flex h-7 w-7 items-center justify-center rounded-full bg-white/20 text-xs font-bold">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </span>
                                {{ auth()->user()->name }}
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div x-show="open" @click.away="open = false" x-cloak
                                 class="absolute right-0 mt-1 w-40 rounded-lg bg-white py-1 shadow-lg ring-1 ring-black/5">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                            class="w-full px-4 py-2 text-left text-sm text-red-600 hover:bg-red-50">
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    {{-- ── Page Header ─────────────────────────────────────────────────────── --}}
    @if(isset($header))
        <header class="bg-white border-b border-gray-200">
            <div class="mx-auto max-w-7xl px-4 py-5 sm:px-6 lg:px-8">
                {{ $header }}
            </div>
        </header>
    @endif

    {{-- ── Flash Messages ──────────────────────────────────────────────────── --}}
    @if(session('success'))
        <div class="mx-auto mt-4 max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="rounded-lg bg-green-50 p-4 text-sm text-green-800 border border-green-200">
                ✅ {{ session('success') }}
            </div>
        </div>
    @endif
    @if(session('error'))
        <div class="mx-auto mt-4 max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="rounded-lg bg-red-50 p-4 text-sm text-red-800 border border-red-200">
                ❌ {{ session('error') }}
            </div>
        </div>
    @endif

    {{-- ── Main Content ────────────────────────────────────────────────────── --}}
    <main class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            {{ $slot }}
        </div>
    </main>
</div>

@stack('scripts')
</body>
</html>
