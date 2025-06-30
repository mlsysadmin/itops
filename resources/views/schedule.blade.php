<x-app-layout>

    <head>
        <title>Shifting Schedule</title>
        <script src="https://cdn.jsdelivr.net/npm/dayjs@1/dayjs.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="{{ asset('js/schedule.js') }}"></script>
        <meta name="csrf-token" content="{{ csrf_token() }}">
    </head>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight text-center">
            {{ __('Manage Team Shift Schedule') }}
        </h2>
    </x-slot>

    <!-- Floating Buttons Container (Properly Stacked & Aligned) -->
    <div class="fixed bottom-20 right-6 flex flex-col items-end space-y-4">
        <!-- Dark Mode Toggle Button (Top) -->
        <button id="theme-toggle" class="bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 w-12 h-12 flex items-center justify-center
               rounded-full shadow-md transition cursor-pointer">
            <i id="theme-toggle-icon" class="fa-solid fa-moon"></i>
        </button>

        <!-- Floating Action Button (FAB) -->
        <a onclick="openShiftAssignmentModal()" class="group relative flex items-center bg-gradient-to-r from-red-500 to-red-700 text-white w-16 h-16 rounded-full shadow-lg
          hover:w-[280px] transition-all duration-300 overflow-hidden cursor-pointer">

            <!-- Icon (Fixed Position) -->
            <i class="fa-solid fa-user-plus text-2xl ml-5 transition-all duration-300"></i>

            <!-- Expanding Text (Fully Visible) -->
            <span class="ml-4 opacity-0 text-sm font-medium whitespace-nowrap transition-all duration-300
                 group-hover:opacity-100 group-hover:ml-6">
                Add schedules for your team.
            </span>
        </a>
    </div>

    <div class="py-12 flex justify-center">
        <div class="max-w-6xl w-full bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-8">

            <!-- Month Navigation -->
            <div class="flex justify-between items-center mb-4">
                <button onclick="changeMonth(-1)"
                    class="flex items-center px-5 py-2 bg-gradient-to-r from-red-500 to-red-700 text-sm text-white font-semibold rounded-full shadow-lg hover:from-red-600 hover:to-red-800 transition-all">
                    <i class="fa-solid fa-arrow-left mr-2"></i> Previous
                </button>
                <h2 id="currentMonth" class="text-lg font-bold text-gray-800 dark:text-gray-200"></h2>
                <button onclick="changeMonth(1)"
                    class="flex items-center px-5 py-2 bg-gradient-to-r from-red-500 to-red-700 text-sm text-white font-semibold rounded-full shadow-lg hover:from-red-600 hover:to-red-800 transition-all">
                    Next <i class="fa-solid fa-arrow-right ml-2"></i>
                </button>
            </div>

            <!-- Calendar Grid -->
            <div class="grid grid-cols-7 gap-4 text-center text-lg text-gray-800 dark:text-gray-200">
                <div class="font-semibold">Sun</div>
                <div class="font-semibold">Mon</div>
                <div class="font-semibold">Tue</div>
                <div class="font-semibold">Wed</div>
                <div class="font-semibold">Thu</div>
                <div class="font-semibold">Fri</div>
                <div class="font-semibold">Sat</div>
            </div>
            <div id="calendarGrid" class="grid grid-cols-7 gap-4 mt-4 text-lg text-gray-800 dark:text-gray-200"></div>
        </div>
    </div>

    <!-- Shift Assignment Modal -->
    <div id="shiftAssignmentModal"
        class="fixed inset-0 flex items-center justify-center hidden bg-gray-900 bg-opacity-50">
        <div class="bg-white dark:bg-gray-800 p-8 rounded-lg shadow-lg w-full max-w-xl">
            <h3 class="text-xl font-semibold mb-4 text-gray-800 dark:text-gray-200">Assign Shifts</h3>

            <!-- Employee List -->
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Select Employees</label>
            <div class="border dark:border-gray-700 rounded-lg p-3 max-h-60 overflow-y-auto mb-4">
                @foreach($employees as $employee)
                    <div class="flex items-center gap-2">
                        <input type="checkbox" id="employee-{{ $employee->id }}" value="{{ $employee->id }}"
                            class="employee-checkbox">
                        <img src="{{ $employee->avatar }}" alt="{{ $employee->name }}'s avatar"
                            class="w-8 h-8 rounded-full">
                        <label for="employee-{{ $employee->id }}"
                            class="text-gray-800 dark:text-gray-200">{{ $employee->name }}</label>
                    </div>
                @endforeach
            </div>

            <!-- Date Selection -->
            <label for="startDate" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Start Date</label>
            <input type="date" id="startDate"
                class="w-full p-2 border border-gray-300 dark:border-gray-700 rounded-lg mb-2">

            <label for="endDate" class="block text-sm font-medium text-gray-700 dark:text-gray-300">End Date</label>
            <input type="date" id="endDate"
                class="w-full p-2 border border-gray-300 dark:border-gray-700 rounded-lg mb-4">

            <div class="flex items-center mb-4">
                <input type="checkbox" id="skipSundays" class="mr-2">
                <label for="skipSundays" class="text-sm text-gray-700 dark:text-gray-300">Skip Sundays</label>
            </div>


            <!-- Shift Selection -->
            <label for="shiftSelect" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Select
                Shift</label>
            <select id="shiftSelect" class="w-full p-2 border border-gray-300 dark:border-gray-700 rounded-lg">
                <option value="2pm-11pm">2:00 PM - 11:00 PM</option>
                <option value="6am-3pm">6:00 AM - 3:00 PM</option>
                <option value="7am-4pm">7:00 AM - 4:00 PM</option>
                <option value="8am-5pm">8:00 AM - 5:00 PM</option>
                <option value="8am-4pm-saturday">8:00 AM - 4:00 PM (Saturday Only)</option>
                <option value="8:30am-5:30pm">8:30 AM - 5:30 PM</option>
                <option value="10pm-7am">10:00 PM - 7:00 AM</option>
                <option value="sick-leave">Sick Leave</option>
                <option value="vacation-leave">Vacation Leave</option>
                <option value="day-off">Day Off</option>
                <option value="half-day">Half Day</option>
            </select>

            <div class="flex justify-end mt-4 space-x-2">
                <button onclick="closeShiftAssignmentModal()"
                    class="px-4 py-2 text-gray-600 dark:text-gray-300 bg-gray-300 dark:bg-gray-700 rounded-lg hover:bg-gray-400 dark:hover:bg-gray-600 transition">
                    Cancel
                </button>
                <button onclick="assignShifts()"
                    class="px-4 py-2 text-white bg-green-500 dark:bg-green-700 rounded-lg hover:bg-green-600 dark:hover:bg-green-800 transition">
                    Assign
                </button>
            </div>
        </div>
    </div>

    <!-- Shift Details Modal -->
    <div id="dateDetailsModal" class="fixed inset-0 flex items-center justify-center hidden bg-gray-900 bg-opacity-50">
        <div class="bg-white dark:bg-gray-800 p-8 rounded-lg shadow-lg w-full max-w-3xl">
            <h3 class="text-2xl font-semibold mb-4 text-gray-800 dark:text-gray-200">Shift Details</h3>

            <div id="dateDetailsContent"
                class="mb-4 space-y-3 max-h-96 overflow-y-auto p-3 border rounded-lg bg-gray-50 dark:bg-gray-700"></div>

            <div class="flex justify-end">
                <button onclick="closeDateDetails()"
                    class="px-5 py-3 bg-gray-500 dark:bg-gray-700 text-white rounded-lg hover:bg-gray-600 dark:hover:bg-gray-800 transition text-lg">
                    Close
                </button>
            </div>
        </div>
    </div>

    <!-- Edit Shift Modal -->
    <div id="editShiftModal" class="fixed inset-0 flex items-center justify-center hidden bg-gray-900 bg-opacity-50">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg w-1/3">
            <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-200">Edit Shift</h3>

            <input type="hidden" id="editDateKey">
            <input type="hidden" id="editShiftIndex">

            <label for="editShiftSelect" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Select New
                Shift</label>
            <select id="editShiftSelect" class="w-full p-2 border border-gray-300 dark:border-gray-700 rounded-lg mb-4">
                <option value="6am-3pm">6:00 AM - 3:00 PM</option>
                <option value="7am-4pm">7:00 AM - 4:00 PM</option>
                <option value="8am-4pm">8:00 AM - 4:00 PM (Saturday Only)</option>
                <option value="8am-5pm">8:00 AM - 5:00 PM</option>
                <option value="8:30am-5:30pm">8:30 AM - 5:30 PM</option>
                <option value="10pm-7am">10:00 PM - 7:00 AM</option>
                <option value="sick-leave">Sick Leave</option>
                <option value="vacation-leave">Vacation Leave</option>
                <option value="day-off">Day Off</option>
                <option value="half-day">Half Day</option>
            </select>

            <div class="flex justify-end space-x-2">
                <button onclick="closeEditShiftModal()"
                    class="px-4 py-2 text-gray-600 dark:text-gray-300 bg-gray-300 dark:bg-gray-700 rounded-lg hover:bg-gray-400 dark:hover:bg-gray-600">Cancel</button>
                <button onclick="saveEditedShift()"
                    class="px-4 py-2 text-white bg-green-500 dark:bg-green-700 rounded-lg hover:bg-green-600 dark:hover:bg-green-800">Save</button>
            </div>
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
