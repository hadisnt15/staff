<header class="antialiased fixed top-0 left-0 w-full z-50">
    <nav class="bg-emerald-800 border-b border-gray-200 px-4 py-3 fixed left-0 right-0 top-0 z-50">
        <div class="max-w-7xl mx-auto">
            <div class="flex flex-wrap justify-between items-center">
                <div class="flex justify-start items-center">
                    {{-- <a href="https://flowbite.com" class="flex items-center justify-between mr-4">
                    <img
                        src="https://flowbite.s3.amazonaws.com/logo.svg"
                        class="mr-1 h-8 border rounded-full border-emerald-950"
                        alt="Flowbite Logo"
                    /> --}}
                    <span class="self-center text-md md:text-lg font-bold whitespace-nowrap text-white ms-1">StaffPort - </span>
                    <span class="self-center text-xs md:text-sm font-semibold whitespace-nowrap text-white ms-1">Staff Management Portal</span>
                    </a>
                </div>
                @auth
                    <div class="flex items-center lg:order-2">
                        <!-- Dropdown menu -->
                        <button
                            type="button" class="flex mx-3 md:mr-0" id="user-menu-button" aria-expanded="false" data-dropdown-toggle="dropdown">
                            <span class="sr-only">Open user menu</span>
                            <i class="ri-menu-fill w-8 h-8 text-white font-bold py-1"></i>
                        </button>
                        <!-- Dropdown menu -->
                        <div class="hidden z-50 my-4 w-56 text-base list-none bg-gradient-to-br from-emerald-600 to-emerald-900 rounded divide-y divide-gray-100 shadow rounded-xl" id="dropdown">
                            <div class="py-3 px-4">
                                <span class="block text-sm font-semibold text-gray-100">{{ auth()->user()->name }}</span>
                                <span class="block text-sm text-gray-100 truncate">{{ auth()->user()->email }}</span>
                            </div>
                            <ul class="py-1 text-gray-100" aria-labelledby="dropdown">
                                {{-- admin-menu --}}
                                <livewire:header.user />
                                @if(auth()->user()->hasRole(['admin','super_admin']))
                                    <div class="border-t border-gray-200 my-1"></div>
                                    
                                    {{-- admin-menu --}}
                                    <livewire:header.admin />
                                @endif
                            </ul>
                            <ul class="py-1 text-gray-800" aria-labelledby="dropdown">
                                {{-- logout --}}
                                <livewire:header.logout />
                            </ul>
                        </div>
                    </div>
                @endauth
            </div>
        </div>
    </nav>
</header>