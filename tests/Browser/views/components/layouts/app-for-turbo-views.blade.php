<html>
<head>
    <meta name="csrf-token" content="{{ ws_csrf_token() }}">

    <script src="https://unpkg.com/alpinejs@3.4.1/dist/cdn.min.js" defer></script>

    <script type="module">
        import hotwiredTurbo from 'https://cdn.skypack.dev/@hotwired/turbo';
    </script>

    @livewireStyles
</head>
<body>
{{ $slot }}

@livewireScripts
<script src="https://cdn.jsdelivr.net/gh/livewire/turbolinks@v0.1.4/dist/livewire-turbolinks.js"
        data-turbolinks-eval="false" data-turbo-eval="false"></script>
<script data-turbo-eval="false">
    document.addEventListener('turbo:before-render', () => {
        let permanents = document.querySelectorAll('[data-turbo-permanent]')

        let undos = Array.from(permanents).map(el => {
            el._x_ignore = true

            return () => {
                delete el._x_ignore
            }
        })

        document.addEventListener('turbo:render', function handler() {
            while (undos.length) undos.shift()()

            document.removeEventListener('turbo:render', handler)
        })
    })
</script>
@stack('scripts')
</body>
</html>
