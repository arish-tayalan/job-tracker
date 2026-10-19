{{--
    A table header that links to the same page sorted by $column.
    Clicking the column that is already sorted flips ascending/descending.
    $query carries the other filters (status, search) so they survive the click.
--}}
@props(['column', 'label', 'sort', 'direction', 'query' => []])

@php
    $active = $sort === $column;
    $nextDirection = $active && $direction === 'asc' ? 'desc' : 'asc';
@endphp

<th class="px-4 py-3" @if ($active) aria-sort="{{ $direction === 'asc' ? 'ascending' : 'descending' }}" @endif>
    <a href="{{ route('job-applications.index', array_merge($query, ['sort' => $column, 'dir' => $nextDirection])) }}"
        class="inline-flex items-center gap-1 uppercase hover:text-gray-900">
        {{ $label }}
        @if ($active)
            <span aria-hidden="true">{{ $direction === 'asc' ? '▲' : '▼' }}</span>
        @endif
    </a>
</th>
