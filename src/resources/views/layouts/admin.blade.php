<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin – @yield('title', 'Dashboard')</title>
    <!-- TailwindCSS from CDN -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.x/dist/tailwind.min.css" rel="stylesheet">
    <style>
        /* Optional custom styles for admin panel */
        .navbar { box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); }
    </style>
</head>
<body class="bg-gray-100 font-sans">

    <!-- Navbar Section -->
    <nav class="bg-white shadow-md p-4 mb-6 flex justify-between items-center">
        <div class="flex items-center">
            <a href="{{ route('admin.dashboard') }}" class="text-2xl font-bold text-blue-600 mr-6">
                Admin Panel
            </a>
            <a href="{{ route('admin.customer-depot-products.index') }}" class="mr-6 text-lg text-blue-600 hover:text-blue-800">
                Rules
            </a>
            <a href="{{ route('admin.bookings.index') }}" class="text-lg text-blue-600 hover:text-blue-800">
                Bookings
            </a>
        </div>
        <div>
            <!-- Admin User Dropdown (if required) -->
            <div class="relative">
                <button class="text-gray-700">
                    Admin <i class="fas fa-caret-down"></i>
                </button>
                <!-- Dropdown content can go here -->
            </div>
        </div>
    </nav>

    <!-- Main Content Section -->
    <main class="container mx-auto px-4">
        @yield('content')
    </main>

    <!-- TailwindJS & FontAwesome (for icons, e.g. dropdown) -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>
</html>
