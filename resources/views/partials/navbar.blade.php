<nav class="bg-white border-b border-gray-200 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between">
        <a href="{{ url('/') }}" class="flex items-center gap-2 text-gray-900 font-semibold">
            <i class="fas fa-university text-blue-600"></i>
            <span>JBI University</span>
        </a>

        <div class="flex items-center gap-4">
            @auth
                <span class="text-sm text-gray-600">Hi, {{ auth()->user()->full_name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-sm text-blue-600 hover:text-blue-800">
                        Logout
                    </button>
                </form>
            @endauth

            @guest
                <a href="{{ route('login') }}" class="text-sm text-blue-600 hover:text-blue-800">
                    Login
                </a>
            @endguest
        </div>
    </div>
</nav>
