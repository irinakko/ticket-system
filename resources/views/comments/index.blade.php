<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $ticket->title }}
        </h2>
        <p class="text-gray-600">{{ $ticket->description }}</p>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Flex container for Sidebar + Form --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex flex-col lg:flex-row gap-6 items-start">

                    {{-- Sidebar --}}
                    <div class="w-full lg:w-1/3 border-r lg:pr-4">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Properties</h3>

                        <div class="mb-4">
                            <strong>Status:</strong>
                            <p>{{ $ticket->status->name }}</p>
                        </div>

                        <div class="mb-4">
                            <strong>Categories:</strong>
                            <ul class="list-disc list-inside text-sm text-gray-700">
                                @foreach ($ticket->categories as $category)
                                    <li>{{ $category->name }}</li>
                                @endforeach
                            </ul>
                        </div>

                        <div class="mb-4">
                            <strong>Labels:</strong>
                            <ul class="list-disc list-inside text-sm text-gray-700">
                                @foreach ($ticket->labels as $label)
                                    <li>{{ $label->name }}</li>
                                @endforeach
                            </ul>
                        </div>

                        <div class="mb-4">
                            <strong>Priority:</strong>
                            <p>{{ $ticket->priority->name }}</p>
                        </div>

                        <div class="mb-4">
                            <strong>Assignee:</strong>
                            <p>{{ $ticket->user->name }}</p>
                        </div>
                    </div>

                    {{-- Main Form Area --}}
                    <div class="w-full lg:w-2/3">
                        <form method="POST" action="{{ route('tickets.update', $ticket) }}"> 
                            @csrf
                            @method('PUT')

                            {{-- Title --}}
                            <div class="mb-4">
                                <label for="name" class="block text-sm font-medium text-gray-700">Title:</label>
                                <input type="text" name="name" id="name" value="{{ old('name', $ticket->title) }}" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            </div>

                            {{-- Description --}}
                            <div class="mb-4">
                                <label for="description" class="block text-sm font-medium text-gray-700">Description:</label>
                                <textarea name="description" id="description" rows="4" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('description', $ticket->description) }}</textarea>
                            </div>

                            <button type="submit" class="mt-4 inline-flex items-center px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700">
                                Save
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Comment Section --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-8 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Comments</h3>

                @foreach($ticket->comments as $comment)
                    <div class="border-b border-gray-200 py-2">
                        <p class="text-sm text-gray-700">{{ $comment->body }}</p>
                        <p class="text-xs text-gray-500">— {{ $comment->user->name }} on {{ $comment->created_at->format('M d, Y H:i') }}</p>
                    </div>
                @endforeach

                {{-- Add New Comment --}}
                <form method="POST" action="{{ route('comments.store', $ticket->id) }}" class="mt-4">
                    @csrf
                    <textarea name="body" rows="3" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" placeholder="Write a comment..."></textarea>
                    <button type="submit" class="mt-2 inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-500">
                        Add Comment
                    </button>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
