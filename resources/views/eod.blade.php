<x-app-layout>

    <head>
        <title>End of Day</title>
        <link rel="icon" type="image/x-icon" href="images/itopslogo.png">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    </head>

    <x-slot name="header">
        <div class="flex justify-between items-center">

            <!-- Submit EOD Button -->
            <button onclick="openSubmissionModal()" class="group relative flex items-center bg-red-600 text-white px-6 py-3 text-sm font-semibold rounded-lg shadow-lg
               transition-all duration-300 cursor-pointer overflow-hidden
               hover:pr-32">

                <!-- Icon (Fixed Position) -->
                <i class="fa-solid fa-plus mr-2"></i>

                <!-- Text Container -->
                <span class="relative whitespace-nowrap transition-all duration-300">
                    <!-- Default Text -->
                    <span class="group-hover:opacity-0 transition-opacity duration-300">
                        Submit EOD
                    </span>

                    <!-- Hover Text -->
                    <span class="absolute left-0 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        What were your tasks today?
                    </span>
                </span>
            </button>

            <!-- Floating Buttons Container -->
            <div class="fixed bottom-6 right-6 flex flex-col items-end space-y-3">
                <!-- Dark Mode Toggle -->
                <button id="theme-toggle"
                    class="bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 px-4 py-3 rounded-lg shadow-md transition">
                    <i id="theme-toggle-icon" class="fa-solid fa-moon"></i>
                </button>
            </div>

        </div>
    </x-slot>


    <div class="max-w-5xl mx-auto min-h-auto mt-6 p-6 bg-white dark:bg-gray-800 shadow-lg rounded-lg">

        <!-- Filters Row -->
        <div class="flex justify-between items-center mb-4">
            <!-- Search Bar & Date Filter Container -->
            <div class="flex space-x-4 w-2/3">
                <!-- Search Bar -->
                <input type="text" id="search" placeholder="Find member..."
                    class="w-1/2 p-3 border border-gray-300 rounded-lg focus:ring focus:ring-blue-300 text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 dark:placeholder-gray-400">

                <!-- Date Filter -->
                <div class="flex space-x-2 w-1/2">
                    <input type="date" id="filter-date"
                        class="w-full p-3 border border-gray-300 rounded-lg focus:ring focus:ring-blue-300 text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200">
                    <button onclick="clearDateFilter()"
                        class="px-2 py-1 bg-red-300 rounded-md hover:bg-red-600 transition flex items-center justify-center dark:bg-red-500 dark:hover:bg-red-700">
                        <i class="fa-solid fa-square-xmark text-white text-lg"></i>
                    </button>
                </div>
            </div>

            <!-- Toggle My Logs / Team Logs -->
            <button id="toggleLogs" onclick="toggleLogs()"
                class="px-4 py-3 text-sm text-white bg-red-600 rounded-lg hover:bg-red-700 transition shadow-md hover:shadow-red-500/50 transform hover:scale-105 flex items-center space-x-2 dark:bg-red-500 dark:hover:bg-red-700">
                <i id="toggleIcon" class="fa-solid fa-users"></i>
                <span id="toggleText">Show Team Logs</span>
            </button>
        </div>

        <!-- Logs List -->
        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg shadow-inner max-h-96 overflow-y-auto">
            <ul id="logs-list" class="divide-y divide-gray-300 dark:divide-gray-600 scroll-smooth">
            @foreach($logs as $log)
                <li class="p-4 flex justify-between items-center bg-white dark:bg-gray-800 rounded-lg shadow-md hover:shadow-lg transition mb-2 dark:hover:bg-gray-700"
                onclick="viewLog('{{ $log->user->name }}', '{{ $log->created_at->format('F d, Y') }}', `{{ addslashes($log->tasks) }}`)">
                <div class="text-gray-800 dark:text-gray-200">
                    <p class="font-semibold">{{ $log->user->name }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $log->created_at->format('F d, Y') }}</p>
                </div>
                </li>
            @endforeach
            </ul>
        </div>
    </div>

    <!-- Edit Log Modal -->
    <div id="edit-modal"
        class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 opacity-0 pointer-events-none transition-opacity duration-300">
        <div
            class="inline-flex flex-col p-6 bg-white dark:bg-gray-800 rounded-lg shadow-lg transform scale-95 transition-transform duration-300 w-auto min-w-[300px] max-w-4xl">
            <h2 class="mb-4 text-lg font-semibold text-gray-800 dark:text-gray-200">Edit End of Day Log</h2>

            <!-- Task Input -->
            <label for="edit-tasks" class="block mb-2 font-medium text-gray-800 dark:text-gray-200">Update Tasks</label>
            <textarea id="edit-tasks"
                class="w-auto min-w-[300px] max-w-full h-40 min-h-[100px] max-h-[500px] p-2 border border-gray-300 dark:border-gray-600 rounded-lg resize dark:bg-gray-700 dark:text-gray-200"></textarea>

            <!-- Buttons -->
            <div class="flex justify-end mt-4 space-x-2">
                <button onclick="closeEditModal()"
                    class="px-4 py-2 text-gray-600 bg-gray-300 rounded-lg hover:bg-gray-400 transition dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-gray-500">Cancel</button>
                <button onclick="updateLog()"
                    class="px-4 py-2 text-white bg-green-500 rounded-lg hover:bg-green-600 transition">Save
                    Changes</button>
            </div>
        </div>
    </div>

    <!-- Log Details Modal -->
    <div id="logDetailsModal" class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 hidden">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg w-full max-w-2xl">
            <!-- Header -->
            <div class="flex justify-between items-center border-b pb-2 mb-4 border-gray-300 dark:border-gray-600">
                <div>
                    <p id="logUser" class="text-lg font-semibold text-gray-900 dark:text-gray-200"></p>
                    <p id="logDate" class="text-sm text-gray-500 dark:text-gray-400"></p>
                </div>
                <button onclick="closeLogModal()"
                    class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                    <i class="fa-solid fa-times text-xl"></i>
                </button>
            </div>

            <!-- Tasks Content -->
            <div id="logTasks"
                class="text-gray-800 dark:text-gray-200 text-left whitespace-pre-line leading-relaxed max-h-80 overflow-y-auto p-2">
            </div>

            <!-- Close Button -->
            <div class="flex justify-end mt-4">
                <button onclick="closeLogModal()"
                    class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                    Close
                </button>
            </div>
        </div>
    </div>


    <!-- Submission Modal -->
    <div id="submission-modal"
        class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 opacity-0 pointer-events-none transition-opacity duration-300">
        <div
            class="inline-flex flex-col p-6 bg-white dark:bg-gray-800 rounded-lg shadow-lg transform scale-95 transition-transform duration-300 w-auto min-w-[300px] max-w-4xl">
            <h2 class="mb-4 text-lg font-semibold text-gray-800 dark:text-gray-200">Submit End of Day Report</h2>

            <!-- Schedule Dropdown -->
            <label for="schedule" class="block mb-2 font-medium text-gray-800 dark:text-gray-200">Select
                Schedule</label>
            <select id="schedule"
                class="w-full p-2 mb-4 border border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200">
                <option value="2pm-11pm">2:00 PM - 11:00 PM</option>
                <option value="6am-3pm">6:00 AM - 3:00 PM</option>
                <option value="7am-4pm">7:00 AM - 4:00 PM</option>
                <option value="8am-5pm">8:00 AM - 5:00 PM</option>
                <option value="8am-4pm-saturday">8:00 AM - 4:00 PM (Saturday)</option>
                <option value="8:30am-5:30pm">8:30 AM - 5:30 PM</option>
                <option value="10pm-7am">10:00 PM - 7:00 AM</option>
            </select>

            <!-- Task Input -->
            <label for="tasks" class="block mb-2 font-medium text-gray-800 dark:text-gray-200">Tasks</label>
            <textarea id="tasks"
                class="w-auto min-w-[300px] max-w-full h-40 min-h-[100px] max-h-[500px] p-2 border border-gray-300 dark:border-gray-600 rounded-lg resize dark:bg-gray-700 dark:text-gray-200"></textarea>

            <!-- Buttons -->
            <div class="flex justify-end mt-4 space-x-2">
                <button onclick="closeSubmissionModal()"
                    class="px-4 py-2 text-gray-600 bg-gray-300 rounded-lg hover:bg-gray-400 transition dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-gray-500">Cancel</button>
                <button onclick="saveData()"
                    class="px-4 py-2 text-white bg-green-500 rounded-lg hover:bg-green-600 transition">Save</button>
            </div>
        </div>
    </div>


    <!-- Pass Laravel Variables to JavaScript -->
    <script>
        var csrfToken = @json(csrf_token());
        var saveEodUrl = @json(route('eod.store'));
        var eodIndexUrl = @json(route('eod'));
        var loggedInUserId = @json(auth()->id());
    </script>

    <!-- Load External JavaScript -->
    <script src="{{ asset('js/eod.js') }}"></script>

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
