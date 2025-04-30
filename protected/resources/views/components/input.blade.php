@props(['name', 'type' => 'text', 'label' => null, 'error' => null])

<div>
    @if ($label)
        <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
            {{ $label }}
        </label>
    @endif

    <input
        type="{{ $type }}"
        name="{{ $name }}"
        id="{{ $name }}"
        {{ $attributes->merge(['class' => 'mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white sm:text-sm']) }}
    >

    @if ($error)
        <p class="mt-2 text-sm text-red-600 dark:text-red-400">
            {{ $error }}
        </p>
    @endif
</div>
