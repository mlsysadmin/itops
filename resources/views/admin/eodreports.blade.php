<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="/images/itopslogo.png">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>EOD Reports</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="{{ asset('js/eodreports.js') }}"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="bg-gray-100 dark:bg-gray-900 min-h-screen w-full overflow-auto flex"></body>

    <!-- Left Fixed Navbar -->
    @include('layouts.admin.navbar')

    <div class="fixed bottom-6 left-6 flex flex-col items-end space-y-3 ml-[50px] mb-[-10px]">
        <!-- Dark Mode Toggle -->
        <button id="theme-toggle"
            class="bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 px-4 py-3 rounded-lg shadow-md transition">
            <i id="theme-toggle-icon" class="fa-solid fa-moon"></i>
        </button>
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

    <!-- Grid Content Area -->
    <div class="flex-grow p-6 md:ml-[200px] w-full">
        <div class="grid grid-cols-1 md:grid-cols-3 grid-rows-7 gap-4 max-w-[1400px] mx-auto">

            <!-- Weekly Filter -->
            <div class="row-span-3 bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 flex flex-col justify-start">
                <h2 class="text-lg font-bold mb-3 dark:text-gray-100">Weekly Missing Logs</h2>
                <div class="relative">
                    <select id="weekly-filter" class="block w-full px-4 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        <option value="This Week" selected>This Week</option>
                        <option value="Last Week">Last Week</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <i class="fa-solid fa-chevron-down text-gray-400 dark:text-gray-500"></i>
                    </div>
                </div>
                <div id="weekly-missing-users"
                    class="mt-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg shadow-inner overflow-y-auto text-xs border border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-200"
                    style="min-height: 150px; max-height: 200px;">
                    <!-- Weekly missing logs will be displayed here -->
                </div>
            </div>

            <!-- Monthly Filter -->
            <div class="row-span-3 bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 flex flex-col justify-start">
                <h2 class="text-lg font-bold mb-3 dark:text-gray-100">Monthly Missing Logs</h2>
                <div class="relative">
                    <select id="monthly-filter" class="block w-full px-4 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        @for ($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}">{{ \Carbon\Carbon::create()->month($m)->format('F') }}</option>
                        @endfor
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <i class="fa-solid fa-chevron-down text-gray-400 dark:text-gray-500"></i>
                    </div>
                </div>
                <div id="monthly-missing-users"
                    class="mt-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg shadow-inner overflow-y-auto text-xs border border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-200"
                    style="min-height: 150px; max-height: 200px;">
                    <!-- Monthly missing logs will be displayed here -->
                </div>
            </div>

            <!-- Yearly Filter -->
            <div class="row-span-3 bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 flex flex-col justify-start">
                <h2 class="text-lg font-bold mb-3 dark:text-gray-100">Yearly Missing Logs</h2>
                <div class="relative">
                    <select id="yearly-filter" class="block w-full px-4 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        @for ($y = now()->year; $y >= 2024; $y--)
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endfor
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <i class="fa-solid fa-chevron-down text-gray-400 dark:text-gray-500"></i>
                    </div>
                </div>
                <div id="yearly-missing-users"
                    class="mt-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg shadow-inner overflow-y-auto text-xs border border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-200"
                    style="min-height: 150px; max-height: 200px;">
                    <!-- Yearly missing logs will be displayed here -->
                </div>
            </div>

            <!-- Date Range -->
            <div class="col-span-1 md:col-span-3 row-span-4 row-start-4 bg-white dark:bg-gray-800 rounded-lg shadow-md p-6"
                style="min-height: 350px; max-height: 450px;">
                <h2 class="text-lg font-bold mb-3 dark:text-gray-100">Date Range Missing Logs</h2>

                <div class="flex flex-col md:flex-row gap-4 mb-4">
                    <div class="flex flex-col w-full md:w-auto">
                        <label for="start-date" class="text-sm mb-1 dark:text-gray-200">Start Date</label>
                        <input type="date" id="start-date" class="block w-full px-4 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                    <div class="flex flex-col w-full md:w-auto">
                        <label for="end-date" class="text-sm mb-1 dark:text-gray-200">End Date</label>
                        <input type="date" id="end-date" class="block w-full px-4 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                </div>

                <div id="date-range-results"
                    class="overflow-y-auto border border-gray-300 dark:border-gray-600 rounded-lg p-4 bg-gray-50 dark:bg-gray-700 text-xs text-gray-800 dark:text-gray-200"
                    style="max-height: 250px;">
                    <!-- Result entries will go here -->
                </div>
            </div>

        </div>
    </div>

</body>

</html>