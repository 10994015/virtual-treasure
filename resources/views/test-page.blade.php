<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Livewire Test</title>
    @livewireStyles
</head>
<body>
    <h1 style="padding: 20px;">Livewire Test Page</h1>

    @livewire('test-counter')

    @livewireScripts

    <script>
        console.log('CSRF Token:', document.querySelector('meta[name="csrf-token"]').content);
        console.log('Livewire loaded:', typeof window.Livewire !== 'undefined');
    </script>
</body>
</html>
