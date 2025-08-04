<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600&display=swap" rel="stylesheet">
    <!-- Favicon -->
  <link rel="icon" type="image/svg+xml" href="{{ asset('images/ktl.svg') }}" />
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: { sans: ['Montserrat','ui-sans-serif','system-ui'] },
          colors: { 'ktl-red': '#ed1c24' }
        }
      }
    }
  </script>
  <script src="https://cdn.tailwindcss.com"></script>
  <title>Customer Portal Login</title>
  <style>
    @keyframes popIn {
      from { opacity: 0; transform: scale(0.95); }
      to   { opacity: 1; transform: scale(1); }
    }
    @keyframes slideBg {
      from { background-position: center 0%; }
      to   { background-position: center 100%; }
    }
    body {
      animation: slideBg 30s ease-in-out infinite alternate;
    }
    :focus-visible {
      outline: 2px dashed #ed1c24;
      outline-offset: 3px;
    }
  </style>
</head>
<body class="font-sans flex items-center justify-center min-h-screen"
      style="background:
        url('/images/ktl_background.svg') center/cover no-repeat,
        linear-gradient(135deg, #fafbfc, #eaeef4)">

  <div class="bg-white/60 backdrop-blur-lg rounded-2xl shadow-2xl p-8 sm:p-10 md:p-12 w-full max-w-md animate-[popIn_0.4s_ease-out_both]">
    <!-- Logo + Title -->
    <div class="text-center mb-8">
      <img src="{{ asset('images/ktl_logo.svg') }}"
           alt="Company Logo"
           class="mx-auto h-28 w-auto">
      <p class="mt-4 text-lg text-ktl-red font-semibold">Customer Portal</p>
    </div>

    <form method="POST" action="{{ route('login') }}" novalidate>
      @csrf

      <!-- Email Field -->
      <div class="mb-4">
        <label for="email" class="block text-gray-700 font-medium mb-1">Email</label>
        <div class="relative">
          <input
            id="email"
            type="email"
            name="email"
            required
            autofocus
            placeholder="you@example.com"
            class="w-full pl-12 pr-4 py-2.5 bg-white/30 placeholder-gray-400 rounded-lg
                   focus:bg-white/80 focus:outline-none focus:shadow-md
                   invalid:border-2 invalid:border-red-500 invalid:ring-red-200 invalid:ring-1 transition"
          />
          <div class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                 viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M16 12H8m-4 8h16a2 2 0 002-2V6a2
                       2 0 00-2-2H4a2 2 0 00-2 2v12a2 2 0 002
                       2zm0-4l8-5 8 5" />
            </svg>
          </div>
        </div>
        <p class="mt-1 text-sm text-red-600 hidden" id="email-error">
          Please enter a valid email address.
        </p>
      </div>

      <!-- Password Field -->
      <div class="mb-6">
        <label for="password" class="block text-gray-700 font-medium mb-1">Password</label>
        <div class="relative">
          <input
            id="password"
            type="password"
            name="password"
            required
            placeholder="••••••••"
            class="w-full pl-12 pr-12 py-2.5 bg-white/30 placeholder-gray-400 rounded-lg
                   focus:bg-white/80 focus:outline-none focus:shadow-md transition"
          />
          <div class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none">
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
                  onclick="let p=document.getElementById('password');p.type=p.type==='password'?'text':'password'">
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

      <!-- Remember + Forgot -->
      <div class="flex items-center justify-between mb-6">
        <label class="flex items-center space-x-2 text-gray-700">
          <span class="text-sm">Remember me</span>
          <div class="relative -mt-0.5">
            <input type="checkbox" name="remember" class="sr-only peer" />
            <div class="w-10 h-6 bg-gray-200 rounded-full peer-checked:bg-ktl-red transition"></div>
            <div class="absolute top-0 left-0 w-6 h-6 bg-white rounded-full
                        peer-checked:translate-x-4 transition-transform shadow"></div>
          </div>
        </label>
        <a href="{{ route('password.request') }}" class="text-sm text-ktl-red hover:underline">
          Forgot your password?
        </a>
      </div>

      <!-- Submit -->
      <button
        type="submit"
        style="background-color:#ed1c24"
        class="mt-6 w-full flex items-center justify-center space-x-2
               py-3 text-white font-semibold rounded-lg shadow-lg
               hover:shadow-xl active:scale-95 transition-transform"
      >
        <span>Log In</span>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
             viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M13 7l5 5m0 0l-5 5m5-5H6" />
        </svg>
      </button>
    </form>

    <!-- Register Link -->
    <div class="mt-4 text-center">
      <span class="text-gray-600">Don’t have an account?</span>
      <a href="{{ route('register') }}" class="ml-1 text-ktl-red font-semibold hover:underline">
        Register
      </a>
    </div>

    <!-- Footer -->
    <footer class="mt-8 text-center space-x-4 text-sm text-gray-500">
      <a href="https://knowleslogistics.com/wp-content/uploads/2024/09/Privacy-Policy.pdf" class="hover:underline">Privacy</a>
      <a href="https://knowleslogistics.com/contact-us/" class="hover:underline">Help</a>
    </footer>
  </div>

</body>
</html>
