<x-layouts::app.sidebar :title="$title ?? null">
    <flux:main class="pb-20 lg:pb-0">
        {{ $slot }}
    </flux:main>
</x-layouts::app.sidebar>
