<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>乘客叫車 - {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/frontend-user/app.js'])
</head>
<body class="min-h-screen" style="background: linear-gradient(160deg, #f0f4f8 0%, #e2e8f0 40%, #cbd5e1 100%);">
    <div id="booking-app"></div>
    <script>
        // 注入登入乘客 ID 供 Vue 叫車 API 使用
        window.__PASSENGER_ID__ = {{ auth()->id() ?? 'null' }};
    </script>
</body>
</html>
