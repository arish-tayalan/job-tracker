<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $application->position }} at {{ $application->company }}
            </h2>
            <a href="{{ route('job-applications.index') }}" class="text-sm text-gray-600 hover:underline">&larr; Back to list</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
                <div class="rounded-md bg-green-50 p-4 text-sm text-green-800">{{ session('status') }}</div>
            @endif

            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <dl class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wider text-gray-500">Status</dt>
                        <dd class="mt-1">
                            <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium {{ $application->status->badgeClasses() }}">
                                {{ $application->status->label() }}
                            </span>
                        </dd>
                    </div>

                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wider text-gray-500">Date applied</dt>
                        <dd class="mt-1 text-gray-900">
                            @if ($application->applied_on)
                                {{ $application->applied_on->format('d M Y') }}
                                <span class="text-gray-500">({{ $application->applied_on->diffForHumans() }})</span>
                            @else
                                —
                            @endif
                        </dd>
                    </div>

                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wider text-gray-500">Follow up on</dt>
                        <dd class="mt-1 text-gray-900">
                            @if ($application->follow_up_on)
                                {{ $application->follow_up_on->format('d M Y') }}
                                @if ($application->followUpIsDue())
                                    <span class="ml-1 rounded bg-orange-100 px-1.5 py-0.5 text-xs font-medium text-orange-800">Due</span>
                                @endif
                            @else
                                —
                            @endif
                        </dd>
                    </div>

                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wider text-gray-500">Location</dt>
                        <dd class="mt-1 text-gray-900">{{ $application->location ?? '—' }}</dd>
                    </div>

                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wider text-gray-500">Job posting</dt>
                        <dd class="mt-1">
                            @if ($application->job_url)
                                <a href="{{ $application->job_url }}" target="_blank" rel="noopener noreferrer"
                                    class="text-indigo-600 hover:underline break-all">{{ $application->job_url }}</a>
                            @else
                                <span class="text-gray-900">—</span>
                            @endif
                        </dd>
                    </div>

                    <div class="sm:col-span-2">
                        <dt class="text-xs font-semibold uppercase tracking-wider text-gray-500">Notes</dt>
                        {{-- whitespace-pre-line keeps the line breaks you typed in the notes box --}}
                        <dd class="mt-1 text-gray-900 whitespace-pre-line">{{ $application->notes ?? '—' }}</dd>
                    </div>
                </dl>

                <div class="mt-8 flex items-center justify-end gap-4 border-t border-gray-100 pt-6">
                    <form method="POST" action="{{ route('job-applications.destroy', $application) }}"
                        onsubmit="return confirm(@js('Delete the '.$application->company.' application?'));">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-sm text-red-600 hover:underline">Delete</button>
                    </form>
                    <a href="{{ route('job-applications.edit', $application) }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-800 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                        Edit
                    </a>
                </div>
            </div>

            {{-- Interviews: $application->interviews runs the hasMany relationship (soonest first). --}}
            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <h3 class="text-lg font-semibold text-gray-900">Interviews</h3>

                @forelse ($application->interviews as $interview)
                    <div class="mt-4 flex items-start justify-between gap-4 border-b border-gray-100 pb-4 last:border-0">
                        <div>
                            <div class="font-medium text-gray-900">
                                {{ $interview->scheduled_at->format('D, d M Y \a\t H:i') }}
                                <span class="ml-2 inline-flex rounded-full bg-indigo-100 px-2 py-0.5 text-xs font-medium text-indigo-700">{{ $interview->type->label() }}</span>
                                @if ($interview->scheduled_at->isFuture())
                                    <span class="ml-1 text-xs text-green-700">upcoming</span>
                                @endif
                            </div>
                            @if ($interview->notes)
                                <p class="mt-1 text-sm text-gray-600 whitespace-pre-line">{{ $interview->notes }}</p>
                            @endif
                        </div>
                        <form method="POST" action="{{ route('interviews.destroy', $interview) }}"
                            onsubmit="return confirm('Delete this interview?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-sm text-red-600 hover:underline">Delete</button>
                        </form>
                    </div>
                @empty
                    <p class="mt-3 text-sm text-gray-500">No interviews logged yet.</p>
                @endforelse

                <form method="POST" action="{{ route('interviews.store', $application) }}" class="mt-6 border-t border-gray-100 pt-6">
                    @csrf
                    <h4 class="text-sm font-semibold text-gray-700">Add an interview</h4>
                    <div class="mt-3 grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <x-input-label for="scheduled_at" value="Date and time" />
                            <x-text-input id="scheduled_at" name="scheduled_at" type="datetime-local" class="mt-1 block w-full"
                                :value="old('scheduled_at')" required />
                            <x-input-error :messages="$errors->get('scheduled_at')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="type" value="Type" />
                            <select id="type" name="type" required
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                @foreach (\App\Enums\InterviewType::cases() as $type)
                                    <option value="{{ $type->value }}" @selected(old('type') === $type->value)>{{ $type->label() }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('type')" class="mt-2" />
                        </div>
                        <div class="sm:col-span-2">
                            <x-input-label for="interview_notes" value="Notes (interviewer, topics, how it went)" />
                            <textarea id="interview_notes" name="notes" rows="3"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('notes') }}</textarea>
                            <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                        </div>
                    </div>
                    <div class="mt-4 flex justify-end">
                        <x-primary-button>Add interview</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
