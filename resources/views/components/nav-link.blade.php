@props(['active' => false])

<a {{ $attributes }}
   class="{{ $active
        ? 'bg-white/20 text-white'
        : 'text-white/80 hover:bg-white/10 hover:text-white'
   }} rounded-lg px-3 py-2 text-sm font-medium transition-colors">
    {{ $slot }}
</a>
