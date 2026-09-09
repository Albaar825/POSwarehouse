<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Kasir & Warehouse')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('head')
</head>

<body class="bg-gray-100 min-h-screen">

    @yield('content')

    @stack('scripts')

</body>

</html>