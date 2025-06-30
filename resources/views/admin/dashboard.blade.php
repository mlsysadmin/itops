<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="/images/itopslogo.png">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="bg-gray-100 dark:bg-gray-900 text-sm flex">

    <x-loading-screen />

    <!-- Include the Navbar -->
    @include('layouts.admin.navbar')
    <div class="fixed bottom-6 left-6 flex flex-col items-end space-y-3 ml-[50px] mb-[-10px]">
        <!-- Dark Mode Toggle -->
        <button id="theme-toggle"
            class="bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 px-4 py-3 rounded-lg shadow-md transition">
            <i id="theme-toggle-icon" class="fa-solid fa-moon"></i>
        </button>
    </div>

    <!-- Main Content Wrapper -->
    <div class="flex-1 p-6 ml-[200px]">
        <h1 class="text-2xl font-semibold mb-4 text-gray-900 dark:text-gray-100">Dashboard</h1>

        <div class="flex flex-col md:flex-row gap-6">
            <!-- Left Column (2/3 width) -->
            <div class="w-full md:w-2/3 space-y-6">
                <!-- Today's Leave Overview -->
                <div class="bg-white dark:bg-gray-800 p-4 shadow rounded-lg h-[200px]">
                    <h2 class="text-lg font-semibold mb-2 text-gray-900 dark:text-gray-100">Today's Leave Summary</h2>
                    <div class="grid grid-cols-3 gap-4">
                        <div
                            class="p-3 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 rounded text-center">
                            <p class="text-xl font-bold">{{ $sickLeaves->count() }}</p>
                            <p class="text-sm">Sick Leave</p>
                        </div>
                        <div
                            class="p-3 bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200 rounded text-center">
                            <p class="text-xl font-bold">{{ $vacationLeaves->count() }}</p>
                            <p class="text-sm">Vacation Leave</p>
                        </div>
                        <div class="p-3 bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 rounded text-center">
                            <p class="text-xl font-bold">{{ $dayOffs->count() }}</p>
                            <p class="text-sm">Day Off</p>
                        </div>
                    </div>
                </div>

                <!-- Leave User List (Single Line) -->
                <div class="flex gap-6">
                    <!-- Sick Leave -->
                    <div class="flex-1 bg-white dark:bg-gray-800 p-4 shadow rounded-lg">
                        <h2 class="text-lg font-semibold mb-2 text-blue-600 dark:text-blue-400">Sick Leave</h2>
                        @if ($sickLeaves->count() == 0)
                            <p class="text-gray-900 dark:text-gray-100">No one is on sick leave today.</p>
                            <div class="flex justify-center">
                                <img src="/images/sickleave.png" alt="Sick Leave" class="w-[300px]">
                            </div>
                        @else
                            <ul class="text-sm text-gray-700 dark:text-gray-300">
                                @foreach ($sickLeaves as $shift)
                                    <li class="bg-blue-600 text-white font-bold text-center p-2 rounded-full my-1">
                                        {{ $shift->user->name }}
                                    </li>
                                @endforeach
                            </ul>
                        @endif

                    </div>

                    <!-- Vacation Leave -->
                    <div class="flex-1 bg-white dark:bg-gray-800 p-4 shadow rounded-lg">
                        <h2 class="text-lg font-semibold mb-2 text-yellow-600 dark:text-yellow-400">Vacation Leave</h2>
                        @if ($vacationLeaves->count() == 0)
                            <p class="text-gray-900 dark:text-gray-100">No one is on vacation leave today.</p>
                            <div class="flex justify-center">
                                <img src="/images/vacationleave.png" alt="Vacation Leave" class="w-[300px]">
                            </div>
                        @else
                            <ul class="text-sm text-gray-700 dark:text-gray-300">
                                @foreach ($vacationLeaves as $shift)
                                    <li class="bg-yellow-600 text-white font-bold text-center p-2 rounded-full my-1">
                                        {{ $shift->user->name }}
                                    </li>
                                @endforeach
                            </ul>
                        @endif

                    </div>

                    <!-- Day Off -->
                    <div class="flex-1 bg-white dark:bg-gray-800 p-4 shadow rounded-lg">
                        <h2 class="text-lg font-semibold mb-2 text-red-600 dark:text-red-400">Day Off</h2>
                        @if ($dayOffs->count() == 0)
                            <p class="text-gray-900 dark:text-gray-100">No one is on day-off today.</p>
                            <div class="flex justify-center">
                                <img src="/images/dayoff.png" alt="Day Off" class="w-[300px]">
                            </div>
                        @else
                            <ul class="text-sm text-gray-700 dark:text-gray-300">
                                @foreach ($dayOffs as $shift)
                                    <li class="bg-red-600 text-white font-bold text-center p-2 rounded-full my-1">
                                        {{ $shift->user->name }}
                                    </li>
                                @endforeach
                            </ul>
                        @endif

                    </div>
                </div>

                <!-- PROGRESS BAR SECTION WITH RANDOM CONSISTENT COLORS -->
                <div class="card bg-white dark:bg-gray-800 shadow-lg rounded-lg p-6 max-w-auto mx-auto">
                    <!-- Header Section -->
                    <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100 mb-2">Team Eod Logs Performance
                        Overview</h2>
                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">This section displays the success rate of
                        each for doing eod
                        logs.</p>

                    <!-- Progress Bar Section -->
                    @foreach($teamSuccessRates as $team => $data)
                                        @php
                                            $percentage = intval($data['success_rate']);
                                            $colorClass = $percentage < 50 ? 'bg-red-500' : ($percentage < 80 ? 'bg-yellow-400' : 'bg-green-500');
                                            $glowClass = $percentage >= 80 ? 'shadow-lg shadow-green-400' : '';
                                        @endphp
                                        <div class="mb-4">
                                            <h5 class="font-semibold text-gray-700 dark:text-gray-300">{{ $team }} ({{ $percentage }}%)</h5>
                                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-5 overflow-hidden">
                                                <div class="progress-bar {{ $colorClass }} {{ $glowClass }} text-xs font-bold text-white text-center h-5 rounded-full transition-all duration-500 ease-in-out"
                                                    style="width: 0%;" data-width="{{ $percentage }}%">
                                                    <i class="fas fa-chart-line mr-1"></i> <span class="progress-text">0%</span>
                                                </div>
                                            </div>
                                        </div>
                    @endforeach
                    <p class="text-gray-600 dark:text-gray-400">Note: This is monthly based, starting from day 1 of
                        current month to yesterday's.</p>
                </div>

            </div>

            <!-- Right Column (1/3 width) -->
            <div class="w-full md:w-1/3 space-y-6">
                <!-- Users Who Didn't Log Yesterday -->
                <div class="bg-white dark:bg-gray-800 p-4 shadow rounded-lg">
                    <h2 class="text-lg font-semibold mb-2 text-gray-900 dark:text-gray-100">Users Who Didn't Log
                        Yesterday</h2>
                    <ul class="space-y-2">
                        @forelse ($missingLogs as $user)
                            <li
                                class="p-2 bg-white dark:bg-gray-800 shadow border border-gray-200 dark:border-gray-700 rounded-lg flex items-center space-x-2">
                                <img src="{{ $user->avatar }}" alt="User Avatar" class="w-8 h-8 rounded-full object-cover">
                                <span class="text-gray-700 dark:text-gray-300 text-sm font-medium">{{ $user->name }} -
                                    {{ $user->position}}</span>
                            </li>
                        @empty
                            <p class="text-gray-500 dark:text-gray-400">All users have logged their shifts yesterday.</p>
                            <div class="flex justify-center">
                                <img src="/images/eodlog.png" alt="No logs missing" class="w-[300px]">
                            </div>
                        @endforelse

                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Progress Bar Animation
            document.querySelectorAll(".progress-bar").forEach(bar => {
                let targetWidth = bar.getAttribute("data-width");
                let counter = 0;
                let interval = setInterval(() => {
                    if (counter >= parseInt(targetWidth)) {
                        clearInterval(interval);
                    } else {
                        counter++;
                        bar.querySelector(".progress-text").innerText = counter + "%";
                    }
                }, 15);

                bar.style.width = targetWidth;
            });

            // Theme Toggle
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

</body>

</html>