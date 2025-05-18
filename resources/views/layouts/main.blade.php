<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Vaccination Infantile')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

</head>

<body class="bg-gray-100">
    <!-- Header Section -->
    <header class="bg-[#4A90E2] py-4" x-data="{ isOpen: false }">
        <div class="container mx-auto flex justify-between items-center px-4">
            <!-- Logo -->
            <a href="{{ route('welcome') }}" class="logo text-white text-3xl font-semibold hover:text-gray-300 cursor-pointer transition duration-300 ease-in-out">
                Vaccibaby
            </a>

            <!-- Hamburger Button (Mobile) -->
            <button @click="isOpen = !isOpen" class="md:hidden text-white focus:outline-none">
                <svg x-show="!isOpen" class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                <svg x-show="isOpen" class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="display: none;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <!-- Navbar -->
            <nav :class="{'block': isOpen, 'hidden': !isOpen}" class="md:flex md:items-center md:space-x-6 space-y-4 md:space-y-0 mt-4 md:mt-0 hidden flex-col md:flex-row w-full md:w-auto">
                <ul class="flex flex-col md:flex-row md:space-x-6 w-full md:w-auto">
                    <li>
                        <a href="{{ Request::is('/') ? '#features' : url('/#features') }}" class="text-white hover:text-gray-300">
                            Fonctionnalités
                        </a>
                    </li>
                    <li>
                        <a href="{{ Request::is('/') ? '#conditions' : url('/#conditions') }}" class="text-white hover:text-gray-300">
                            Conditions
                        </a>
                    </li>
                    <li>
                        <a href="{{ Request::is('/') ? '#importance' : url('/#importance') }}" class="text-white hover:text-gray-300">
                            Importance
                        </a>
                    </li>
                    <li>
                        <a href="{{ Request::is('/') ? '#vaccins' : url('/#vaccins') }}" class="text-white hover:text-gray-300">
                            Vaccin
                        </a>
                    </li>

                    @auth
                    @if(auth()->user()->is_admin)
                    <li>
                        <a href="{{ route('statistics') }}" class="text-white bg-blue-600 hover:bg-blue-700 hover:text-gray-300 font-semibold px-4 py-2 rounded-full transition duration-300 ease-in-out">
                            Tableau de bord
                        </a>
                    </li>
                    @elseif(auth()->user()->is_pediatrician)
                    <li>
                        <a href="{{ route('meddash') }}" class="text-white bg-red-600 hover:bg-red-700 hover:text-gray-300 font-semibold px-4 py-2 rounded-full transition duration-300 ease-in-out">
                            Tableau de bord
                        </a>
                    </li>
                    @else
                    <li>
                        <a href="{{ route('pardash') }}" class="text-white bg-green-600 hover:bg-green-700 hover:text-gray-300 font-semibold px-4 py-2 rounded-full transition duration-300 ease-in-out">
                            Dashboard
                        </a>
                    </li>
                    @endif
                    @else
                    <li>
                        <a href="{{ route('login') }}" class="bg-white text-[#4A90E2] px-4 py-2 rounded-full hover:bg-gray-100">
                            Connexion
                        </a>
                    </li>
                    @endauth
                </ul>
            </nav>
        </div>
    </header>


    
    <main>
        @yield('content')
    </main>

    <!-- Footer Section -->
    <footer class="bg-[#4A90E2] py-4 text-center text-white">
        <p>&copy; 2025 Vaccination Infantile. Tous droits réservés.</p>
    </footer>

    <script>
        document.getElementById("backToTop").addEventListener("click", function() {
            window.scrollTo({
                top: 0,
                behavior: "smooth"
            });
        });

        document.getElementById('mobile-menu-button').addEventListener('click', function() {
            var mobileMenu = document.getElementById('mobile-menu');
            mobileMenu.classList.toggle('hidden');
        });
    </script>
</body>
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

</html>