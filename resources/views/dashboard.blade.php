<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="flex flex-col md:flex-row gap-8">
                    <div class="summary-box flex-1">
    <h3 class="font-bold mb-2">Tickets Summary</h3>
   <ul>
     @foreach($ticketCounts as $status => $count)
    <li class="flex items-center space-x-2 mb-2">
        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-orange-100 text-red-600">
            <x-icon name="ticket" class="w-4 h-4" />
        </span>
        <span>{{ $status }}: {{ $count }}</span>
    </li>
@endforeach
    </ul>
</div>
                    <div class="summary-box flex-1">
    <h3 class="font-bold mb-2">Users Summary</h3>
     <ul>
     @foreach($userCounts as $role => $count)
    <li class="flex items-center space-x-2 mb-2">
        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-blue-100 text-black-600">
            <x-icon name="user" class="w-4 h-4" />
        </span>
        <span>{{ $role }}: {{ $count }}</span>
    </li>
@endforeach
    </ul>
</div>
            </div>
        </div>
    </div>
</x-app-layout>
