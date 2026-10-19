<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
            <a href="{{ route('job-applications.create') }}"
                class="inline-flex items-center px-4 py-2 bg-gray-800 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                + Add application
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            @if ($total === 0)
                <div class="bg-white p-10 text-center text-gray-500 shadow-sm sm:rounded-lg">
                    Nothing to show yet.
                    <a href="{{ route('job-applications.create') }}" class="text-indigo-600 hover:underline">Add your first application</a>
                    and your stats will appear here.
                </div>
            @else
                {{-- Headline numbers --}}
                <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
                    <div class="bg-white p-5 shadow-sm sm:rounded-lg">
                        <div class="text-sm text-gray-500">Total applications</div>
                        <div class="mt-1 text-3xl font-bold text-gray-900">{{ $total }}</div>
                    </div>
                    <div class="bg-white p-5 shadow-sm sm:rounded-lg">
                        <div class="text-sm text-gray-500">Still active</div>
                        <div class="mt-1 text-3xl font-bold text-blue-600">{{ $active }}</div>
                        <div class="mt-1 text-xs text-gray-400">Applied or interviewing</div>
                    </div>
                    <div class="bg-white p-5 shadow-sm sm:rounded-lg">
                        <div class="text-sm text-gray-500">Interview rate</div>
                        <div class="mt-1 text-3xl font-bold text-amber-600">{{ $interviewRate === null ? '—' : $interviewRate.'%' }}</div>
                        <div class="mt-1 text-xs text-gray-400">Of submitted applications</div>
                    </div>
                    <div class="bg-white p-5 shadow-sm sm:rounded-lg">
                        <div class="text-sm text-gray-500">Offers</div>
                        <div class="mt-1 text-3xl font-bold text-green-600">{{ $offers }}</div>
                    </div>
                </div>

                {{-- Charts --}}
                <div class="grid gap-6 lg:grid-cols-3">
                    <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                        <h3 class="font-semibold text-gray-900">By status</h3>
                        <div class="mt-4"><canvas id="status-chart" aria-label="Applications by status"></canvas></div>
                    </div>
                    <div class="bg-white p-6 shadow-sm sm:rounded-lg lg:col-span-2">
                        <h3 class="font-semibold text-gray-900">Applications per week</h3>
                        <div class="mt-4"><canvas id="weekly-chart" aria-label="Applications per week"></canvas></div>
                    </div>
                </div>

                {{-- To-do style lists --}}
                <div class="grid gap-6 lg:grid-cols-2">
                    <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                        <h3 class="font-semibold text-gray-900">Upcoming interviews</h3>
                        @forelse ($upcomingInterviews as $interview)
                            <a href="{{ route('job-applications.show', $interview->jobApplication) }}"
                                class="mt-3 flex items-center justify-between gap-4 rounded-md border border-gray-100 p-3 hover:bg-gray-50">
                                <div>
                                    <div class="font-medium text-gray-900">{{ $interview->jobApplication->company }}</div>
                                    <div class="text-sm text-gray-500">{{ $interview->jobApplication->position }}</div>
                                </div>
                                <div class="text-right text-sm">
                                    <div class="text-gray-900">{{ $interview->scheduled_at->format('D, d M \a\t H:i') }}</div>
                                    <div class="text-indigo-600">{{ $interview->type->label() }}</div>
                                </div>
                            </a>
                        @empty
                            <p class="mt-3 text-sm text-gray-500">No interviews coming up. Add one from an application's page.</p>
                        @endforelse
                    </div>

                    <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                        <h3 class="font-semibold text-gray-900">Follow-ups due</h3>
                        @forelse ($followUps as $application)
                            <a href="{{ route('job-applications.show', $application) }}"
                                class="mt-3 flex items-center justify-between gap-4 rounded-md border border-gray-100 p-3 hover:bg-gray-50">
                                <div>
                                    <div class="font-medium text-gray-900">{{ $application->company }}</div>
                                    <div class="text-sm text-gray-500">{{ $application->position }}</div>
                                </div>
                                <span class="rounded bg-orange-100 px-2 py-0.5 text-xs font-medium text-orange-800">
                                    {{ $application->follow_up_on->diffForHumans() }}
                                </span>
                            </a>
                        @empty
                            <p class="mt-3 text-sm text-gray-500">Nothing to chase right now.</p>
                        @endforelse
                    </div>
                </div>

                {{-- The numbers for dashboard.js to draw. JSON_HEX_TAG stops a stray "</script>" breaking out. --}}
                <script id="dashboard-data" type="application/json">@json($chartData, JSON_HEX_TAG)</script>
                @vite('resources/js/dashboard.js')
            @endif
        </div>
    </div>
</x-app-layout>
