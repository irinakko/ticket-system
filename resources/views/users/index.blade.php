@php
$extraColumns = function($item) {
    return new \Illuminate\Support\HtmlString(
        '<td class="px-6 py-4">' . ($item->email ?? '-') . '</td>'
    );
};
    $filters = [
        'name' => $name,
        'email' => $email,
    ];
@endphp
<x-index-layout
    title="Users"
    routeBase="users"
    :items="$users"
    :filters="$filters"
    :extraColumns="$extraColumns"
    :extraHeaders="'
        <th class=\'px-6 py-3 text-left\'>Email</th>
    '"
/>



