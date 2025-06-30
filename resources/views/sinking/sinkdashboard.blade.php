<x-app-layout>

    <head>
        <title>ITOPS Sinking</title>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
        <script src="{{ asset('js/schedule.js') }}"></script>
        <meta name="csrf-token" content="{{ csrf_token() }}">
    </head>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight text-center">
            {{ __('ITOPS Sinking Fund') }}
        </h2>
    </x-slot>

    <!-- Floating Buttons Container (Properly Stacked & Aligned) -->
    <div class="fixed bottom-20 right-6 flex flex-col items-end space-y-4">
        <!-- Dark Mode Toggle Button (Top) -->
        <button id="theme-toggle" class="bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 w-12 h-12 flex items-center justify-center
               rounded-full shadow-md transition cursor-pointer">
            <i id="theme-toggle-icon" class="fa-solid fa-moon"></i>
        </button>
    </div>

    <div class="container">
        <div class="text-center mt-8">
            <h2 class="text-5xl font-bold text-gray-800 dark:text-gray-200 moving-text">
                Coming Soon!
            </h2>
        </div>
    </div>

    <style>
        @keyframes moveLetters {
            0% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            50% { transform: translateX(5px); }
            75% { transform: translateX(-5px); }
            100% { transform: translateX(0); }
        }

        .moving-text {
            display: inline-block;
            animation: moveLetters 1.5s infinite ease-in-out;
        }
    </style>


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
