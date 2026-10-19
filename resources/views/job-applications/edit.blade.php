<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit: {{ $application->position }} at {{ $application->company }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('job-applications.update', $application) }}">
                    @csrf
                    @method('PUT') {{-- HTML forms only do GET/POST; this tells Laravel it's an update --}}

                    @include('job-applications._form')

                    <div class="mt-6 flex items-center justify-end gap-4">
                        <a href="{{ route('job-applications.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Cancel</a>
                        <x-primary-button>Save changes</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
