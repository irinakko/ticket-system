@php
    $extraColumns = function($item) {
        return new \Illuminate\Support\HtmlString('
           <td class="px-6 py-4">' . ($item->priority->name ?? '-') . '</td>' .
            '<td class="px-6 py-4">' . ($item->status->name ?? '-') . '</td>' .
            '<td class="px-6 py-4">' . implode(", ", $item->categories->pluck("name")->toArray()) . '</td>' .
            '<td class="px-6 py-4">' . implode(", ", $item->labels->pluck("name")->toArray()) . '</td>' .
            '<td class="px-6 py-4">' . ($item->user->name ?? '-') . '</td>'
        );
    };
    $filters = [
        'priority' => $priorities,
        'status' => $statuses,
        'categories' => $categories,
        'labels' => $labels,
        'assignee' => $assignees,
    ];
@endphp

<x-index-layout
    title="Tickets"
    routeBase="tickets"
    :items="$tickets"
    :clickableRows="true"
    :extraHeaders="'
        <th class=\'px-6 py-3 text-left\'>Priority</th>
        <th class=\'px-6 py-3 text-left\'>Status</th>
        <th class=\'px-6 py-3 text-left\'>Categories</th>
        <th class=\'px-6 py-3 text-left\'>Labels</th>
        <th class=\'px-6 py-3 text-left\'>Assignee</th>
    '"
    :extraColumns="$extraColumns"
    :filters="$filters"
/>


