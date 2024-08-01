<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"></script>
<!-- Template Main JS File -->
<script src="{{ asset('public/frontend/js/jquery.min.js') }}"></script>
<script src="{{ asset('public/frontend/js/owl.carousel.min.js') }}"></script>
<script>
   $('#home-slides').owlCarousel({
       loop:true,
       margin:10,
       animateOut: 'fadeOut',
       autoplayTimeout:3000,
       autoplay:true,
       dots:false,
       navigation : true,
       nav:true,
       responsive:{
           0:{
               items:1
           },
           600:{
               items:1
           },
           1000:{
               items:1
           }
       }
   })
   
   $('#Events-slide').owlCarousel({
    loop:true,
    margin:10,
    dots:false,
    autoplay:true,
    nav:true,
    responsive:{
        0:{
            items:1
        },
        600:{
            items:3
        },
        1000:{
            items:4
        }
    }
   })
   
   
   $('#quick-slides').owlCarousel({
    loop:true,
    margin:10,
    autoplay:true,
    nav:true,
    responsive:{
        0:{
            items:1
        },
        600:{
            items:3
        },
        1000:{
            items:7
        }
    }
   })
   
   
   
</script>
