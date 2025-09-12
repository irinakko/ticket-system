@props([
    'title',
    'routeBase',
    'items',
    'extraHeaders' => '',
    'extraColumns' => '',
    'clickableRows' => false,
    'filters' => [],
])
@php
    use Illuminate\Support\Str;
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $title }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- Add new item button --}}
                    <a href="{{ route($routeBase . '.create') }}"
                       class="inline-flex items-center px-4 py-2 mb-4 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                        Add new {{ Str::singular($title) }}
                    </a>

                    {{-- Filters Form --}}
                    <form method="GET" action="{{ route($routeBase . '.index') }}" class="mb-6 flex flex-wrap gap-4">
                        @foreach ($filters as $filterName => $options)
                            <div class="flex flex-col">
                                <label class="text-sm font-medium mb-1 capitalize" for="filter-{{ $filterName }}">
                                    {{ ucfirst($filterName) }}
                                </label>
                                <select
                                    id="filter-{{ $filterName }}"
                                    name="filters[{{ $filterName }}][]"
                                    multiple
                                    class="tom-select"
                                >
                                    @foreach ($options as $option)
                                        <option value="{{ $option->id ?? $option }}"
                                            @if (request()->input("filters.$filterName") && in_array($option->id ?? $option, request()->input("filters.$filterName")))
                                                selected
                                            @endif
                                        >
                                            {{ $option->name ?? $option }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endforeach
                    </form>

                    {{-- Table --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left">Name</th>
                                    {!! $extraHeaders !!}
                                    <th class="px-6 py-3 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-700 divide-y divide-gray-200">
                                @foreach ($items as $item)
                                    <tr
                                        @if ($clickableRows)
                                            onclick="window.location='{{ route($routeBase . '.show', Str::slug($item->title ?? $item->name)) }}'"
                                            class="cursor-pointer hover:bg-gray-100"
                                            tabindex="0"
                                            onkeydown="if(event.key==='Enter'){window.location='{{ route($routeBase . '.show', Str::slug($item->title ?? $item->name)) }}'}"
                                        @endif
                                    >
                                        <td class="px-6 py-4">{{ $item->name }}</td>

                                        {{-- Extra Columns --}}
                                        @if (isset($extraColumns) && is_callable($extraColumns))
                                            {!! $extraColumns($item) !!}
                                        @endif

                                        {{-- Actions --}}
                                        <td class="px-6 py-4 text-right space-x-2" onclick="event.stopPropagation()" onkeydown="event.stopPropagation();">
                                            <a href="{{ route($routeBase . '.edit', $item) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                            <form method="POST" action="{{ route($routeBase . '.destroy', $item) }}" class="inline-block" onsubmit="event.stopPropagation(); return confirm('Are you sure?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-800 font-medium">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.tom-select').forEach(function(select) {
            new TomSelect(select, {
                plugins: ['remove_button'],
                create: false,
                allowEmptyOption: true,
                closeAfterSelect: false,
                placeholder: 'Select...',
            });
        });
        function onFilterChange() {
        const filters = {};

        // Collect all selected options for each filter
        document.querySelectorAll('select[name^="filters"]').forEach(select => {
            const name = select.name.match(/filters\[(.+?)\]/)[1];
            const selectedOptions = Array.from(select.selectedOptions).map(o => o.value);
            if (selectedOptions.length) {
                filters[name] = selectedOptions;
            }
        });

        // Build query string with filters
        const params = new URLSearchParams();

        for (const [key, values] of Object.entries(filters)) {
            values.forEach(value => params.append(`filters[${key}][]`, value));
        }

        // Reload page with new filters
        const baseUrl = window.location.origin + window.location.pathname;
        window.location.href = baseUrl + '?' + params.toString();
    }

    // Attach change event to all filter selects
    document.querySelectorAll('.tom-select').forEach(select => {
        select.addEventListener('change', onFilterChange);
    });
});
</script>