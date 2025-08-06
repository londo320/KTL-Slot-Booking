<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Switch Recovery</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; }
        .btn { padding: 10px 20px; margin: 10px; background: #007cba; color: white; text-decoration: none; border-radius: 5px; }
        .btn:hover { background: #005a87; }
        .warning { background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 5px; margin: 20px 0; }
    </style>
</head>
<body>
    <h1>🔄 User Switch Recovery</h1>
    
    <div class="warning">
        <strong>Locked Out?</strong> Use the buttons below to recover admin access.
    </div>

    <h3>Recovery Options:</h3>
    
    <p><strong>Option 1: Emergency Switch Back (GET)</strong></p>
    <a href="/emergency-switch-back" class="btn">🚨 Emergency Switch Back</a>
    
    <p><strong>Option 2: Regular Switch Back (POST)</strong></p>
    <form action="/switch-back" method="POST" style="display: inline;">
        @csrf
        <button type="submit" class="btn">🔙 Switch Back to Admin</button>
    </form>
    
    <p><strong>Option 3: Logout</strong></p>
    <form action="/logout" method="POST" style="display: inline;">
        @csrf
        <button type="submit" class="btn" style="background: #dc3545;">🚪 Logout</button>
    </form>

    <hr>
    
    <h3>Current Status:</h3>
    @auth
        <p><strong>Logged in as:</strong> {{ auth()->user()->name }} ({{ auth()->user()->email }})</p>
        <p><strong>Roles:</strong> {{ auth()->user()->roles->pluck('name')->join(', ') ?: 'No roles' }}</p>
        @if(session('original_admin_id'))
            <p><strong>Original Admin ID:</strong> {{ session('original_admin_id') }}</p>
            <p><strong>Switched At:</strong> {{ session('switched_at') }}</p>
        @else
            <p><em>No user switching session detected</em></p>
        @endif
    @else
        <p><em>Not logged in</em></p>
    @endauth
</body>
</html>