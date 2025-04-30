@props(['title' => null])

<div {{ $attributes->merge(['class' => 'bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg']) }}>
    @if ($title)
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">
                {{ $title }}
            </h3>
        </div>
    @endif

    <div class="p-6">
        {{ $slot }}
    </div>
</div>
