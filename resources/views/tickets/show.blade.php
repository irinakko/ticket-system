<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $ticket->title }}
        </h2>
    </x-slot>

    <div class="py-12 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-12">
        {{-- Main content and sidebar --}}
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 flex flex-col lg:flex-row gap-8">
            
            {{-- Left: Main content --}}
            <div class="flex-1">
                <p class="text-gray-700 mb-4">{{ $ticket->description ?? 'No description provided.' }}</p>

                <a href="{{ route('tickets.index') }}"
                    class="inline-flex items-center px-4 py-2 bg-gray-800 border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 mt-4">
                    Back to Tickets
                </a>
            </div>

            {{-- Right: Sidebar --}}
            <div class="lg:w-1/3 bg-gray-50 border border-gray-200 rounded-lg p-4">
                <h3 class="text-lg font-semibold mb-4">Properties</h3>

                <p><strong>Status:</strong> {{ $ticket->status->name }}</p>
                <p><strong>Priority:</strong> {{ $ticket->priority->name ?? 'N/A' }}</p>
                <p><strong>Assignee:</strong> {{ $ticket->user->name ?? 'Unassigned' }}</p>

                <div class="mt-4">
                    <h4 class="font-semibold">Labels</h4>
                    <ul class="list-disc list-inside">
                        @forelse ($ticket->labels as $label)
                            <li>{{ $label->name }}</li>
                        @empty
                            <li class="text-gray-500">No labels</li>
                        @endforelse
                    </ul>
                </div>

                <div class="mt-4">
                    <h4 class="font-semibold">Categories</h4>
                    <ul class="list-disc list-inside">
                        @forelse ($ticket->categories as $category)
                            <li>{{ $category->name }}</li>
                        @empty
                            <li class="text-gray-500">No categories</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

       {{-- Comments section --}}
<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
    <h3 class="text-lg font-semibold mb-4">Comments</h3>

    {{-- Add Comment Button --}}
    <button onclick="document.getElementById('commentModal').classList.remove('hidden')"
        class="inline-flex items-center px-4 py-2 bg-gray-800 border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 mb-4">
        Add new comment
    </button>

    {{-- Add Comment Modal --}}
    <div id="commentModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white w-full max-w-md rounded-lg p-6 relative">
            <button onclick="document.getElementById('commentModal').classList.add('hidden')"
                    class="absolute top-2 right-2 text-gray-500 hover:text-gray-800">
                ✖
            </button>

            <h2 class="text-lg font-semibold mb-4">Add Comment</h2>

            <form method="POST" action="{{ route('comments.store') }}">
                @csrf
                <input type="hidden" name="ticket_id" value="{{ $ticket->id }}">

                <div class="mb-4">
                    <textarea name="content" rows="4" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="Your comment..."></textarea>
                </div>

                <div class="flex justify-end">
                    <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">
                        Submit
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- List Top-Level Comments --}}
    @forelse ($ticket->comments->where('parent_id', null) as $comment)
        <div class="mb-6 border-b pb-4">
            <p class="text-gray-800"><strong>{{ $comment->user->name ?? 'Anonymous' }}:</strong></p>
            <p class="text-gray-600">{{ $comment->content }}</p>

            <div class="text-sm text-gray-700 mt-2">
                {{-- Edit button --}}
                <button onclick="document.getElementById('editCommentModal-{{ $comment->id }}').classList.remove('hidden')"
                        class="text-blue-600 hover:underline mr-2">
                    Edit
                </button>

                {{-- Delete form --}}
                <form method="POST" action="{{ route('comments.destroy', $comment->id) }}" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="text-red-600 hover:underline"
                            onclick="return confirm('Are you sure you want to delete this comment?');">
                        Delete
                    </button>
                </form>

                {{-- Reply button --}}
                <button onclick="document.getElementById('replyModal-{{ $comment->id }}').classList.remove('hidden')"
                        class="text-indigo-600 hover:underline ml-2">
                    Reply
                </button>

                <p class="text-xs text-gray-400 mt-1">{{ $comment->created_at->diffForHumans() }}</p>
            </div>

            {{-- Edit Comment Modal --}}
            <div id="editCommentModal-{{ $comment->id }}" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center hidden z-50">
                <div class="bg-white w-full max-w-md rounded-lg p-6 relative">
                    <button onclick="document.getElementById('editCommentModal-{{ $comment->id }}').classList.add('hidden')"
                            class="absolute top-2 right-2 text-gray-500 hover:text-gray-800">
                        ✖
                    </button>

                    <h2 class="text-lg font-semibold mb-4">Edit Comment</h2>

                    <form method="POST" action="{{ route('comments.update', $comment->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <textarea name="content" rows="4" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">{{ $comment->content }}</textarea>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit"
                                    class="px-4 py-2 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">
                                Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Reply Modal --}}
            <div id="replyModal-{{ $comment->id }}" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center hidden z-50">
                <div class="bg-white w-full max-w-md rounded-lg p-6 relative">
                    <button onclick="document.getElementById('replyModal-{{ $comment->id }}').classList.add('hidden')"
                            class="absolute top-2 right-2 text-gray-500 hover:text-gray-800">
                        ✖
                    </button>

                    <h2 class="text-lg font-semibold mb-4">Reply to Comment</h2>

                    <form method="POST" action="{{ route('comments.respond', $comment->id) }}">
                        @csrf

                        <div class="mb-4">
                            <textarea name="content" rows="4" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="Your reply..."></textarea>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit"
                                    class="px-4 py-2 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">
                                Submit Reply
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- List Replies --}}
            @foreach ($comment->replies as $reply)
                <div class="ml-6 mt-4 border-l-2 border-gray-200 pl-4">
                    <p class="text-gray-800"><strong>{{ $reply->user->name ?? 'Anonymous' }}:</strong></p>
                    <p class="text-gray-600">{{ $reply->content }}</p>
                    <p class="text-xs text-gray-400">{{ $reply->created_at->diffForHumans() }}</p>
                </div>
            @endforeach
        </div>
    @empty
        <p class="text-gray-500">No comments yet.</p>
    @endforelse
</div>

</x-app-layout>
