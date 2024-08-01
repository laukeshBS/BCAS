
   $('#home-slides').owlCarousel({
       loop:true,
       margin:10,
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


