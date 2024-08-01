@extends('frontend.layouts.pages')

@section('title')
BCAS - {{$title}}
@endsection

@section('styles')
<style>
    .form-check-label {
        text-transform: capitalize;
    }
</style>
@endsection


@section('frontend-content')
<section class="home-slider" style="height: 250px;">
   
   <div class="owl-carousel owl-theme" id="home-slides">
      <div class="item">
         <div class="slider-img slide-one">
            <div class="slider-cnt">
               <h2> {{$title}}</h2>
            
            </div>
         </div>
      </div>
     
      
   </div>
</section>

<section class="about-us">
   <div class="about-rgtimgs"></div>
   <div class="container">
      <div class="abut-inner">
         <div class="row">
          <div class="col-xs-12 col-md-12">
               <div class="abt-inner">
                  <h2 class="ab-heading"> <span class="hdbor-lft"></span> {{$title}}</span> <span class="hdbor-rgt"></span></h2>
                 <p><?php echo $data->content ;?></p>
               </div>
            </div>
         </div>
      </div>
   </div>
</section>

@endsection