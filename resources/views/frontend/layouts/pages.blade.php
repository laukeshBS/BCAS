<!doctype html>
<html class="no-js" lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Frontend')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @include('frontend.layouts.partials.styles')
    @yield('styles')
</head>

<body>
  
    @include('frontend.layouts.partials.header')
           
               
        @yield('frontend-content')
         
        <!-- main content area end -->
        @include('frontend.layouts.partials.footer')
    
    @include('frontend.layouts.partials.scripts')
    @yield('scripts')
</body>


</html>
