<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>{{ config('app.name') }}{{ isset($title) ? ' - ' . $title : '' }}</title>

        <script>
            const branchTimezone = "{{ auth()->user()?->branch?->timezone ?? config('app.timezone') }}";
        </script>

        @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/attendance-chart2.js'])
        
        <link rel="icon" type="image/x-icon" href="{{ asset('staff-kkj.png') }}">
        <link href="https://cdn.jsdelivr.net/npm/remixicon@4.9.0/fonts/remixicon.css" rel="stylesheet"/>
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>

        @livewireStyles
    </head>
    <body>
        @include('layouts.header')
        <div class="flex flex-col min-h-screen pt-14">
            <main class="flex-1">
                {{ $slot }}
            </main>

            @include('layouts.footer')
        </div>
        @livewireScripts
        <script src="https://cdn.jsdelivr.net/npm/flowbite@4.0.1/dist/flowbite.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        
        <script>
            function initAll() {
                if (window.initFlowbite) {
                    initFlowbite();
                }
            }

            document.addEventListener('DOMContentLoaded', initAll);
            document.addEventListener('livewire:navigated', initAll);
        </script>

         <!-- Make sure you put this AFTER Leaflet's CSS -->
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    </body>
</html>
