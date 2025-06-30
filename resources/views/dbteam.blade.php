<x-app-layout>
    <x-loading-screen />

    <head>
        <title>DB Checklist</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>

    <x-slot name="header">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100 tracking-wide text-center w-full">
            {{ __('Database Team Daily Checklist') }}
        </h2>
    </x-slot>

    <div class="flex items-center justify-center min-h-[60vh] px-4 mt-6">
        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-lg p-8 flex flex-col gap-6 w-full max-w-md">

            <a href="{{ url('/export/mysql-status') }}"
                class="flex items-center justify-center gap-2 px-6 py-3 bg-blue-500 hover:bg-blue-600 text-white font-semibold rounded-lg shadow-md transition duration-200 text-lg">
                <i class="fas fa-database"></i>
                MySQL Status
            </a>

            <a href="{{ url('/export/replication-status') }}"
                class="flex items-center justify-center gap-2 px-6 py-3 bg-green-500 hover:bg-green-600 text-white font-semibold rounded-lg shadow-md transition duration-200 text-lg">
                <i class="fas fa-sync-alt"></i>
                MySQL Replication Status
            </a>

            <a href="{{ url('/export/home-utilization') }}"
                class="flex items-center justify-center gap-2 px-6 py-3 bg-yellow-500 hover:bg-yellow-600 text-white font-semibold rounded-lg shadow-md transition duration-200 text-lg">
                <i class="fas fa-hdd"></i>
                MYSQL Disk Storage
            </a>

            <div class="mt-4 text-center text-sm text-gray-600 dark:text-gray-300">
                If the first click doesn't work, just click again to download the PDF file.
            </div>

        </div>
    </div>

</x-app-layout>
