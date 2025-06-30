<x-app-layout>
<x-loading-screen />
    <head>
        <title>Dashboard</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    </head>

    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-200 leading-tight uppercase tracking-wide slide-in">
            {{ __('Team Insights & Daily Highlights') }}
        </h2>
    </x-slot>

    <style>
        .slide-in {
            animation: slideIn 0.5s ease-out;
        }

        @keyframes slideIn {
            from {
                transform: translateX(-100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
    </style>

    <div class="grid grid-cols-2 grid-rows-2 gap-4 p-10">

        <!-- Floating Buttons Container -->
        <div class="fixed bottom-6 right-6 flex flex-col items-end space-y-3">
            <!-- Dark Mode Toggle -->
            <button id="theme-toggle"
                class="bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 px-4 py-3 rounded-lg shadow-md transition">
                <i id="theme-toggle-icon" class="fa-solid fa-moon"></i>
            </button>
            <!-- DB Team Redirect Button (Visible only if user position contains 'database') -->
            @if (stripos(auth()->user()->position, 'database') !== false)
            <a href="{{ url('/dbteam') }}"
                class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-3 rounded-lg shadow-md transition flex items-center justify-center">
                <i class="fa-solid fa-clipboard-check mr-2"></i>
                DB Team Checklist
            </a>
            @endif
        </div>

        <!-- ✅ Reminder for EOD Logs (1) -->
        <div class="bg-white dark:bg-gray-800 p-4 shadow-md rounded-lg">
            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300">Reminder to do EOD Logs</h3>
            @if($hasSubmittedEodToday)
                <p class="text-sm text-green-600 dark:text-green-400">Great job! You have completed your EOD log today.</p>
                <img class="h-[250px] w-[250px] mt-2 justify-self-center" src="/images/rdone.png" alt="Completed EOD">
            @else
                <p class="text-sm text-gray-600 dark:text-gray-400">Don't forget to complete your EOD log today!</p>
                <img class="h-[250px] w-[250px] mt-2 justify-self-center animate-float" src="/images/reminder.png"
                    alt="Reminder">
            @endif
        </div>

        <!-- ✅ Users on Day Off / Leave (2) -->
        <div class="bg-white dark:bg-gray-800 p-4 shadow-md rounded-lg col-start-1 row-start-2">
            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300">Teammates on Leave/Day Off Today</h3>
            @if($dayOffUsers->isEmpty())
            <div class="flex flex-col items-center justify-center h-full">
                <p class="text-gray-500 text-center">No team members on leave today.</p>
                <img class="h-[150px] w-[150px] mt-4" src="/images/noleave.png" alt="No leave">
            </div>
            @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mt-4">
                @foreach ($dayOffUsers as $shift)
                <div class="p-4 flex flex-col items-center bg-white dark:bg-gray-800 rounded-lg shadow-md transition-all hover:scale-105">
                    <img class="h-16 w-16 rounded-full mb-3" src="{{ $shift->user->avatar }}" alt="User Avatar">
                    <p class="text-md font-medium text-gray-900 dark:text-gray-100 text-center">{{ $shift->user->name }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400 text-center">
                        {{ ucfirst(str_replace('-', ' ', $shift->shift_time)) }}
                    </p>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        <!-- ✅ Users Missing EOD Logs (3) - Spanning 2 Rows -->

        <div class="bg-white dark:bg-gray-800 p-4 shadow-md rounded-lg row-span-2 col-start-2 row-start-1">
            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300">Missing EOD Logs on your team today.</h3>
            <p class="text-gray-600 dark:text-gray-400">Remind your teammates to do their EOD logs!</p>
            @if($usersWithoutEodToday->isEmpty())
            <div class="flex items-center justify-center h-screen">
                <p class="text-gray-500 dark:text-gray-400">Everyone has submitted their EOD logs.</p>
            </div>
            @else
            @php
                $softColors = ['#FAF1E6', '#FDFAF6', '#E4EFE7', '#99BC85'];
            @endphp

            <ul class="divide-y divide-gray-200 dark:divide-gray-700 mt-2">
                @foreach ($usersWithoutEodToday as $user)
                @php
                    $randomColor = $softColors[array_rand($softColors)];
                @endphp
                <li class="p-3 m-3 flex items-center space-x-3 rounded-lg shadow-sm transition-all hover:scale-105"
                    style="background-color: {{ $randomColor }};">
                    <img class="h-8 w-8 rounded-full" src="{{ $user->avatar }}" alt="User Avatar">
                    <div>
                    <p class="text-md font-medium text-gray-900 dark:text-black-500">{{ $user->name }}</p>
                    <p class="text-gray-600 dark:text-black">{{ $user->position }}</p>
                    </div>
                </li>
                @endforeach
            </ul>
            @endif
        </div>


    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const themeToggleBtn = document.getElementById("theme-toggle");
            const themeToggleIcon = document.getElementById("theme-toggle-icon");
            const htmlElement = document.documentElement;

            // Check local storage for dark mode preference
            if (localStorage.getItem("theme") === "dark") {
                htmlElement.classList.add("dark");
                themeToggleIcon.classList.replace("fa-moon", "fa-sun");
            }

            themeToggleBtn.addEventListener("click", function () {
                if (htmlElement.classList.contains("dark")) {
                    htmlElement.classList.remove("dark");
                    themeToggleIcon.classList.replace("fa-sun", "fa-moon");
                    localStorage.setItem("theme", "light");
                } else {
                    htmlElement.classList.add("dark");
                    themeToggleIcon.classList.replace("fa-moon", "fa-sun");
                    localStorage.setItem("theme", "dark");
                }
            });
        });

    </script>
</x-app-layout>
