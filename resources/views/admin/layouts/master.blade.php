<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Laravel Role Admin')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @include('admin.layouts.partials.styles')
    @yield('styles')
</head>

<body>
    <!--[if lt IE 8]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->
    <!-- preloader area start -->
    <div id="preloader">
        <div class="loader"></div>
    </div>
    <!-- preloader area end -->
    <!-- page container area start -->
    <div  id="main" class="page-container">
    
       @include('admin.layouts.partials.sidebar')
       
       <div  class="main-content">
        <!-- main content area start -->
        <button class="openbtn" onclick="toggleNav()">☰</button>
            @include('admin.layouts.partials.header')
           
               
                @yield('admin-content')
         
        <!-- main content area end -->
        @include('admin.layouts.partials.footer')
        </div>
    </div>
    <!-- page container area end -->

    @include('admin.layouts.partials.offsets')
    @include('admin.layouts.partials.scripts')
    @yield('scripts')
</body>


<script>
   // document.getElementById("mySidebar").style.width = "250px";
    //document.getElementById("main").style.marginLeft = "250px";
    function toggleNav() {
        var sidebarWidth = document.getElementById("mySidebar").style.width;
    
        if (sidebarWidth === "250px") {
            // If sidebar is open, close it
            document.getElementById("mySidebar").style.width = "0";
            document.getElementById("main").style.marginLeft = "0";
        } else {
            // If sidebar is closed, open it
            document.getElementById("mySidebar").style.width = "250px";
           // document.getElementById("main").style.marginLeft = "250px";
        }
    }
</script>

</html>
