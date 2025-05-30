<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
        <meta charset="utf-8">
 
        <meta name="application-name" content="{{ config('app.name') }}">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="viewport" content="width=device-width, initial-scale=1">
 
        <title>{{ $title }}</title>
        <link rel="icon" type="image/x-icon" href="{{ asset('img/logo.png') }}">
 
        <style>
            [x-cloak] {
                display: none !important;
            }
        </style>
 
        @filamentStyles
        @vite('resources/css/app.css')
    </head>
 
    <body class="antialiased bg-slate-100">
        {{ $slot }}
 
        @livewire('notifications')
        
        @filamentScripts
        @vite('resources/js/app.js')
    </body>
</html>
