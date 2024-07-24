<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        {{-- <title>{{ isset($title) ? $title . ' - ' . config('app.name', 'Laravel') : config('app.name', 'Laravel') }}</title> --}}

        {!! SEO::generate() !!}

        <!-- Favicon -->
        <link rel="icon" type="image/x-icon" href={{ strtolower(substr($_SERVER['SERVER_PROTOCOL'], 0, strpos($_SERVER['SERVER_PROTOCOL'], '/' ))) . '://' . $_SERVER['HTTP_HOST'] ."/favicon.ico" }}>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link href="https://fonts.googleapis.com/css?family=Karla:400,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Font Awesome -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/js/all.min.js" integrity="sha256-KzZiKy0DWYsnwMF+X1DvQngQ2/FxF7MF3Ff72XcpuPs=" crossorigin="anonymous"></script>

        <!-- Styles -->
        @livewireStyles
    </head>
    <body class="bg-gray-100 font-family-karla">

        <!-- Page Heading -->
        <x-app-header title="{{ config('app.name') }}" description="Totam accusamus deleniti culpa ullam nihil odit culpa" image="background_612x612.jpg" />

        <!-- Page Navigation -->
        @include('layouts.navigation')

        <div class="container mx-auto py-8">
            <!-- Flash Message -->
            @if (flash()->message)
                <div class="z-50 float-right text-center px-4 py-2 rounded-md shadow-md {{ flash()->class }}">
                    {{ flash()->message }}
                </div>
            @endif

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        <!-- Page Footing -->
        @include('layouts.footer')

        @stack('modals')

        @livewireScripts
    </body>
</html>
