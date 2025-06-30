<nav x-data="{ scrollingDown: false, open: false }"
    x-init="
       let lastScroll = window.scrollY;
       window.addEventListener('scroll', () => {
          scrollingDown = window.scrollY > lastScroll && window.scrollY > 50;
          lastScroll = window.scrollY;
       });
    "
    :class="scrollingDown ? 'fixed top-0 left-0 right-0 z-50 shadow-md' : 'relative'"
    class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700 transition-all duration-500 ease-in-out transform">

    <!-- Container -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center h-20 transition-all duration-500 ease-in-out">

    <!-- Logo -->
    <div class="flex items-center space-x-3">
       <a href="{{ route('dashboard') }}" class="flex items-center">
          <img src="images/itopslogo.png" alt="Your Logo" class="h-12 w-auto transition-transform duration-500 ease-in-out">
          <span class="text-xl font-semibold text-gray-800 dark:text-gray-200 ml-2">IT Operations</span>
       </a>
    </div>

    <!-- Navigation Links -->
    <div class="hidden lg:flex space-x-6">
       <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="flex flex-col items-center relative group">
          <i class="fa-solid fa-house"></i>
          <span>{{ __('Dashboard') }}</span>
       </x-nav-link>
       <x-nav-link :href="route('eod')" :active="request()->routeIs('eod')" class="flex flex-col items-center">
          <i class="fa-solid fa-circle-check"></i>
          <span>{{ __('End of Day') }}</span>
       </x-nav-link>
       @if (in_array(Auth::user()->role ?? '', ['manager', 'admin']))
          <x-nav-link :href="route('schedule')" :active="request()->routeIs('schedule')" class="flex flex-col items-center">
             <i class="fa-solid fa-calendar"></i>
             <span>{{ __('Shift Schedule') }}</span>
          </x-nav-link>
       @endif
       <x-nav-link :href="route('sinking.index')" :active="request()->routeIs('sinking.index')" class="flex flex-col items-center">
          <i class="fa-solid fa-coins"></i>
          <span>{{ __('Sinking Fund') }}</span>
       </x-nav-link>
    </div>

       <!-- User Dropdown -->
       <div class="relative">
          <button @click="open = !open" class="flex items-center space-x-2 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none">
             <img src="{{ Auth::user()->avatar }}" alt="User Avatar" class="w-10 h-10 rounded-full transition-transform duration-500 ease-in-out">
             <div class="text-sm">
                <span>{{ Auth::user()->name }}</span>
                <br>
                <span class="text-xs text-gray-500 dark:text-gray-400">{{ Auth::user()->position ?? 'No Position Set' }}</span>
             </div>
             <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
             </svg>
          </button>
          <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-lg z-50">
             <form id="logout-form" method="POST" action="{{ route('logout') }}">
                @csrf
                <x-dropdown-link href="#" onclick="handleLogout(event)">
                    {{ __('Log Out') }}
                </x-dropdown-link>
             </form>
          </div>
       </div>
    </div>
</nav>

<script>
    function handleLogout(event) {
       event.preventDefault();
       sessionStorage.removeItem("loadingScreenShown"); // Clear session storage
       document.getElementById('logout-form').submit(); // Submit the form
    }
</script>
