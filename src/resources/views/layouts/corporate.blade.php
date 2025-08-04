<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'Welcome')</title>
  <!-- Your compiled CSS (Tailwind or Bootstrap) -->
  <link href="{{ mix('css/app.css') }}" rel="stylesheet">
</head>
<body class="min-h-screen bg-cover bg-center" style="background-image: url('/images/warehouse-hero.jpg')">
  <div class="flex items-center justify-center min-h-screen bg-black bg-opacity-50">
    @yield('content')
  </div>

  <footer class="absolute bottom-0 w-full text-center text-sm text-white opacity-75 p-4">
    © {{ date('Y') }} Knowles Logistics · <a href="/privacy" class="underline">Privacy</a> · <a href="/contact" class="underline">Contact</a>
  </footer>
</body>
</html>
