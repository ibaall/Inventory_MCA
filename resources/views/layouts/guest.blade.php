<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: radial-gradient(circle at 10% 20%, rgb(15, 23, 42) 0%, rgb(30, 27, 75) 100.2%);
            color: #f8fafc;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            overflow-x: hidden;
            position: relative;
        }

        /* Decorative animated background orbs */
        .orb {
            position: absolute;
            width: 500px;
            height: 500px;
            border-radius: 50%;
            filter: blur(120px);
            opacity: 0.15;
            z-index: 0;
            pointer-events: none;
            animation: orb-float 20s infinite ease-in-out alternate;
        }
        .orb-1 {
            background: #6366f1;
            top: -150px;
            left: -150px;
        }
        .orb-2 {
            background: #a855f7;
            bottom: -150px;
            right: -150px;
            animation-delay: -10s;
        }

        @keyframes orb-float {
            0% { transform: translate(0, 0) scale(1); }
            100% { transform: translate(40px, 30px) scale(1.1); }
        }

        .login-container {
            background: rgba(30, 41, 59, 0.45);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 1.5rem;
            padding: 3rem 2.5rem;
            width: 100%;
            max-width: 440px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            z-index: 10;
            position: relative;
            box-sizing: border-box;
            transition: all 0.3s ease;
        }

        .login-container:hover {
            border-color: rgba(255, 255, 255, 0.12);
            box-shadow: 0 30px 60px -10px rgba(0, 0, 0, 0.6), 0 0 40px rgba(99, 102, 241, 0.1);
        }

        .header-text {
            font-size: 1.85rem;
            font-weight: 700;
            letter-spacing: -0.025em;
            margin-bottom: 0.5rem;
            text-align: center;
            background: linear-gradient(135deg, #ffffff 0%, #cbd5e1 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .brand-highlight {
            background: linear-gradient(135deg, #818cf8 0%, #6366f1 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-weight: 800;
        }

        .subtitle-text {
            font-size: 0.9rem;
            color: #94a3b8;
            text-align: center;
            margin-bottom: 2rem;
        }

        .form-area {
            margin-top: 1rem;
        }

        /* Direct styling for child elements within login-container to ensure gorgeous styling */
        
        /* Labels */
        .login-container label, 
        .login-container .text-gray-1000, 
        .login-container .text-gray-800,
        .login-container .text-gray-700 {
            display: block;
            font-size: 0.85rem !important;
            font-weight: 600 !important;
            color: #cbd5e1 !important;
            margin-bottom: 0.5rem !important;
            text-transform: none;
            letter-spacing: 0.025em;
        }

        /* Checkbox Label Fix */
        .login-container label.inline-flex,
        .login-container label[for="remember_me"] {
            display: inline-flex !important;
            align-items: center;
            margin-bottom: 0;
            cursor: pointer;
        }
        
        .login-container label.inline-flex span,
        .login-container label[for="remember_me"] span {
            color: #94a3b8 !important;
            font-weight: 500 !important;
            font-size: 0.85rem !important;
        }

        /* Text inputs */
        .login-container input[type="text"],
        .login-container input[type="email"],
        .login-container input[type="password"] {
            width: 100% !important;
            background: rgba(15, 23, 42, 0.6) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            color: #f8fafc !important;
            border-radius: 0.75rem !important;
            padding: 0.75rem 1rem !important;
            font-size: 0.95rem !important;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
            box-sizing: border-box !important;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.2) !important;
        }

        .login-container input[type="text"]:focus,
        .login-container input[type="email"]:focus,
        .login-container input[type="password"]:focus {
            outline: none !important;
            border-color: #6366f1 !important;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.25), inset 0 2px 4px rgba(0,0,0,0.1) !important;
            background: rgba(15, 23, 42, 0.8) !important;
        }

        /* Checkboxes */
        .login-container input[type="checkbox"] {
            appearance: none;
            -webkit-appearance: none;
            width: 1.1rem;
            height: 1.1rem;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 0.35rem;
            background: rgba(15, 23, 42, 0.6);
            display: inline-grid;
            place-content: center;
            cursor: pointer;
            transition: all 0.2s;
            outline: none;
            margin-right: 0.5rem;
        }

        .login-container input[type="checkbox"]:checked {
            background: #6366f1;
            border-color: #6366f1;
        }

        .login-container input[type="checkbox"]:checked::before {
            content: "";
            width: 0.55rem;
            height: 0.55rem;
            transform: scale(1);
            background-color: white;
            clip-path: polygon(14% 44%, 0 65%, 50% 100%, 100% 16%, 80% 0%, 43% 62%);
        }

        .login-container input[type="checkbox"]:focus {
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.25);
        }

        /* Links */
        .login-container a {
            font-size: 0.85rem !important;
            color: #94a3b8 !important;
            text-decoration: none !important;
            transition: color 0.2s !important;
            font-weight: 500;
        }

        .login-container a:hover {
            color: #a5b4fc !important;
        }

        /* Primary Button */
        .login-container button[type="submit"],
        .login-container .btn-primary {
            display: flex !important;
            justify-content: center !important;
            align-items: center !important;
            width: 100% !important;
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%) !important;
            border: none !important;
            border-radius: 0.75rem !important;
            color: white !important;
            font-weight: 600 !important;
            font-size: 0.95rem !important;
            padding: 0.75rem 1.5rem !important;
            cursor: pointer !important;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3) !important;
            text-transform: none !important;
            letter-spacing: 0.05em !important;
            margin-top: 1rem !important;
        }

        .login-container button[type="submit"]:hover,
        .login-container .btn-primary:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 8px 20px rgba(79, 70, 229, 0.45) !important;
            background: linear-gradient(135deg, #818cf8 0%, #6366f1 100%) !important;
        }

        .login-container button[type="submit"]:active,
        .login-container .btn-primary:active {
            transform: translateY(0) !important;
        }

        /* Validation errors */
        .login-container ul.text-red-600,
        .login-container .text-red-600,
        .login-container .text-red-400,
        .login-container .error-text {
            color: #f87171 !important;
            font-size: 0.8rem !important;
            margin-top: 0.35rem !important;
            list-style: none !important;
            padding-left: 0 !important;
        }
        
        .login-container ul.text-red-600 li {
            margin-bottom: 0.15rem;
        }

        /* Spacing for groups */
        .login-container .mt-4 {
            margin-top: 1.25rem !important;
        }
        
        .login-container .mb-4 {
            margin-bottom: 1.25rem !important;
        }

        .login-container .flex.items-center.justify-end {
            display: flex !important;
            flex-direction: column-reverse !important;
            align-items: center !important;
            gap: 1rem !important;
            margin-top: 1.5rem !important;
        }

        @media(min-width: 480px) {
            .login-container .flex.items-center.justify-end {
                flex-direction: row !important;
                justify-content: space-between !important;
            }
        }
    </style>
</head>
<body class="font-sans antialiased">

    <!-- Ambient background orbs -->
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>

    <div class="login-container">

        <div class="header-text">
            Selamat Datang di <span class="brand-highlight">PT MCA</span>
        </div>
        <div class="subtitle-text">
            Sistem Manajemen Transaksi & Inventaris
        </div>

        <div class="form-area">
            {{ $slot }}
        </div>

        <div class="text-center text-sm mt-6 text-gray-500" style="margin-top: 2rem; font-size: 0.8rem; color: #64748b; text-align: center;">
            &copy; {{ date('Y') }} PT MCA. All rights reserved.
        </div>

    </div>

</body>
</html>
