<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - LogisticsCtrl</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@2.47.0/tabler-icons.min.css">
    <style>
        :root {
            --violet-400: #f472b6;
            --violet-500: #ec4899;
            --violet-600: #db2777;
            --violet-700: #be185d;
            --bg-color: #0f172a;
            --card-bg: #1e293b;
            --text-primary: #f8fafc;
            --text-secondary: #94a3b8;
            --border-color: #334155;
        }
        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            background: var(--bg-color);
            color: var(--text-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }
        .auth-container {
            width: 100%;
            max-width: 400px;
            padding: 40px;
            background: var(--card-bg);
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.5);
            border: 1px solid var(--border-color);
            text-align: center;
        }
        .logo-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, var(--violet-500), var(--violet-700));
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 16px;
        }
        .title { font-size: 24px; font-weight: 700; margin: 0 0 8px; }
        .subtitle { color: var(--text-secondary); font-size: 14px; margin-bottom: 30px; }
        .form-group { text-align: left; margin-bottom: 16px; }
        .form-group label { display: block; font-size: 13px; font-weight: 500; margin-bottom: 6px; color: var(--text-secondary); }
        .form-control {
            width: 100%;
            padding: 12px 16px;
            background: #0f172a;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            color: white;
            font-family: 'Inter', sans-serif;
            box-sizing: border-box;
            outline: none;
            transition: border-color 0.2s;
        }
        .form-control:focus { border-color: var(--violet-500); }
        .btn-primary {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, var(--violet-600), var(--violet-700));
            border: none;
            border-radius: 8px;
            color: white;
            font-weight: 600;
            font-size: 15px;
            cursor: pointer;
            transition: opacity 0.2s;
            margin-top: 10px;
        }
        .btn-primary:hover { opacity: 0.9; }
        .error-msg { color: #ef4444; font-size: 12px; margin-top: 5px; display: block; }
        .bottom-links { margin-top: 24px; font-size: 13px; color: var(--text-secondary); }
        .bottom-links a { color: var(--violet-400); text-decoration: none; font-weight: 500; }
        .bottom-links a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="logo-icon"><i class="ti ti-world"></i></div>
        <h1 class="title">Create Account</h1>
        <p class="subtitle">Join LogisticsCtrl today</p>

        <form method="POST" action="{{ route('register') }}">
            @csrf
            
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required autofocus>
                @error('name') <span class="error-msg">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                @error('email') <span class="error-msg">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
                @error('password') <span class="error-msg">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="password_confirmation" class="form-control" required>
            </div>

            <button type="submit" class="btn-primary">Sign Up</button>
        </form>

        <div class="bottom-links">
            Already have an account? <a href="{{ route('login') }}">Sign In</a>
        </div>
    </div>
</body>
</html>
