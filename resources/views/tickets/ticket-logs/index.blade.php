@php
$extraColumns = function ($log) {
    $details = is_string($log->details) ? json_decode($log->details, true) : $log->details;

    if (!is_array($details)) {
        $details = ['raw' => $log->details];
    }

    $detailsHtml = collect($details)->map(function($value, $key) {
        if (is_array($value)) {
            $value = implode(', ', $value);
        }
        return '<div><strong>' . ucfirst($key) . ':</strong> ' . e($value ?: '—') . '</div>';
    })->implode('');

    return new \Illuminate\Support\HtmlString(
        '<td class="px-6 py-4 text-sm text-gray-900">' . $log->created_at->format('Y-m-d H:i') . '</td>' .
        '<td class="px-6 py-4 text-sm text-gray-900">' . e($log->user->name ?? 'N/A') . '</td>' .
        '<td class="px-6 py-4 text-sm text-gray-900">' . e(ucfirst($log->action)) . '</td>' .
        '<td class="px-6 py-4 text-sm text-gray-900">' . $detailsHtml . '</td>'
    );
};

    $extraHeaders = '
        <th class="px-6 py-3 text-left">Created At</th>
        <th class="px-6 py-3 text-left">User</th>
        <th class="px-6 py-3 text-left">Action</th>
        <th class="px-6 py-3 text-left">Details</th>
    ';
@endphp

<x-index-layout
    title="Ticket Logs"
    routeBase="ticket-logs"
    :items="$logs"
    :filters="$filters"
    :extraHeaders="$extraHeaders"
    :extraColumns="$extraColumns"
    :showCreate="false"
    :readOnly="true"
/>
