<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="/images/itopslogo.png">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Team Shifts</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/adminshift.js') }}" defer></script>
</head>

<body class="bg-gray-100 dark:bg-gray-900 text-sm flex"></body>

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
    <div class="flex flex-col w-full min-h-screen ml-56 mt-14 px-8 mb-9">

        <!-- Calendar Header -->
        <div class="flex flex-col items-center mb-6">
            <h2 class="text-xl font-bold text-gray-800 dark:text-gray-200">Team Shift Calendar</h2>
            <div class="h-1 w-32 bg-red-500 rounded-full mt-1"></div>
        </div>

        <!-- Position Filter -->
        <div class="flex items-center justify-center space-x-3 mb-6">
            <label for="position-filter" class="text-sm font-medium text-gray-700 dark:text-gray-300">Filter by Team:</label>
            <select id="position-filter"
                class="border border-gray-300 dark:border-gray-600 p-2 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500">
                <option value="" disabled selected>Select Team</option>
                <option value="Network Admin">Network Team</option>
                <option value="IT Security Admin">IT Security Team</option>
                <option value="Database Admin">Database Team</option>
                <option value="Application Security Admin">Application Security Team</option>
                <option value="System Admin">Systems Team</option>
            </select>
        </div>

        <!-- Calendar Container -->
        <div class="w-full max-w-5xl bg-white dark:bg-gray-800 shadow-lg rounded-lg p-6 mx-auto">

            <!-- Month Navigation -->
            <div class="flex justify-between items-center mb-4 px-6">
                <button onclick="changeMonth(-1)"
                    class="px-4 py-2 bg-red-500 text-sm text-white rounded-lg hover:bg-red-600">
                    <i class="fa-solid fa-arrow-left"></i>
                </button>
                <h2 id="currentMonth" class="text-lg font-semibold text-gray-800 dark:text-gray-200"></h2>
                <button onclick="changeMonth(1)"
                    class="px-4 py-2 bg-red-500 text-sm text-white rounded-lg hover:bg-red-600">
                    <i class="fa-solid fa-arrow-right"></i>
                </button>
            </div>

            <!-- Calendar Grid -->
            <div class="grid grid-cols-7 gap-1 text-center font-semibold bg-gray-200 dark:bg-gray-700 p-2 rounded-md">
                <div class="py-2 text-gray-800 dark:text-gray-200">Sun</div>
                <div class="py-2 text-gray-800 dark:text-gray-200">Mon</div>
                <div class="py-2 text-gray-800 dark:text-gray-200">Tue</div>
                <div class="py-2 text-gray-800 dark:text-gray-200">Wed</div>
                <div class="py-2 text-gray-800 dark:text-gray-200">Thu</div>
                <div class="py-2 text-gray-800 dark:text-gray-200">Fri</div>
                <div class="py-2 text-gray-800 dark:text-gray-200">Sat</div>
            </div>
            <div id="calendarGrid" class="grid grid-cols-7 gap-2 mt-2">
                <!-- Dates will be injected here dynamically -->
            </div>
        </div>
    </div>

    <!-- Shift Details Modal -->
    <div id="shiftModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg w-full max-w-md max-h-[80vh] overflow-hidden">
            <h2 class="text-lg font-bold text-gray-800 dark:text-gray-200 mb-3 flex items-center">
                <svg class="w-5 h-5 text-blue-500 mr-2" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M8 7V3m8 4V3m-9 4h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                    </path>
                </svg>
                Shift Details
            </h2>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4" id="modalDate"></p>

            <!-- Scrollable content container -->
            <div id="modalShiftDetails" class="space-y-2 overflow-y-auto max-h-[50vh] pr-2"></div>

            <button onclick="closeModal()"
                class="mt-4 w-full px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">
                Close
            </button>
        </div>
    </div>


    <!-- Pass shifts data to JavaScript -->
    <script>
        const shifts = @json($shifts);
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

</body>

</html>