<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name') }} – keep track of every job application</title>
        <meta name="description" content="Track job applications, interviews and follow-ups in one place, with a kanban board and a stats dashboard.">

        {{-- Lets the CSS hide .reveal sections only when JavaScript is available to show them again. --}}
        <script>document.documentElement.classList.add('js')</script>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/landing.js'])
    </head>
    <body class="font-sans antialiased bg-gray-50 text-gray-900">

        {{-- Hero: the glow follows the cursor, the screenshot tilts, the labels drift (see landing.js). --}}
        <div class="spotlight relative overflow-hidden" data-spotlight>

            <header class="relative max-w-6xl mx-auto px-6 py-5 flex items-center justify-between">
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

            <section class="relative max-w-6xl mx-auto px-6 pt-10 pb-20 grid gap-14 lg:grid-cols-2 lg:items-center">
                <div>
                    <p class="inline-block rounded-full bg-indigo-100 px-3 py-1 text-xs font-semibold text-indigo-700">
                        Built for job hunters
                    </p>
                    <h1 class="mt-4 text-4xl sm:text-5xl font-bold tracking-tight text-gray-900">
                        Never lose track of a job application again.
                    </h1>
                    <p class="mt-5 text-lg text-gray-600">
                        Log every application, drag it through your pipeline, record each interview and get a nudge when it's
                        time to follow up. Applying to dozens of jobs is hard enough without a messy spreadsheet.
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

                {{-- The real dashboard. Outer div drifts, middle div tilts, floating labels bob on top. --}}
                <div class="relative">
                    <div data-parallax="14">
                        <div class="tilt" data-tilt="5">
                            <x-browser-frame src="images/landing/dashboard.png" alt="The dashboard: totals, an interview rate, a status chart and applications per week"
                                :width="1280" :height="1078" :eager="true" />
                        </div>
                    </div>

                    <div class="pointer-events-none absolute -top-4 right-8 hidden sm:block" data-parallax="34" aria-hidden="true">
                        <span class="float block rounded-lg bg-white px-3 py-2 text-sm font-medium text-orange-800 shadow-lg ring-1 ring-orange-200">
                            Follow up due
                        </span>
                    </div>
                    <div class="pointer-events-none absolute -bottom-4 left-8 hidden sm:block" data-parallax="-26" aria-hidden="true">
                        <span class="float float-delayed block rounded-lg bg-white px-3 py-2 text-sm font-medium text-green-700 shadow-lg ring-1 ring-green-200">
                            Offer received
                        </span>
                    </div>
                </div>
            </section>
        </div>

        <main>
            {{-- Feature: board (wide layout) --}}
            <section class="border-t border-gray-200 bg-white">
                <div class="reveal max-w-6xl mx-auto px-6 py-20">
                    <div class="max-w-2xl mx-auto text-center">
                        <p class="text-sm font-semibold uppercase tracking-wider text-indigo-600">Kanban board</p>
                        <h2 class="mt-2 text-3xl font-bold tracking-tight">Drag applications through your pipeline</h2>
                        <p class="mt-4 text-gray-600">
                            Move a card from Applied to Interviewing with one drag, and it's saved straight away.
                            On a phone, or if you prefer the keyboard, every card has a "Move to" menu.
                        </p>
                    </div>
                    <div class="tilt mt-12" data-tilt="3">
                        <x-browser-frame src="images/landing/board.png" alt="A kanban board with six columns from Wishlist to Withdrawn"
                            :width="1900" :height="900" />
                    </div>
                </div>
            </section>

            {{-- Feature: list --}}
            <section class="border-t border-gray-200 bg-gray-50">
                <div class="reveal max-w-6xl mx-auto px-6 py-20 grid gap-12 lg:grid-cols-2 lg:items-center">
                    <div class="lg:order-2">
                        <p class="text-sm font-semibold uppercase tracking-wider text-indigo-600">Applications</p>
                        <h2 class="mt-2 text-3xl font-bold tracking-tight">Find any application in seconds</h2>
                        <p class="mt-4 text-gray-600">
                            Once the list grows past a handful, you need ways to cut through it. Search, filter and sort
                            are all there, and they work together.
                        </p>
                        <ul class="mt-6 space-y-3 text-gray-700">
                            <li class="flex gap-3"><span class="mt-0.5 text-indigo-600" aria-hidden="true">&#10003;</span>Search by company or position</li>
                            <li class="flex gap-3"><span class="mt-0.5 text-indigo-600" aria-hidden="true">&#10003;</span>Status tabs with live counts</li>
                            <li class="flex gap-3"><span class="mt-0.5 text-indigo-600" aria-hidden="true">&#10003;</span>Click any column header to sort</li>
                        </ul>
                    </div>
                    <div class="tilt lg:order-1" data-tilt="4">
                        <x-browser-frame src="images/landing/applications.png" alt="The applications list with search, status tabs and sortable columns"
                            :width="1280" :height="1129" :crop="true" />
                    </div>
                </div>
            </section>

            {{-- Feature: interviews and follow-ups --}}
            <section class="border-t border-gray-200 bg-white">
                <div class="reveal max-w-6xl mx-auto px-6 py-20 grid gap-12 lg:grid-cols-2 lg:items-center">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-wider text-indigo-600">Interviews and follow-ups</p>
                        <h2 class="mt-2 text-3xl font-bold tracking-tight">Remember what was said, and when to chase</h2>
                        <p class="mt-4 text-gray-600">
                            Log every phone screen and technical round with notes on how it went. Set a follow-up date and
                            the application is flagged once the day arrives.
                        </p>
                        <ul class="mt-6 space-y-3 text-gray-700">
                            <li class="flex gap-3"><span class="mt-0.5 text-indigo-600" aria-hidden="true">&#10003;</span>Phone, technical, HR, onsite and final rounds</li>
                            <li class="flex gap-3"><span class="mt-0.5 text-indigo-600" aria-hidden="true">&#10003;</span>Notes for each interview</li>
                            <li class="flex gap-3"><span class="mt-0.5 text-indigo-600" aria-hidden="true">&#10003;</span>"Follow up due" tags in the list, board and dashboard</li>
                        </ul>
                    </div>
                    <div class="tilt" data-tilt="4">
                        <x-browser-frame src="images/landing/application-detail.png" alt="An application's detail page with an interview log and a form to add an interview"
                            :width="1280" :height="1289" />
                    </div>
                </div>
            </section>

            {{-- How it works --}}
            <section class="border-t border-gray-200 bg-gray-50">
                <div class="reveal max-w-6xl mx-auto px-6 py-20">
                    <h2 class="text-center text-3xl font-bold tracking-tight">Three steps, no spreadsheet</h2>
                    <ol class="mt-12 grid gap-8 sm:grid-cols-3">
                        <li class="rounded-xl border border-gray-200 bg-white p-6">
                            <span class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-indigo-100 font-bold text-indigo-700">1</span>
                            <h3 class="mt-4 font-semibold">Add an application</h3>
                            <p class="mt-2 text-sm text-gray-600">Company, role, location, the job link and the date you applied.</p>
                        </li>
                        <li class="rounded-xl border border-gray-200 bg-white p-6">
                            <span class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-indigo-100 font-bold text-indigo-700">2</span>
                            <h3 class="mt-4 font-semibold">Track its progress</h3>
                            <p class="mt-2 text-sm text-gray-600">Drag it along the board, log interviews and set a follow-up date.</p>
                        </li>
                        <li class="rounded-xl border border-gray-200 bg-white p-6">
                            <span class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-indigo-100 font-bold text-indigo-700">3</span>
                            <h3 class="mt-4 font-semibold">Review the numbers</h3>
                            <p class="mt-2 text-sm text-gray-600">Check your interview rate and weekly pace on the dashboard.</p>
                        </li>
                    </ol>
                </div>
            </section>

            {{-- Closing call to action --}}
            <section class="bg-indigo-600">
                <div class="reveal max-w-3xl mx-auto px-6 py-16 text-center text-white">
                    <h2 class="text-3xl font-bold tracking-tight">Ready to organise your job hunt?</h2>

                    <div class="mt-8">
                        @auth
                            <a href="{{ route('job-applications.index') }}"
                                class="inline-block rounded-md bg-white px-6 py-3 font-semibold text-indigo-700 shadow-sm hover:bg-indigo-50">
                                Go to my applications
                            </a>
                        @else
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}"
                                    class="inline-block rounded-md bg-white px-6 py-3 font-semibold text-indigo-700 shadow-sm hover:bg-indigo-50">
                                    Create a free account
                                </a>
                            @endif
                            <p class="mt-6 text-sm text-indigo-100">
                                Want a look around first? <a href="{{ route('login') }}" class="font-semibold underline">Log in</a>
                                with the demo account:
                                <span class="whitespace-nowrap font-mono">demo@example.com</span> /
                                <span class="whitespace-nowrap font-mono">DemoPass123</span>
                            </p>
                        @endauth
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
