
{{-- Opção 4: Header com separador visual --}}
<x-filament-widgets::widget>
<div class="w-full text-center pb-6  pt-2 border-b border-gray-200 dark:border-gray-700 mb-6">
    <div class="flex items-center justify-center space-x-3">
        <span class="text-4xl">{{ $icon }}</span>
        <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100">
            {{ $title }}
        </h2>
    </div>
</div>
</x-filament-widgets::widget>