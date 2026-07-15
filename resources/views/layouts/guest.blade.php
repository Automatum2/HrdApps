<!DOCTYPE html>
<html class="light" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'HRDApps Management System')</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Hanken+Grotesk:wght@400;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    
    <!-- Material Symbols -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            background: linear-gradient(135deg, #f8f9ff 0%, #dce9ff 50%, #f0f4ff 100%);
            background-size: 400% 400%;
            animation: bgGradientShift 12s ease infinite;
            min-height: 100vh;
            overflow: hidden;
        }
        @keyframes bgGradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        .login-card {
            box-shadow:
                0px 10px 15px -3px rgba(0, 0, 0, 0.05),
                0px 4px 6px -2px rgba(0, 0, 0, 0.02),
                0 0 60px -20px rgba(0, 80, 203, 0.08);
            backdrop-filter: blur(8px);
            transition: box-shadow 0.4s ease, transform 0.4s ease;
        }
        .login-card:hover {
            box-shadow:
                0px 20px 40px -8px rgba(0, 0, 0, 0.08),
                0px 8px 16px -4px rgba(0, 0, 0, 0.03),
                0 0 80px -20px rgba(0, 80, 203, 0.12);
            transform: translateY(-2px);
        }
        .input-focus-ring:focus-within {
            box-shadow: 0 0 0 4px rgba(0, 80, 203, 0.15), 0 0 20px -4px rgba(0, 80, 203, 0.1);
            border-color: #0050cb;
        }
        /* Floating Orbs Background */
        .floating-orb {
            position: fixed;
            border-radius: 50%;
            filter: blur(60px);
            opacity: 0.15;
            pointer-events: none;
            z-index: 0;
        }
        .orb-1 {
            width: 300px; height: 300px;
            background: #0050cb;
            top: -100px; right: -50px;
            animation: orbFloat1 8s ease-in-out infinite;
        }
        .orb-2 {
            width: 250px; height: 250px;
            background: #006645;
            bottom: -80px; left: -60px;
            animation: orbFloat2 10s ease-in-out infinite;
        }
        .orb-3 {
            width: 180px; height: 180px;
            background: #0066ff;
            top: 50%; left: 10%;
            animation: orbFloat3 12s ease-in-out infinite;
        }
        @keyframes orbFloat1 {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(-30px, 40px) scale(1.1); }
        }
        @keyframes orbFloat2 {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(40px, -30px) scale(1.15); }
        }
        @keyframes orbFloat3 {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(-20px, -40px) scale(0.9); }
        }
        /* Logo Shimmer */
        @keyframes logoShine {
            0% { filter: brightness(1); }
            50% { filter: brightness(1.15); }
            100% { filter: brightness(1); }
        }
        .logo-shine {
            animation: logoShine 3s ease-in-out infinite;
        }
    </style>
</head>
<body class="flex flex-col items-center justify-center p-md font-body-md text-on-surface">
    @yield('content')
</body>
</html>
