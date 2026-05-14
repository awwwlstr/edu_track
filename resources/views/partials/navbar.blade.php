@if (Route::has('login'))
    <header class="w-full lg:max-w-4xl max-w-[335px] text-sm mb-6 not-has-[nav]:hidden">
        <nav class="flex items-center justify-end gap-4">
            @auth
                <a href="{{ url('/dashboard') }}" class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] border-[#19140035] hover:border-[#1915014a] border text-[#1b1b18] dark:border-[#3E3E3A] dark:hover:border-[#62605b] rounded-sm text-sm leading-normal">Dashboard</a>

                @php
                    $role = optional(auth()->user())->role ?? '';
                    $avatar = optional(auth()->user())->avatar ?? null;
                    $name = optional(auth()->user())->name ?? optional(auth()->user())->email ?? 'User';
                @endphp

                <a href="{{ url('/profile') }}" class="inline-flex items-center gap-2 px-2 py-1.5 rounded-sm">
                    @if ($avatar)
                        <img src="{{ asset('storage/' . $avatar) }}" alt="avatar" class="w-8 h-8 rounded-full object-cover border-2 {{ $role === 'admin' ? 'border-indigo-700' : ($role === 'guru' ? 'border-green-700' : 'border-transparent') }}">
                    @else
                        <span class="w-8 h-8 rounded-full flex items-center justify-center font-medium {{ $role === 'admin' ? 'bg-indigo-600 text-white' : ($role === 'guru' ? 'bg-green-600 text-white' : 'bg-gray-300 text-[#1b1b18]') }}">
                            {{ strtoupper(substr($name, 0, 1)) }}
                        </span>
                    @endif
                    <span class="text-sm">{{ $role === 'admin' ? 'Profil Admin' : ($role === 'guru' ? 'Profil Guru' : 'Profil') }}</span>
                </a>

                <form method="POST" action="{{ route('logout') }}" class="inline-block ml-2">
                    @csrf
                    <button type="submit" class="inline-block px-5 py-1.5 text-sm rounded-sm border border-transparent hover:border-[#19140035]">Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] text-[#1b1b18] border border-transparent hover:border-[#19140035] dark:hover:border-[#3E3E3A] rounded-sm text-sm leading-normal">Log in</a>

                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] border-[#19140035] hover:border-[#1915014a] border text-[#1b1b18] dark:border-[#3E3E3A] dark:hover:border-[#62605b] rounded-sm text-sm leading-normal">Register</a>
                @endif
            @endauth
        </nav>
    </header>
@endif
