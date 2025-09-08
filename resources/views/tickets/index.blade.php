<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center space-x-2">
        <span>{{ __('Tickets') }}</span>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <a href="{{ route('tickets.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 mb-4">Add new ticket</a>
                     <table class="min-w-full bg-white border border-gray-200 shadow-md rounded-lg overflow-hidden">
    <thead class="bg-gray-100 text-gray-700 uppercase text-sm font-semibold">
        
        <tr>
            <th class="px-6 py-3 text-left">Title</th>
            <th class="px-6 py-3 text-left">Label</th>
            <th class="px-6 py-3 text-left">Priority</th>
            <th class="px-6 py-3 text-left">Category</th>
            <th class="px-6 py-3 text-left">Status</th>
            <th class="px-6 py-3 text-left">Assignee</th>
            <th class="px-6 py-3 text-right">Actions</th>
        </tr>
    </thead>
    <tbody class="text-gray-700 divide-y divide-gray-200">
        @foreach($tickets as $ticket)
    <tr
    tabindex="0"
    class="hover:bg-gray-50 transition duration-150 cursor-pointer"
    onclick="window.location='{{ route('tickets.show', \Illuminate\Support\Str::slug($ticket->title)) }}'"
    onkeydown="if(event.key === 'Enter' || event.key === ' ') { window.location='{{ route('tickets.show', \Illuminate\Support\Str::slug($ticket->title)) }}'; event.preventDefault(); }">
        <td class="px-6 py-4">{{ $ticket->title }}</td>

        <td class="px-6 py-4">
            @if ($ticket->labels->isNotEmpty())
                <ul>
                    @foreach ($ticket->labels as $label)
                        <li>{{ $label->name }}</li>
                    @endforeach
                </ul>
            @else
                <span>No labels assigned</span>
            @endif
        </td>
  <td class="px-6 py-4">
    @php
    $priorityEnum = $ticket->getPriorityLevelEnum();
@endphp
    @if ($priorityEnum)
        <span
            class="inline-block px-3 py-1 rounded-full text-sm font-medium text-black"
            style="background-color: {{ $priorityEnum->color() }};"
        >
            {{ ucfirst($priorityEnum->value) }}
        </span>
    @else
        <span class="text-gray-500">Unassigned</span>
    @endif
</td>
        <td class="px-6 py-4">
            @if ($ticket->categories->isNotEmpty())
                <ul>
                    @foreach ($ticket->categories as $category)
                        <li>{{ $category->name }}</li>
                    @endforeach
                </ul>
            @else
                <span>No categories assigned</span>
            @endif
        </td>

        <td class="px-6 py-4">{{ $ticket->status->name }}</td>
        <td class="px-6 py-4">{{ $ticket->user->name ?? 'Unassigned' }}</td>

        <td class="px-6 py-4 text-right space-x-2">
            <a href="{{ route('tickets.edit', $ticket) }}" class="text-indigo-600 hover:text-indigo-900 font-medium">Edit</a>

            <form method="POST" action="{{ route('tickets.destroy', $ticket) }}" class="inline-block" onsubmit="return confirm('Are you sure?')">
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
