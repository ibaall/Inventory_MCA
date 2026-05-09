<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            background-color: #f9fafb;
        }

        .login-container {
            background: #ffffff;
            padding: 3rem 2rem;
            border-radius: 1rem;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.05);
            width: 100%;
            max-width: 420px;
            transition: 0.3s ease-in-out;
        }

        .login-container:hover {
            box-shadow: 0 25px 40px rgba(0, 0, 0, 0.1);
        }

        .header-text {
            font-size: 1.75rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            color: #333;
            text-align: center;
        }

        .brand-highlight {
            color: #6366f1;
        }

        .custom-link {
            color: #6b7280;
            text-decoration: none;
        }

        .custom-link:hover {
            color: #4f46e5;
        }

        .form-area {
            margin-top: 1.5rem;
        }
    </style>
</head>
<body class="font-sans text-gray-900 antialiased">

    <div class="min-h-screen flex items-center justify-center bg-gray-100">
        <div class="login-container">

            <div class="header-text">
               Selamat Datang di <span class="brand-highlight">PT MCA</span>

            </div>

            <div class="form-area">
                {{ $slot }}
            </div>

            <div class="text-center text-sm mt-6 text-gray-500">
                &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
            </div>

        </div>
    </div>

</body>
</html>
