<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Labels') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <a href="{{ route('labels.create') }}" class="mb-4 inline-block">Add new label</a>
                     <table class="min-w-full bg-white border border-gray-200 shadow-md rounded-lg overflow-hidden">
    <thead class="bg-gray-100 text-gray-700 uppercase text-sm font-semibold">
        <tr>
            <th class="px-6 py-3 text-left">Name</th>
            <th class="px-6 py-3 text-right">Actions</th>
        </tr>
    </thead>
    <tbody class="text-gray-700 divide-y divide-gray-200">
        @foreach($labels as $label)
            <tr class="hover:bg-gray-50 transition duration-150">
                <td class="px-6 py-4">{{ $label->name }}</td>
                <td class="px-6 py-4 text-right space-x-2">
                    <a href="{{ route('labels.edit', $label) }}" class="text-indigo-600 hover:text-indigo-900 font-medium">Edit</a>

                    <form method="POST" action="{{ route('labels.destroy', $label) }}" class="inline-block" onsubmit="return confirm('Are you sure?')">
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
</x-app-layout>
