<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Board</h2>
            <a href="{{ route('job-applications.create') }}"
                class="inline-flex items-center px-4 py-2 bg-gray-800 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                + Add application
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
            <p class="mb-4 text-sm text-gray-500">Drag a card to another column, or use the "Move to" menu on the card.</p>
            <div id="board-message" class="mb-4 hidden rounded-md bg-red-50 p-3 text-sm text-red-800" role="alert"></div>

            {{-- One column per status. board.js reads data-status / data-url and does the rest. --}}
            <div id="board" class="flex gap-4 overflow-x-auto pb-4">
                @foreach (\App\Enums\ApplicationStatus::cases() as $status)
                    @php($cards = $columns[$status->value] ?? collect())
                    <section class="w-72 shrink-0 rounded-lg bg-gray-100 p-3" data-column data-status="{{ $status->value }}">
                        <h3 class="mb-3 flex items-center justify-between text-sm font-semibold text-gray-700">
                            <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium {{ $status->badgeClasses() }}">{{ $status->label() }}</span>
                            <span class="text-xs text-gray-500" data-count>{{ $cards->count() }}</span>
                        </h3>

                        <div class="min-h-24 space-y-3" data-dropzone>
                            @foreach ($cards as $application)
                                <article draggable="true"
                                    class="cursor-grab rounded-md border border-gray-200 bg-white p-3 shadow-sm"
                                    data-card
                                    data-url="{{ route('job-applications.status', $application) }}">
                                    <a href="{{ route('job-applications.show', $application) }}" class="font-medium text-gray-900 hover:underline">{{ $application->company }}</a>
                                    <div class="text-sm text-gray-500">{{ $application->position }}</div>

                                    @if ($application->followUpIsDue())
                                        <span class="mt-2 inline-flex rounded bg-orange-100 px-1.5 py-0.5 text-xs font-medium text-orange-800">Follow up due</span>
                                    @endif

                                    <label class="mt-3 block text-xs text-gray-400">
                                        <span class="sr-only">Move {{ $application->company }} to</span>
                                        <select data-move class="mt-1 block w-full rounded border-gray-200 py-1 text-xs text-gray-600">
                                            @foreach (\App\Enums\ApplicationStatus::cases() as $option)
                                                <option value="{{ $option->value }}" @selected($option === $status)>Move to: {{ $option->label() }}</option>
                                            @endforeach
                                        </select>
                                    </label>
                                </article>
                            @endforeach
                        </div>

                        <p class="py-6 text-center text-xs text-gray-400 {{ $cards->isEmpty() ? '' : 'hidden' }}" data-empty>Nothing here</p>
                    </section>
                @endforeach
            </div>
        </div>
    </div>

    @vite('resources/js/board.js')
</x-app-layout>
