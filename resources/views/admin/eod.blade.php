<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="/images/itopslogo.png">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>End of Day Logs</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<style>
    /* Custom scrollbar */
    ::-webkit-scrollbar {
        width: 8px;
    }

    /* Style the search input */
    .dataTables_filter {
        margin-bottom: 15px;
    }

    /* Dark mode styles for search input */
    .dark .dataTables_filter input {
        background-color: #333;
        color: #fff;
        border: 1px solid #555;
    }

    /* Make search input and pagination align well */
    .dataTables_filter input {
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 5px;
    }

    /* Dark mode styles for pagination links */
    .dark .dataTables_paginate .paginate_button {
        background-color: #333;
        color: #fff !important;
        border: 1px solid #555;
    }

    .dark .dataTables_paginate .paginate_button.current {
        background-color: #555;
        color: #fff !important;
    }

    /* Dark mode styles for search label */
    .dark .dataTables_filter label {
        color: #fff;
    }
</style>

<body class="bg-gray-100 dark:bg-gray-900 text-sm flex">

    <!-- Include the Navbar -->
    @include('layouts.admin.navbar')

    <div class="fixed bottom-6 left-6 flex flex-col items-end space-y-3 ml-[50px] mb-[-10px]">
        <!-- Dark Mode Toggle -->
        <button id="theme-toggle"
            class="bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 px-4 py-3 rounded-lg shadow-md transition">
            <i id="theme-toggle-icon" class="fa-solid fa-moon"></i>
        </button>
    </div>

    <div class="p-6 w-full ml-48">
        <div class="bg-white dark:bg-gray-800 shadow-xl rounded-lg p-6">
            <div class="overflow-x-auto">
                <table id="logsTable"
                    class="w-full border border-gray-300 dark:border-gray-700 rounded-lg text-gray-700 dark:text-gray-300 shadow-sm">
                    <thead
                        class="bg-gradient-to-r from-gray-200 to-gray-300 dark:from-gray-700 dark:to-gray-800 text-gray-900 dark:text-gray-100 font-semibold uppercase text-sm tracking-wider">
                        <tr>
                            <th class="p-4 border dark:border-gray-700"><i class="fa-solid fa-calendar-week"></i> Date
                            </th>
                            <th class="p-4 border dark:border-gray-700"><i class="fa-solid fa-people-group"></i> User
                            </th>
                            <th class="p-4 border dark:border-gray-700"><i class="fa-solid fa-shield-cat"></i> Position
                            </th>
                            <th class="p-4 border dark:border-gray-700"><i class="fa-solid fa-rotate"></i> Shift</th>
                            <th class="p-4 border dark:border-gray-700"><i class="fa-regular fa-rectangle-list"></i>
                                Summary</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($logs as $log)
                                                <tr class="hover:bg-gray-100 dark:hover:bg-gray-700 hover:scale-[1.02] transition-all cursor-pointer log-row"
                                                    data-log="{{ json_encode([
                                'avatar' => $log->user->avatar,
                                'name' => $log->user->name,
                                'email' => $log->user->email,
                                'position' => $log->user->position,
                                'tasks' => $log->tasks
                            ]) }}">
                                                    <td class="p-4 border text-gray-800 dark:text-gray-300 dark:border-gray-700">
                                                        {{ $log->created_at->format('M d, Y') }}</td>
                                                    <td class="p-4 border flex items-center space-x-3 dark:border-gray-700">
                                                        <img src="{{ $log->user->avatar }}"
                                                            class="w-9 h-9 rounded-full border-2 border-gray-300 dark:border-gray-600 shadow-sm">
                                                        <span class="font-semibold dark:text-gray-300">{{ $log->user->name }}</span>
                                                    </td>
                                                    <td class="p-4 border text-gray-600 dark:text-gray-400 dark:border-gray-700">
                                                        {{ $log->user->position }}</td>
                                                    <td class="p-4 border text-gray-600 dark:text-gray-400 dark:border-gray-700">
                                                        {{ $log->schedule }}</td>
                                                    <td class="p-4 border text-gray-500 dark:text-gray-400 italic dark:border-gray-700">
                                                        {{ Str::limit($log->tasks, 50, '...') }}</td>
                                                </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Log Details Modal -->
    <div id="logModal"
        class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 opacity-0 pointer-events-none transition-opacity duration-300">
        <div
            class="bg-white dark:bg-gray-800 rounded-lg shadow-lg w-full max-w-3xl p-6 relative transform scale-95 transition-transform duration-300">
            <!-- Close Button -->
            <button id="closeModal"
                class="absolute top-3 right-3 text-gray-600 dark:text-gray-400 hover:text-red-500 text-2xl">
                &times;
            </button>

            <!-- Modal Content -->
            <div class="text-center">
                <img id="logAvatar" src="/images/itopslogo.png" alt="User Avatar"
                    class="w-24 h-24 rounded-full border-4 border-red-500 mx-auto shadow-lg">
                <h2 id="logName" class="text-xl font-semibold mt-3 text-gray-800 dark:text-gray-300"></h2>
                <p id="logEmail" class="text-gray-500 dark:text-gray-400 text-sm"></p>
            </div>

            <hr class="my-4 border-red-500">

            <!-- Log Tasks -->
            <div id="logTasks"
                class="text-gray-800 dark:text-gray-300 text-sm bg-gray-100 dark:bg-gray-700 p-4 rounded-md max-h-[500px] overflow-y-auto min-h-[200px]">
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="{{ asset('js/admineod.js') }}"></script>
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

</body>

</html>