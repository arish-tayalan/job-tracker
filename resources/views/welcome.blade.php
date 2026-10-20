<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name') }} – keep track of every job application</title>
        <meta name="description" content="Track job applications, interviews and follow-ups in one place.">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-50 text-gray-900">

        {{-- Top bar --}}
        <header class="max-w-6xl mx-auto px-6 py-5 flex items-center justify-between">
            <a href="/" class="flex items-center gap-2 font-bold text-lg">
                <x-application-logo class="h-8 w-8" />
                {{ config('app.name') }}
            </a>

            <nav class="flex items-center gap-3 text-sm font-medium">
                @auth
                    <a href="{{ route('job-applications.index') }}"
                        class="rounded-md bg-gray-900 px-4 py-2 text-white hover:bg-gray-700">My applications</a>
                @else
                    <a href="{{ route('login') }}" class="px-3 py-2 text-gray-700 hover:text-gray-900">Log in</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}"
                            class="rounded-md bg-gray-900 px-4 py-2 text-white hover:bg-gray-700">Sign up</a>
                    @endif
                @endauth
            </nav>
        </header>

        <main>
            {{-- Hero --}}
            <section class="max-w-6xl mx-auto px-6 pt-12 pb-16 grid gap-12 lg:grid-cols-2 lg:items-center">
                <div>
                    <p class="inline-block rounded-full bg-indigo-100 px-3 py-1 text-xs font-semibold text-indigo-700">
                        Built for job hunters
                    </p>
                    <h1 class="mt-4 text-4xl sm:text-5xl font-bold tracking-tight text-gray-900">
                        Never lose track of a job application again.
                    </h1>
                    <p class="mt-5 text-lg text-gray-600">
                        Log every application, see where each one stands, and get a nudge when it's time to follow up.
                        Applying to dozens of jobs is hard enough without a messy spreadsheet.
                    </p>

                    <div class="mt-8 flex flex-wrap items-center gap-4">
                        @auth
                            <a href="{{ route('job-applications.index') }}"
                                class="rounded-md bg-indigo-600 px-6 py-3 font-semibold text-white shadow-sm hover:bg-indigo-500">
                                Go to my applications
                            </a>
                        @else
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}"
                                    class="rounded-md bg-indigo-600 px-6 py-3 font-semibold text-white shadow-sm hover:bg-indigo-500">
                                    Get started – it's free
                                </a>
                            @endif
                            <a href="{{ route('login') }}" class="font-semibold text-gray-700 hover:text-gray-900">
                                I already have an account <span aria-hidden="true">&rarr;</span>
                            </a>
                        @endauth
                    </div>
                </div>

                {{-- A small fake preview of the app, drawn with plain HTML/Tailwind --}}
                <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-xl" aria-hidden="true">
                    <div class="mb-4 flex flex-wrap gap-2 text-xs font-medium">
                        <span class="rounded-full border border-gray-800 bg-gray-800 px-3 py-1 text-white">All (4)</span>
                        <span class="rounded-full border border-gray-300 px-3 py-1 text-gray-700">Applied (2)</span>
                        <span class="rounded-full border border-gray-300 px-3 py-1 text-gray-700">Interviewing (1)</span>
                        <span class="rounded-full border border-gray-300 px-3 py-1 text-gray-700">Offer (1)</span>
                    </div>

                    <div class="divide-y divide-gray-100 text-sm">
                        <div class="flex items-center justify-between py-3">
                            <div>
                                <div class="font-medium">Northwind Digital</div>
                                <div class="text-gray-500">Junior Laravel Developer</div>
                            </div>
                            <span class="rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-700">Offer</span>
                        </div>
                        <div class="flex items-center justify-between py-3">
                            <div>
                                <div class="font-medium">Brightside Labs</div>
                                <div class="text-gray-500">Web Developer</div>
                                <span class="mt-1 inline-flex rounded bg-orange-100 px-1.5 py-0.5 text-xs font-medium text-orange-800">Follow up due</span>
                            </div>
                            <span class="rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-medium text-amber-800">Interviewing</span>
                        </div>
                        <div class="flex items-center justify-between py-3">
                            <div>
                                <div class="font-medium">Contoso Tech</div>
                                <div class="text-gray-500">Graduate Software Engineer</div>
                            </div>
                            <span class="rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-700">Applied</span>
                        </div>
                        <div class="flex items-center justify-between py-3">
                            <div>
                                <div class="font-medium">Fabrikam Studio</div>
                                <div class="text-gray-500">Full Stack Developer</div>
                            </div>
                            <span class="rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-700">Applied</span>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Features --}}
            <section class="border-t border-gray-200 bg-white">
                <div class="max-w-6xl mx-auto px-6 py-16">
                    <h2 class="text-center text-2xl font-bold">Everything you need, nothing you don't</h2>

                    <div class="mt-10 grid gap-8 sm:grid-cols-2 lg:grid-cols-4">
                        <div>
                            <h3 class="font-semibold">Track every application</h3>
                            <p class="mt-2 text-sm text-gray-600">Company, role, location, job link, notes and the date you applied, all in one list.</p>
                        </div>
                        <div>
                            <h3 class="font-semibold">Filter, search and sort</h3>
                            <p class="mt-2 text-sm text-gray-600">Jump to Interviewing or Offers, search by company, and sort by any column.</p>
                        </div>
                        <div>
                            <h3 class="font-semibold">Log your interviews</h3>
                            <p class="mt-2 text-sm text-gray-600">Record phone screens, technical rounds and notes on how each one went.</p>
                        </div>
                        <div>
                            <h3 class="font-semibold">Follow-up reminders</h3>
                            <p class="mt-2 text-sm text-gray-600">Set a follow-up date and see a "Follow up due" tag when it's time to chase.</p>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <footer class="border-t border-gray-200">
            <div class="max-w-6xl mx-auto px-6 py-6 text-sm text-gray-500 flex flex-wrap items-center justify-between gap-2">
                <span>&copy; {{ date('Y') }} {{ config('app.name') }}</span>
                <span>Built with Laravel, Tailwind CSS and MySQL</span>
            </div>
        </footer>
    </body>
</html>
