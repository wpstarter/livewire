<html>
<head>
    <meta name="csrf-token" content="{{ ws_csrf_token() }}">
    @livewireStyles
</head>
<body>
@yield('content')

@livewireScripts
@stack('scripts')
</body>
</html>
