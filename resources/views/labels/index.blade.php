@php
    $filters = [
        'name' => $name,
    ];
@endphp
<x-index-layout
    title="Labels"
    routeBase="labels"
    :items="$labels"
    :filters="$filters" />

