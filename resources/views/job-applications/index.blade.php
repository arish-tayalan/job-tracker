<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">My applications</h2>
            <a href="{{ route('job-applications.create') }}"
                class="inline-flex items-center px-4 py-2 bg-gray-800 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                + Add application
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
                <div class="rounded-md bg-green-50 p-4 text-sm text-green-800">{{ session('status') }}</div>
            @endif

            {{-- Search form: GET puts the word in the URL (?search=...), which the controller reads. --}}
            <form method="GET" action="{{ route('job-applications.index') }}" class="flex flex-wrap items-center gap-2">
                @if ($currentStatus)
                    <input type="hidden" name="status" value="{{ $currentStatus->value }}">
                @endif
                @if (request()->has('sort'))
                    <input type="hidden" name="sort" value="{{ $sort }}">
                    <input type="hidden" name="dir" value="{{ $direction }}">
                @endif
                <x-text-input name="search" type="text" :value="$search"
                    placeholder="Search company or position..." class="w-full sm:w-80" />
                <x-primary-button>Search</x-primary-button>
                @if ($search !== '')
                    <a href="{{ route('job-applications.index') }}" class="text-sm text-gray-600 hover:underline">Clear</a>
                @endif
            </form>

            {{-- Status filter tabs: each is a link that sets ?status=... --}}
            <nav class="flex flex-wrap gap-2" aria-label="Filter by status">
                @php($tabClasses = 'rounded-full px-3 py-1 text-sm font-medium border')
                @php($active = 'bg-gray-800 text-white border-gray-800')
                @php($inactive = 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50')

                <a href="{{ route('job-applications.index') }}"
                    class="{{ $tabClasses }} {{ $currentStatus === null ? $active : $inactive }}">
                    All <span class="opacity-70">({{ $counts->sum() }})</span>
                </a>

                @foreach (\App\Enums\ApplicationStatus::cases() as $status)
                    <a href="{{ route('job-applications.index', ['status' => $status->value]) }}"
                        class="{{ $tabClasses }} {{ $currentStatus === $status ? $active : $inactive }}">
                        {{ $status->label() }} <span class="opacity-70">({{ $counts[$status->value] ?? 0 }})</span>
                    </a>
                @endforeach
            </nav>

            <div class="bg-white shadow-sm sm:rounded-lg overflow-x-auto">
                @if ($applications->isEmpty())
                    <div class="p-10 text-center text-gray-500">
                        @if ($currentStatus)
                            No applications with status "{{ $currentStatus->label() }}".
                        @else
                            No applications yet.
                            <a href="{{ route('job-applications.create') }}" class="text-indigo-600 hover:underline">Add your first one</a>.
                        @endif
                    </div>
                @else
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            <tr>
                                @php($filters = array_filter(['status' => $currentStatus?->value, 'search' => $search]))
                                <x-sortable-th column="company" label="Company / Position" :sort="$sort" :direction="$direction" :query="$filters" />
                                <x-sortable-th column="status" label="Status" :sort="$sort" :direction="$direction" :query="$filters" />
                                <x-sortable-th column="location" label="Location" :sort="$sort" :direction="$direction" :query="$filters" />
                                <x-sortable-th column="applied_on" label="Applied" :sort="$sort" :direction="$direction" :query="$filters" />
                                <th class="px-4 py-3"><span class="sr-only">Actions</span></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($applications as $application)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3">
                                        <div class="font-medium text-gray-900">
                                            <a href="{{ route('job-applications.show', $application) }}" class="hover:underline">{{ $application->company }}</a>
                                        </div>
                                        <div class="text-gray-500">{{ $application->position }}</div>
                                        @if ($application->followUpIsDue())
                                            <span class="mt-1 inline-flex rounded bg-orange-100 px-1.5 py-0.5 text-xs font-medium text-orange-800">Follow up due</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium {{ $application->status->badgeClasses() }}">
                                            {{ $application->status->label() }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-gray-600">{{ $application->location ?? '—' }}</td>
                                    <td class="px-4 py-3 text-gray-600 whitespace-nowrap">{{ $application->applied_on?->format('d M Y') ?? '—' }}</td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center justify-end gap-3">
                                            <a href="{{ route('job-applications.edit', $application) }}" class="text-indigo-600 hover:underline">Edit</a>
                                            <form method="POST" action="{{ route('job-applications.destroy', $application) }}"
                                                onsubmit="return confirm(@js('Delete the '.$application->company.' application?'));">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:underline">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>

            {{ $applications->links() }}
        </div>
    </div>
</x-app-layout>
