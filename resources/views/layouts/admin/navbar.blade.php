<!-- Sidebar -->
<nav
    class="fixed top-10 left-5 h-[85vh] w-40 bg-white dark:bg-gray-800 shadow-lg rounded-xl p-4 flex flex-col items-center border border-gray-200 dark:border-gray-700">
    <img src="{{ asset('images/itopslogo.png') }}" alt="Your Logo" class="h-[80px] w-auto mb-6">
    <div class="flex flex-col space-y-4 w-full text-center">
        <a href="{{route('admin.dashboard')}}"
            class="{{ request()->routeIs('admin.dashboard') ? 'bg-gray-200 dark:bg-gray-700' : '' }} text-gray-800 dark:text-gray-200 text-xs font-medium hover:bg-gray-200 dark:hover:bg-gray-700 p-2 rounded-md">Dashboard</a>
        <a href="{{route('admin.users')}}"
            class="{{ request()->routeIs('admin.users') ? 'bg-gray-200 dark:bg-gray-700' : '' }} text-gray-800 dark:text-gray-200 text-xs font-medium hover:bg-gray-200 dark:hover:bg-gray-700 p-2 rounded-md">Users</a>
        <a href="{{route('admin.shifts')}}"
            class="{{ request()->routeIs('admin.shifts') ? 'bg-gray-200 dark:bg-gray-700' : '' }} text-gray-800 dark:text-gray-200 text-xs font-medium hover:bg-gray-200 dark:hover:bg-gray-700 p-2 rounded-md">Team
            Shifts</a>
        <a href="{{route('admin.eod')}}"
            class="{{ request()->routeIs('admin.eod') ? 'bg-gray-200 dark:bg-gray-700' : '' }} text-gray-800 dark:text-gray-200 text-xs font-medium hover:bg-gray-200 dark:hover:bg-gray-700 p-2 rounded-md">End
            of Day Logs</a>
        <a href="{{route('admin.eodreports')}}"
            class="{{ request()->routeIs('admin.eodreports') ? 'bg-gray-200 dark:bg-gray-700' : '' }} text-gray-800 dark:text-gray-200 text-xs font-medium hover:bg-gray-200 dark:hover:bg-gray-700 p-2 rounded-md">EOD Reports</a>
    </div>
    <div class="mt-auto flex flex-col items-center w-full">
        <img src="{{ Auth::user()->avatar }}" alt="User Avatar"
            class="w-10 h-10 rounded-full border border-gray-300 dark:border-gray-600 mb-3">
        <p class="text-gray-800 dark:text-gray-200 text-sm font-medium text-center">{{ Auth::user()->name }}</p>
        <p class="text-gray-500 dark:text-gray-400 text-xs">{{ Auth::user()->position }}</p>
        <p class="text-gray-500 dark:text-gray-400 text-xs">{{ Auth::user()->role }}</p>

        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="w-full mt-3">
            @csrf
            <button type="submit"
                class="w-full px-3 py-1.5 bg-gradient-to-r from-red-500 to-red-600 text-white text-sm font-semibold rounded-lg shadow-md hover:from-red-600 hover:to-red-700 focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-offset-2 transition-all duration-200"
                onclick="clearLoadingScreenSession()">
                <i class="fa-solid fa-right-from-bracket mr-2"></i>
                Logout
            </button>
        </form>
    </div>
</nav>

<script>
    function clearLoadingScreenSession() {
        sessionStorage.removeItem("loadingScreenShown"); // Clear session storage on logout
    }
</script>