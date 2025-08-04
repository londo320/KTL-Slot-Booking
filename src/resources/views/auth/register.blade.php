{{-- resources/views/auth/register.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>Create Account – {{ config('app.name', 'Laravel') }}</title>

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600&display=swap" rel="stylesheet">

  <!-- Tailwind + Vite -->
  @vite(['resources/css/app.css', 'resources/js/app.js'])

  <style>
    @keyframes popIn {
      from { opacity:0; transform: scale(0.95); }
      to   { opacity:1; transform: scale(1);    }
    }
    @keyframes slideBg {
      from { background-position: center 0%; }
      to   { background-position: center 100%; }
    }
    body {
      animation: slideBg 30s ease-in-out infinite alternate;
    }
  </style>
</head>
<body class="font-sans antialiased flex items-center justify-center min-h-screen"
      style="background:
        url('/images/ktl_background.svg') center/cover no-repeat,
        linear-gradient(135deg,#fafbfc,#eaeef4)">

  <div class="w-full max-w-md p-8 sm:p-10 bg-white/60 backdrop-blur-lg
              rounded-2xl shadow-2xl animate-[popIn_0.4s_ease-out_both]">

    {{-- Logo + Title --}}
    <div class="text-center mb-8">
      <img src="{{ asset('images/ktl_logo.svg') }}" alt="Knowles Logo" class="mx-auto h-28 w-auto">
      <p class="mt-4 text-lg text-ktl-red font-semibold">Create Account</p>
    </div>

    <form method="POST" action="{{ route('register') }}" novalidate>
      @csrf

      {{-- Name --}}
      <div class="mb-4">
        <label for="name" class="block mb-1 font-medium text-gray-700">
          Name
        </label>
        <div class="relative">
          <input id="name" name="name" type="text" required autofocus
                 value="{{ old('name') }}"
                 placeholder="Your full name"
                 class="w-full pl-12 pr-4 py-2.5 bg-white/30 placeholder-gray-400
                        rounded-lg focus:bg-white/80 focus:outline-none focus:shadow-md
                        transition @error('name') border-2 border-red-500 @enderror">
          <div class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none">
            {{-- user icon --}}
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                 viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M5.121 17.804A13.937 13.937 0 0112 15c2.5 0
                       4.847.712 6.879 1.804M15 11a3 3 0 11-6 0
                       3 3 0 016 0z"/>
            </svg>
          </div>
        </div>
        @error('name')
          <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
      </div>

      {{-- Email --}}
      <div class="mb-4">
        <label for="email" class="block mb-1 font-medium text-gray-700">
          Email Address
        </label>
        <div class="relative">
          <input id="email" name="email" type="email" required
                 value="{{ old('email') }}"
                 placeholder="you@example.com"
                 class="w-full pl-12 pr-4 py-2.5 bg-white/30 placeholder-gray-400
                        rounded-lg focus:bg-white/80 focus:outline-none focus:shadow-md
                        transition @error('email') border-2 border-red-500 @enderror">
          <div class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none">
            {{-- mail icon --}}
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                 viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M16 12H8m-4 8h16a2 2 0 002-2V6a2
                       2 0 00-2-2H4a2 2 0 00-2 2v12a2 2 0 002
                       2zm0-4l8-5 8 5" />
            </svg>
          </div>
        </div>
        @error('email')
          <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
      </div>

      {{-- Password --}}
      <div class="mb-4">
        <label for="password" class="block mb-1 font-medium text-gray-700">
          Password
        </label>
        <div class="relative">
          <input id="password" name="password" type="password" required
                 placeholder="••••••••"
                 class="w-full pl-12 pr-12 py-2.5 bg-white/30 placeholder-gray-400
                        rounded-lg focus:bg-white/80 focus:outline-none focus:shadow-md
                        transition @error('password') border-2 border-red-500 @enderror">
          <div class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none">
            {{-- lock icon --}}
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                 viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 11c1.656 0 3-1.344
                       3-3V5a3 3 0 00-6 0v3c0
                       1.656 1.344 3 3 3zm6 2H6a2
                       2 0 00-2 2v6a2 2 0 002
                       2h12a2 2 0 002-2v-6a2 2
                       0 00-2-2z" />
            </svg>
          </div>
          <button type="button"
                  class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 focus:outline-none"
                  onclick="let p=document.getElementById('password'); p.type = p.type==='password'?'text':'password'">
            {{-- eye icon --}}
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                 viewBox="0 0 24 24" stroke="currentColor">
              <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 
                       0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 
                       7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
          </button>
        </div>
        @error('password')
          <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
      </div>

      {{-- Confirm Password --}}
      <div class="mb-6">
        <label for="password_confirmation" class="block mb-1 font-medium text-gray-700">
          Confirm Password
        </label>
        <div class="relative">
          <input id="password_confirmation" name="password_confirmation"
                 type="password" required placeholder="Retype your password"
                 class="w-full pl-12 pr-12 py-2.5 bg-white/30 placeholder-gray-400
                        rounded-lg focus:bg-white/80 focus:outline-none focus:shadow-md
                        transition">
          <div class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none">
            {{-- lock icon --}}
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                 viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 11c1.656 0 3-1.344
                       3-3V5a3 3 0 00-6 0v3c0
                       1.656 1.344 3 3 3zm6 2H6a2
                       2 0 00-2 2v6a2 2 0 002
                       2h12a2 2 0 002-2v-6a2 2
                       0 00-2-2z" />
            </svg>
          </div>
          <button type="button"
                  class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 focus:outline-none"
                  onclick="let p=document.getElementById('password_confirmation'); p.type = p.type==='password'?'text':'password'">
            {{-- eye icon --}}
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                 viewBox="0 0 24 24" stroke="currentColor">
              <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 
                       0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 
                       7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
          </button>
        </div>
      </div>

      {{-- Already registered + Register button --}}
      <div class="flex items-center justify-between mb-2">
        <a href="{{ route('login') }}"
           class="text-sm underline text-gray-700 hover:text-gray-900">
          Already registered?
        </a>
        <button type="submit"
                class="flex items-center space-x-2 px-6 py-3 bg-ktl-red text-white 
                       font-semibold rounded-lg shadow-lg hover:shadow-xl 
                       active:scale-95 transition-transform">
          <span>Register</span>
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
               viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M13 7l5 5m0 0l-5 5m5-5H6" />
          </svg>
        </button>
      </div>
    </form>

    {{-- Footer links --}}
  <footer class="mt-8 flex justify-between px-4 text-sm text-gray-500">
      <a href="https://knowleslogistics.com/wp-content/uploads/2024/09/Privacy-Policy.pdf" class="hover:underline">Privacy</a>
      <a href="https://knowleslogistics.com/contact-us/" class="hover:underline">Help</a>
    </footer>
  </div>
</body>
</html>
