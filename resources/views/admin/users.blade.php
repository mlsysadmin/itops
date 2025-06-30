<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/x-icon" href="/images/itopslogo.png">
    <title>ITOPS Users</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/adminuser.js') }}"></script>
</head>

<body class="bg-gray-100 text-sm flex dark:bg-gray-800">

    <!-- Include the Navbar -->
    @include('layouts.admin.navbar')

    <div class="fixed bottom-6 left-6 flex flex-col items-end space-y-3 ml-[50px] mb-[-10px]">
        <!-- Dark Mode Toggle -->
        <button id="theme-toggle"
            class="bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 px-4 py-3 rounded-lg shadow-md transition">
            <i id="theme-toggle-icon" class="fa-solid fa-moon"></i>
        </button>
    </div>

    <!-- Main Content -->
    <div class="ml-48 w-full p-8 dark:bg-gray-800 dark:text-gray-200">
        <h1 class="text-2xl font-semibold text-gray-800 dark:text-gray-100">Infrastructure Department Individuals</h1>
        <br>

        <!-- Add Users Button -->
        <button id="openAddUserModal" class="group relative flex items-center bg-gradient-to-r from-red-500 to-red-700 text-white px-7 py-4 rounded-lg
               font-semibold shadow-md transition-all duration-300 ease-in-out hover:pr-[300px] cursor-pointer
               focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-opacity-75 overflow-hidden">

            <!-- User Icon (Fixed Position) -->
            <i class="fa-solid fa-user-plus mr-3"></i>

            <!-- Expanding Text (Ensures Single Line) -->
            <span class="whitespace-nowrap transition-all duration-300 group-hover:opacity-0">
                Add Users
            </span>

            <span
                class="absolute left-14 whitespace-nowrap opacity-0 transition-all duration-300 group-hover:opacity-100">
                Add allowed emails of Infrastructure Team only.
            </span>
        </button>


        <br>
        <div class="space-y-6">
            @foreach($users->groupBy('position') as $position => $group)
                        @php
                            // Assign colors based on team
                            $colors = [
                                'Database Admin' => '#3ca76a',
                                'Application Security Admin' => '#d1b751',
                                'Network Admin' => '#48b9a8',
                                'IT Security Admin' => '#9b5caf',
                                'System Admin' => '#d1544f',
                                'ITOPS Head' => '#1c75d8',
                                'Project Manager' => '#ff7f50'
                            ];
                            $teamColor = $colors[$position] ?? '#757575'; // Default gray
                        @endphp

                        <div x-data="{ open: false }" class="border border-gray-300 rounded-lg shadow-md dark:border-gray-700">
                            <!-- Team Header -->
                            <button @click="open = !open"
                                class="w-full flex justify-between items-center p-4 text-white font-semibold text-lg rounded-t-lg transition-all duration-300"
                                style="background-color: {{ $teamColor }};">
                                {{ ucfirst($position) }}
                                <span x-show="!open" class="text-xl">+</span>
                                <span x-show="open" class="text-xl">−</span>
                            </button>

                            <!-- Collapsible User List -->
                            <div x-show="open"
                                class="p-6 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 dark:bg-gray-800">
                                @foreach($group as $user)
                                    <div class="relative bg-white rounded-xl p-6 flex flex-col items-center text-center shadow-lg border-t-4 transition transform hover:-translate-y-2 hover:shadow-2xl cursor-pointer dark:bg-gray-700 dark:text-gray-200"
                                        style="border-color: {{ $teamColor }};"
                                        onclick="openModal({ id: '{{ $user->id }}', avatar: '{{ $user->avatar }}', name: '{{ $user->name }}', email: '{{ $user->email }}', position: '{{ $user->position }}', role: '{{ $user->role }}' })">

                                        <!-- Avatar -->
                                        <div class="relative w-24 h-24">
                                            <img src="{{ $user->avatar }}" alt="{{ $user->name }}"
                                                class="w-full h-full rounded-full border-4 shadow-lg dark:border-gray-600"
                                                style="border-color: {{ $teamColor }};">
                                            <div class="absolute inset-0 bg-opacity-30 rounded-full"
                                                style="box-shadow: 0px 0px 10px 2px {{ $teamColor }};"></div>
                                        </div>

                                        <!-- User Name -->
                                        <h2 class="mt-4 text-lg font-bold text-gray-900 dark:text-gray-100">{{ $user->name }}</h2>

                                        <!-- Role Capsule -->
                                        <span class="mt-2 px-3 py-1 text-xs font-semibold text-white rounded-full flex items-center"
                                            style="background-color: {{ $teamColor }};">
                                            <svg class="w-4 h-4 mr-1" fill="white" viewBox="0 0 20 20">
                                                <path
                                                    d="M10 2a4 4 0 014 4 4 4 0 11-8 0 4 4 0 014-4zm0 8a7 7 0 00-7 7 1 1 0 001 1h12a1 1 0 001-1 7 7 0 00-7-7z" />
                                            </svg>
                                            {{ ucfirst($user->role) }}
                                        </span>

                                        <!-- Email -->
                                        <p class="mt-2 text-sm text-gray-600 flex items-center dark:text-gray-300">
                                            <svg class="w-4 h-4 mr-1 text-gray-500 dark:text-gray-400" fill="currentColor"
                                                viewBox="0 0 20 20"></svg>
                                            <path
                                                d="M2.94 5.5A2 2 0 014.84 4h10.32a2 2 0 011.9 1.5L10 10.95 2.94 5.5zm-.94 2.52V14a2 2 0 002 2h12a2 2 0 002-2V8.02l-7.44 5.67a1 1 0 01-1.12 0L2 8.02z" />
                                            </svg>
                                            {{ $user->email }}
                                        </p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
            @endforeach
        </div>
    </div>

    <!-- Modal -->
    <div id="userModal" class="fixed inset-0 hidden flex items-center justify-center bg-gray-900 bg-opacity-50">
        <div class="bg-white p-6 rounded-lg shadow-lg w-96 relative dark:bg-gray-800 dark:text-gray-200">
            <!-- Move close button inside -->
            <div class="flex justify-end">
                <button id="modalClose" class="text-gray-600 text-2xl font-bold dark:text-gray-400">&times;</button>
            </div>

            <img id="modalAvatar" src="" class="w-24 h-24 mx-auto rounded-full border-4 dark:border-gray-600">
            <h2 id="modalName" class="text-lg font-semibold text-center mt-2 dark:text-gray-100"></h2>
            <p id="modalEmail" class="text-sm text-center text-gray-600 dark:text-gray-300"></p>

            <div class="mt-4">
                <label class="block text-gray-700 dark:text-gray-300">Position</label>
                <select id="modalPosition" class="w-full border px-3 py-2 rounded dark:bg-gray-700 dark:text-gray-200">
                    <option value="" disabled selected>Select Position</option>
                    <!-- Replace with actual position options -->
                    <option value="ITOPS Head">IT Operations Head</option>
                    <option value="Network Admin">Network Admin</option>
                    <option value="Application Security Admin">Application Security Admin</option>
                    <option value="Database Admin">Database Admin</option>
                    <option value="IT Security Admin">IT Security Admin</option>
                    <option value="System Admin">System Admin</option>
                    <option value="Project Manager">Project Manager</option>
                </select>
            </div>

            <div class="mt-4">
                <label class="block text-gray-700 dark:text-gray-300">Role</label>
                <select id="modalRole" class="w-full border px-3 py-2 rounded dark:bg-gray-700 dark:text-gray-200">
                    <option value="" disabled selected>Select Role</option>
                    <!-- Replace with actual role options -->
                    <option value="admin">Admin</option>
                    <option value="manager">Manager</option>
                    <option value="user">User</option>
                </select>
            </div>

            <div class="mt-4 flex justify-between">
                <button id="saveUser"
                    class="bg-blue-500 text-white px-5 py-2 rounded-lg font-semibold shadow-md transition duration-300 ease-in-out transform hover:bg-blue-600 hover:scale-105 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-opacity-75 dark:bg-blue-600 dark:hover:bg-blue-700">Save</button>
                <button id="deleteUser"
                    class="bg-red-500 text-white px-5 py-2 rounded-lg font-semibold shadow-md transition duration-300 ease-in-out transform hover:bg-red-600 hover:scale-105 focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-opacity-75 dark:bg-red-600 dark:hover:bg-red-700">Delete</button>
            </div>
        </div>
    </div>

    <!-- Add User Modal -->
    <div id="addUserModal" class="fixed inset-0 hidden flex items-center justify-center bg-gray-900 bg-opacity-50">
        <div class="bg-white p-6 rounded-lg shadow-lg w-[32rem] relative dark:bg-gray-800 dark:text-gray-200">
            <!-- Increased width -->
            <!-- Close Button -->
            <button id="closeAddUserModal"
                class="absolute top-2 right-3 text-gray-600 text-2xl font-bold dark:text-gray-400">
                &times;
            </button>

            <h5 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-100">Add Users</h5>

            <form id="addUsersForm">
                @csrf
                <div class="mb-3">
                    <label for="emails" class="block text-gray-700 font-medium dark:text-gray-300">Emails (comma
                        separated)</label>
                    <textarea
                        class="w-full h-24 border px-3 py-2 rounded resize-y focus:ring focus:ring-blue-300 dark:bg-gray-700 dark:text-gray-200"
                        id="emails" placeholder="Enter multiple emails"></textarea>
                    <!-- Changed to textarea for better visibility -->
                </div>

                <div class="mb-3">
                    <label for="role" class="block text-gray-700 font-medium dark:text-gray-300">Role</label>
                    <select
                        class="w-full border px-3 py-2 rounded focus:ring focus:ring-blue-300 dark:bg-gray-700 dark:text-gray-200"
                        id="role">
                        <option value="admin">Admin</option>
                        <option value="user">User</option>
                        <option value="manager">Manager</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="position" class="block text-gray-700 font-medium dark:text-gray-300">Position</label>
                    <select
                        class="w-full border px-3 py-2 rounded focus:ring focus:ring-blue-300 dark:bg-gray-700 dark:text-gray-200"
                        id="position">
                        <option value="" disabled selected>Select a Position</option>
                        <option value="ITOPS Head">IT Operations Head</option>
                        <option value="Network Admin">Network Admin</option>
                        <option value="Database Admin">Database Admin</option>
                        <option value="IT Security Admin">IT Security Admin</option>
                        <option value="Application Security Admin">Application Security Admin</option>
                        <option value="System Admin">System Admin</option>
                        <option value="Project Manager">Project Manager</option>
                    </select>
                </div>


                <button type="submit"
                    class="bg-green-500 text-white px-5 py-2 rounded-lg font-semibold shadow-md transition duration-300 ease-in-out hover:bg-green-600 hover:scale-105 dark:bg-green-600 dark:hover:bg-green-700">
                    Submit
                </button>
            </form>
        </div>
    </div>


</body>

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

</html>
