<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Access Denied | JBI University</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: #334155;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }
        .error-card {
            max-width: 540px;
            width: 100%;
            padding: 44px 36px;
            background-color: #ffffff;
            border-radius: 16px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35);
            text-align: center;
        }
        .error-icon-wrap {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 88px;
            height: 88px;
            border-radius: 50%;
            background-color: #fef2f2;
            color: #ef4444;
            margin-bottom: 24px;
        }
        .error-badge {
            display: inline-block;
            background-color: #fee2e2;
            color: #991b1b;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 4px 10px;
            border-radius: 9999px;
            margin-bottom: 12px;
        }
        .error-title {
            font-size: 26px;
            font-weight: 700;
            color: #0f172a;
            margin: 0 0 12px 0;
        }
        .error-message {
            font-size: 15px;
            color: #64748b;
            margin: 0 0 24px 0;
            line-height: 1.6;
        }
        .user-context {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px 16px;
            margin-bottom: 24px;
            font-size: 13px;
            color: #475569;
            text-align: left;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .user-context strong {
            color: #0f172a;
        }
        .actions {
            display: flex;
            justify-content: center;
            gap: 10px;
            flex-wrap: wrap;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background-color: #2563eb;
            color: #ffffff;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.2s ease;
            border: 1px solid transparent;
            cursor: pointer;
        }
        .btn:hover {
            background-color: #1d4ed8;
            transform: translateY(-1px);
        }
        .btn-outline {
            background-color: #ffffff;
            color: #334155;
            border: 1px solid #cbd5e1;
        }
        .btn-outline:hover {
            background-color: #f8fafc;
            color: #0f172a;
            border-color: #94a3b8;
        }
        .btn-danger-outline {
            background-color: transparent;
            color: #ef4444;
            border: 1px solid #fecaca;
        }
        .btn-danger-outline:hover {
            background-color: #fef2f2;
            color: #dc2626;
        }
        .footer-note {
            margin-top: 24px;
            font-size: 12px;
            color: #94a3b8;
        }
    </style>
</head>
<body>
    <div class="error-card">
        <div class="error-icon-wrap">
            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                <line x1="9.5" y1="9" x2="14.5" y2="14"/>
                <line x1="14.5" y1="9" x2="9.5" y2="14"/>
            </svg>
        </div>

        <div>
            <span class="error-badge">HTTP 403 Forbidden</span>
            <h1 class="error-title">Access Denied</h1>
            <p class="error-message">
                {{ (!empty($exception) && $exception->getMessage()) ? $exception->getMessage() : 'You do not have the required permissions or role privileges to view or perform actions in this area.' }}
            </p>
        </div>

        @if(auth()->check())
            <div class="user-context">
                <div>
                    <div>Signed in as <strong>{{ auth()->user()->full_name }}</strong></div>
                    <small style="color: #64748b;">Current Role: {{ auth()->user()->role_name }}</small>
                </div>
                <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit" class="btn btn-danger-outline" style="padding: 6px 12px; font-size: 12px;">Sign Out</button>
                </form>
            </div>
        @endif

        <div class="actions">
            <button onclick="window.history.length > 1 ? window.history.back() : window.location.href='{{ auth()->check() ? route('dashboard') : url('/') }}'" class="btn btn-outline">
                ← Go Back
            </button>
            <a href="{{ auth()->check() ? route('dashboard') : url('/') }}" class="btn">
                Dashboard
            </a>
            @if(Route::has('support.index'))
                <a href="{{ route('support.index') }}" class="btn btn-outline">
                    Contact Support
                </a>
            @endif
        </div>

        <div class="footer-note">
            If you need elevated access for this area, contact your Head of Department, Dean, or the JBI Registrar.
        </div>
    </div>
</body>
</html>
