<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Ticket Create') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if ($errors->any())
    <div class="alert alert-danger">
        <strong>There were some problems with your input:</strong>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
                    <!-- ✅ SINGLE FORM START -->
                    <form method="POST" action="{{ route('tickets.store') }}" enctype="multipart/form-data">
                        @csrf

                        {{-- Name --}}
                        <div class="mb-4">
                            <label for="name" class="block font-medium text-sm text-gray-700">Name:</label>
                            <input type="text" name="title" id="name" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        </div>

                        {{-- Description --}}
                        <div class="mb-4">
                            <label for="description" class="block font-medium text-sm text-gray-700">Description:</label>
                            <textarea name="description" id="description" rows="4" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"></textarea>
                        </div>

                        {{-- Attachments (Drag and Drop) --}}
                        <div 
                            id="dropzone"
                            class="mb-4 w-full p-6 border-2 border-dashed border-gray-300 rounded-md cursor-pointer text-center transition-all duration-200"
                            onclick="document.getElementById('fileInput').click();"
                            ondragover="handleDragOver(event)"
                            ondragleave="handleDragLeave(event)"
                            ondrop="handleDrop(event)"
                        >
                            <p class="text-gray-500">Drag & drop files here or <span class="text-blue-600 underline">click to browse</span></p>

                            <!-- File names will appear here -->
                            <ul id="fileList" class="mt-3 text-sm text-gray-700 space-y-1"></ul>

                            <!-- Hidden file input -->
                            <input
                                type="file"
                                id="fileInput"
                                name="attachments[]"
                                multiple
                                class="hidden"
                                onchange="handleFileSelect(this.files)"
                            >
                        </div>

                        {{-- Categories --}}
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Categories:</label>
                            <div class="flex flex-wrap gap-4">
                                @foreach ($categories as $category)
                                    <div class="flex items-center space-x-2">
                                        <input
                                            type="checkbox"
                                            name="category_ids[]"
                                            id="category_{{ $category->id }}"
                                            value="{{ $category->id }}"
                                            class="h-4 w-4 text-indigo-600 border-gray-300 rounded"
                                        >
                                        <label for="category_{{ $category->id }}" class="text-sm text-gray-700">
                                            {{ $category->name }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Labels --}}
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Labels:</label>
                            <div class="flex flex-wrap gap-4">
                                @foreach ($labels as $label)
                                    <div class="flex items-center space-x-2">
                                        <input
                                            type="checkbox"
                                            name="label_ids[]"
                                            id="label_{{ $label->id }}"
                                            value="{{ $label->id }}"
                                            class="h-4 w-4 text-indigo-600 border-gray-300 rounded"
                                        >
                                        <label for="label_{{ $label->id }}" class="text-sm text-gray-700">
                                            {{ $label->name }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Priority --}}
                        <div class="mb-4">
                            <label for="priority_id" class="block font-medium text-sm text-gray-700">Priority:</label>
                            <select name="priority_id" id="priority_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                @foreach ($priorities as $priority)
                                    <option value="{{ $priority->id }}">{{ $priority->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Status --}}
                        <div class="mb-4">
                            <label for="status_id" class="block font-medium text-sm text-gray-700">Status:</label>
                            <select name="status_id" id="status_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                @foreach ($statuses as $status)
                                    <option value="{{ $status->id }}">{{ $status->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Assignee --}}
                        <div class="mb-4">
                            <label for="assignee_id" class="block font-medium text-sm text-gray-700">Assignee:</label>
                            <select name="assignee_id" id="assignee_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Submit Button --}}
                        <div>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Save
                            </button>
                        </div>

                    </form>
                    <!-- ✅ SINGLE FORM END -->

                </div>
            </div>
        </div>
    </div>

    {{-- Drag & Drop Script --}}
    <script>
        const dropzone = document.getElementById('dropzone');
        const fileInput = document.getElementById('fileInput');
        const fileList = document.getElementById('fileList');

        function handleDragOver(e) {
            e.preventDefault();
            dropzone.classList.add('border-blue-500', 'bg-blue-50');
        }

        function handleDragLeave(e) {
            e.preventDefault();
            dropzone.classList.remove('border-blue-500', 'bg-blue-50');
        }

        function handleDrop(e) {
            e.preventDefault();
            dropzone.classList.remove('border-blue-500', 'bg-blue-50');

            const files = e.dataTransfer.files;
            fileInput.files = files;
            displayFileNames(files);
        }

        function handleFileSelect(files) {
            displayFileNames(files);
        }

        function displayFileNames(files) {
            fileList.innerHTML = '';
            for (let i = 0; i < files.length; i++) {
                const li = document.createElement('li');
                li.textContent = files[i].name;
                fileList.appendChild(li);
            }
        }
    </script>
</x-app-layout>
