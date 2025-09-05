<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
               <div class="ticket-summary-box">
    <h3>Tickets Summary</h3>
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
            </div>
        </div>
    </div>
</x-app-layout>
