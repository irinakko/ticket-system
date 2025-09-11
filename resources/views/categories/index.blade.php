@php
    $filters = [
        'name' => $name,
    ];
@endphp
<x-index-layout
    title="Categories"
    routeBase="categories"
    :items="$categories"
    :filters="$filters"
/>

