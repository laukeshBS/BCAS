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
<section class="home-slider">
   
   <div class="owl-carousel owl-theme" id="home-slides">
      <div class="item">
         <div class="slider-img slide-one">
            <div class="slider-cnt">
               <h2>Staff Wellness Yoga Sessions</h2>
               <p>Take a break and join us for <br> yogasessions tailored for <br> airport staff.</p>
               <p class="welns"># <span>Sab</span>Uden<span>Sab</span>Juden</p>
            </div>
         </div>
      </div>
      <div class="item">
         <div class="slider-img slide-two">
            <div class="slider-cnt">
               <h2>Staff Wellness Yoga Sessions</h2>
               <p>Take a break and join us for <br> yogasessions tailored for <br> airport staff.</p>
            </div>
         </div>
      </div>
      <div class="item">
         <div class="slider-img slide-three">
         </div>
      </div>
      <div class="item">
         <div class="slider-img slide-four">
            <div class="slider-cnt">
            </div>
         </div>
      </div>
      
   </div>
</section>
<div class="Important">
   <div class="container">
      <span class="not-icn"><img src="{{ asset('public/frontend/img/notification.png')}}" alt=""></span>
      <span>Important Notification :</span>The exponential growth in the Civil Aviation Sector is a challenge for BCAS since trained security personnel, security infrastructure and security
   </div>
</div>
<section class="about-us">
   <div class="about-rgtimgs"></div>
   <div class="container">
      <div class="abut-inner">
         <div class="row">
            <div class="col-xs-12 col-md-5">
               <div class="abt-left">
                 
                  @if(!empty($abtdata->img_upload))
                     <img src="{{ URL::asset('public/uploads/admin/cmsfiles/menus/')}}/{{$abtdata->img_upload}}" class="img-fluid" alt="{{ $abtdata->menu_name}}">
                  @else
                     <img src="{{ asset('public/frontend/img/about-us.png')}}" class="img-fluid" alt="{{ $abtdata->menu_name}}">
                  @endif
               
                
               </div>
            </div>
            <div class="col-xs-12 col-md-7">
               <div class="abt-inner">
                  <h2 class="ab-heading"> <span class="hdbor-lft"></span> {{ $abtdata->menu_name}} <span class="hdbor-rgt"></span></h2>
                  <p><?php echo $abtdata->content; ?></p>
                  <a href="{{ url('/pages') }}/{{$abtdata->menu_url}}" class="read-more">Read More</a>
               </div>
            </div>
         </div>
      </div>
   </div>
</section>
<section class="Visitor">
   <div class="container">
      <h2 class="ab-heading"> <span class="hdbor-lft"></span> Visitor  <span>Desk</span> <span class="hdbor-rgt"></span></h2>
      <div class="Visitor-desk">
         <div class="row">
            <div class="col-xs-12 col-md-3">
               <div class="visiter-card">
                  <a href="#" class="card-pop"><img src="{{ asset('public/frontend/img/arrow-t.png')}}" alt=""></a>
                  <div class="card-bg">
                     <div class="card-icon"><img src="{{ asset('public/frontend/img/whast-new.png')}}" alt=""></div>
                  </div>
                  <h5>What's New</h5>
               </div>
            </div>
            <div class="col-xs-12 col-md-3">
               <div class="visiter-card" data-toggle="modal" data-target="#Permitted">
                  <a href="#" class="card-pop"><img src="{{ asset('public/frontend/img/arrow-t.png')}}" alt=""></a>
                  <div class="card-bg">
                     <div class="card-icon"><img src="{{ asset('public/frontend/img/Permitted.png')}}" alt=""></div>
                  </div>
                  <h5>Permitted and Prohibited Items</h5>
               </div>
            </div>
            <div class="col-xs-12 col-md-3">
               <div class="visiter-card">
                  <a href="#" class="card-pop"><img src="{{ asset('public/frontend/img/arrow-t.png')}}" alt=""></a>
                  <div class="card-bg">
                     <div class="card-icon"><img src="{{ asset('public/frontend/img/Security.png')}}" alt=""></div>
                  </div>
                  <h5>Security Program Status</h5>
               </div>
            </div>
            <div class="col-xs-12 col-md-3">
               <div class="visiter-card">
                  <a href="#" class="card-pop"><img src="{{ asset('public/frontend/img/arrow-t.png')}}" alt=""></a>
                  <div class="card-bg">
                     <div class="card-icon"><img src="{{ asset('public/frontend/img/Clearance.png')}}" alt=""></div>
                  </div>
                  <h5>Security Clearance Status</h5>
               </div>
            </div>
         </div>
      </div>
   </div>
</section>
<section class="Passenger">
   <div class="passenger-leftshed"><img src="{{ asset('public/frontend/img/qucik-link/passenger-leftsh.png')}}" alt=""></div>
   <div class="cmft"><img src="{{ asset('public/frontend/img/passenger-plan.png')}}" alt=""></div>
   <div class="container">
      <h2 class="ab-heading"> <span class="hdbor-lft"></span> Passenger   <span>Comfort</span> <span class="hdbor-rgt"></span></h2>
      <div class="passenger-card">
         <div class="passenger-wrap" data-toggle="modal" data-target="#Air-Travel">
            <div class="passenger-on"></div>
            <div class="passenger-w">01</div>
            <div class="passenger-wp">
               <h6>Air Travel in India <span><i class="fa fa-long-arrow-right" aria-hidden="true"></i></span></h6>
               <p>A must read for anyone traveling by air.</p>
            </div>
         </div>
         <div class="passenger-wrap" data-toggle="modal" data-target="#Travel-tips">
            <div class="passenger-on"></div>
            <div class="passenger-w">02</div>
            <div class="passenger-wp">
               <h6>Travel Tips <span><i class="fa fa-long-arrow-right" aria-hidden="true"></i></span></h6>
               <p>A must read for anyone traveling by air.</p>
            </div>
         </div>
         <div class="passenger-wrap bor-n" data-toggle="modal" data-target="#Travel-Safe">
            <div class="passenger-on"></div>
            <div class="passenger-w">03</div>
            <div class="passenger-wp">
               <h6>Travel Safe and Smart<span><i class="fa fa-long-arrow-right" aria-hidden="true"></i></span></h6>
               <p>A must read for anyone traveling by air.</p>
            </div>
         </div>
      </div>
   </div>
</section>
<section class="e-services">
   <div class="container">
      <div class="e-servic-top">
         <div class="eservice-left">
            <h2 class="ab-heading"> <span class="hdbor-lft"></span> e-Services<span>/Digital Library </span> <span class="hdbor-rgt"></span></h2>
         </div>
         <div class="eservice-right">
            <p>The Bureau of Civil Aviation Security (BCAS) in India offers several e-services to enhance security and efficiency in civil aviation. These services streamline various processes for stakeholders, including airlines, airports, and individuals involved in aviation security. Here are some of the key e-services provided by BCAS:</p>
         </div>
      </div>
      <div class="e-services-cardinner">
         <div class="e-services-cardwrap">
            <div class="e-serviicon"><img src="{{ asset('public/frontend/img/tenders.png')}}" alt=""></div>
            <h4>Tenders</h4>
            <p>CAS (Civil Aviation Services Limited)
               Is engaged in the technical
               management of commercial
               aircraft
            </p>
            <a href="#" class="lern-more">LEARN MORE</a>
         </div>
         <div class="e-services-cardwrap">
            <div class="e-serviicon"><img src="{{ asset('public/frontend/img/Training.png')}}" alt=""></div>
            <h4>Training</h4>
            <p>CAS (Civil Aviation Services Limited)
               Is engaged in the technical
               management of commercial
               aircraft
            </p>
            <a href="#" class="lern-more">LEARN MORE</a>
         </div>
         <div class="e-services-cardwrap">
            <div class="e-serviicon"><img src="{{ asset('public/frontend/img/forms.png')}}" alt=""></div>
            <h4>Forms</h4>
            <p>CAS (Civil Aviation Services Limited)
               Is engaged in the technical
               management of commercial
               aircraft
            </p>
            <a href="#" class="lern-more">LEARN MORE</a>
         </div>
         <div class="e-services-cardwrap">
            <div class="e-serviicon"><img src="{{ asset('public/frontend/img/vacency.png')}}" alt=""></div>
            <h4>Vacancy</h4>
            <p>CAS (Civil Aviation Services Limited)
               Is engaged in the technical
               management of commercial
               aircraft
            </p>
            <a href="#" class="lern-more">LEARN MORE</a>
         </div>
      </div>
   </div>
</section>

<section class="our-mission-visin">
   <div class="about-rgtimgs"></div>
   <div class="container">
      <div class="mision-hd">
         <h2 class="ab-heading"> Our <span>Mission & Vision</span> <span class="hdbor-rgt"></span></h2>
      </div>
      <div class="row">
         <div class="col-xs-12 col-md-6">
            <div class="mission-txt">
               <div class="m-val">01</div>
               <div class="M-SHAPE"><img src="{{ asset('public/frontend/img/mission-angle.png')}}" alt=""></div>
               <div class="ms-wrap">
                  <div class="mission-mg"><img src="{{ asset('public/frontend/img/mission.png')}}" alt=""></div>
                  <h5>Mission</h5>
                  <p>Enforce all Standards & Recommended Practices of ICAO for International as well as Domestic operations. Coordinate monitor inspect and train personnel in Civil Aviation Security matters thereby maximizing effectiveness of aviation security.</p>
               </div>
            </div>
         </div>

         <div class="col-xs-12 col-md-6">
            <div class="mission-txt">
               <div class="m-val">02</div>
               <div class="M-SHAPE"><img src="{{ asset('public/frontend/img/Vission-angle.png')}}" alt=""></div>
               <div class="ms-wrap">
                  <div class="mission-mg"><img src="{{ asset('public/frontend/img/mission.png')}}" alt=""></div>
                  <h5>Vission</h5>
                  <p>Enforce all Standards & Recommended Practices of ICAO for International as well as Domestic operations. Coordinate monitor inspect and train personnel in Civil Aviation Security.</p>
               </div>
            </div>
         </div>
         
      </div>
   </div>
</section>
<section class="event">
   <div class="container">
      <h2 class="ab-heading"> <span class="hdbor-lft"></span> Latest  <span>Events</span> <span class="hdbor-rgt"></span></h2>
      <p class="news">Here we displayed the news about Upcoming Events in our BCAS.</p>
      <div class="Events-sliders">
         <div class="owl-carousel owl-theme" id="Events-slide">
            <div class="item">
               <div class="event-box">
                  <img src="{{ asset('public/frontend/img/Vacancy-01.png')}}" class="img-fluid">
                  <div class="event-inner">
                     <h1>Vacancy Circulars</h1>
                     <p>Invitation of applications for engagement as Young Professionals in Bureau of Civil Aviation Security, Ministry of Civil Aviation-reg.</p>
                     <div class="more-btn">
                        <a href="">Learm More</a>
                     </div>
                  </div>
               </div>
            </div>
            <div class="item">
               <div class="event-box">
                  <img src="{{ asset('public/frontend/img/Vacancy-02.png')}}" class="img-fluid">
                  <div class="event-inner">
                     <h1>Vacancy Circulars</h1>
                     <p>Invitation of applications for engagement as Young Professionals in Bureau of Civil Aviation Security, Ministry of Civil Aviation-reg.</p>
                     <div class="more-btn">
                        <a href="">Learm More</a>
                     </div>
                  </div>
               </div>
            </div>
            <div class="item">
               <div class="event-box">
                  <img src="{{ asset('public/frontend/img/Vacancy-03.png')}}" class="img-fluid">
                  <div class="event-inner">
                     <h1>Vacancy Circulars</h1>
                     <p>Invitation of applications for engagement as Young Professionals in Bureau of Civil Aviation Security, Ministry of Civil Aviation-reg.</p>
                     <div class="more-btn">
                        <a href="">Learm More</a>
                     </div>
                  </div>
               </div>
            </div>
            <div class="item">
               <div class="event-box">
                  <img src="{{ asset('public/frontend/img/Vacancy-04.png')}}" class="img-fluid">
                  <div class="event-inner">
                     <h1>Vacancy Circulars</h1>
                     <p>Invitation of applications for engagement as Young Professionals in Bureau of Civil Aviation Security, Ministry of Civil Aviation-reg.</p>
                     <div class="more-btn">
                        <a href="">Learm More</a>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</section>
<section class="chatbox">
   <div class="container">
      <div class="row">
         <div class="col-md-6">
            <div class="notice">
               <h2> <span class="notice-ic">
                  <img src="{{ asset('public/frontend/img/notices.png')}}" alt=""></span> 
                  <span class="nt">Notices</span> 
                  <span class="se-more">
                  <a href="#">See more notices<i class="fa fa-arrow-right" aria-hidden="true"></i></a> 
                  </span>
               </h2>
               <div class="notice-list">
                  <h5>16<br><span>April, 24</span></h5>
                  <p>Establishment Order 23/2024<br>
                     <small>Dated 16/04/2024</small>
                  </p>
               </div>
               <div class="notice-list">
                  <h5>16<br><span>April, 24</span></h5>
                  <p>Establishment Order 23/2024<br>
                     <small>Dated 16/04/2024</small>
                  </p>
               </div>
               <div class="notice-list">
                  <h5>16<br><span>April, 24</span></h5>
                  <p>Establishment Order 23/2024<br>
                     <small>Dated 16/04/2024</small>
                  </p>
               </div>
               <div class="notice-list">
                  <h5>16<br><span>April, 24</span></h5>
                  <p>Establishment Order 23/2024<br>
                     <small>Dated 16/04/2024</small>
                  </p>
               </div>
               <div class="notice-list">
                  <h5>16<br><span>April, 24</span></h5>
                  <p>Establishment Order 23/2024<br>
                     <small>Dated 16/04/2024</small>
                  </p>
               </div>
            </div>
         </div>
         <div class="col-md-6">
            <div class="circulars">
               <h2><img src="{{ asset('public/frontend/img/c-chat.png')}}" width="34px"> Circulars<span><img src="{{ asset('public/frontend/img/c-icon.png')}}" width="34px"></span></h2>
               <div class="inner-circ">
                  <div class="circ-box">
                     <h3>Office Order</h3>
                     <p>Dated 28/11/2022</p>
                  </div>
                  <div class="circ-box">
                     <h3>Vacancy Circular Filling Up The Posts Of Joint Director Regional
                        Director, Deputy Director & Assistant Director
                     </h3>
                     <p>Dated 28/11/2022</p>
                  </div>
                  <div class="circ-box">
                     <h3>Office Order</h3>
                     <p>Dated 28/11/2022</p>
                  </div>
                  <div class="circ-box">
                     <h3>Establishment Order 39/2022</h3>
                     <p>Dated 28/11/2022</p>
                  </div>
                  <div class="circ-box">
                     <h3>Establishment Order 39/2022</h3>
                     <p>Dated 28/11/2022</p>
                  </div>
                  <div class="circ-box">
                     <h3>Office Order</h3>
                     <p>Dated 28/11/2022</p>
                  </div>
                  <div class="circ-box">
                     <h3>Vacancy Circular Filling Up The Posts Of Joint Director Regional
                        Director, Deputy Director & Assistant Director
                     </h3>
                     <p>Dated 28/11/2022</p>
                  </div>
                  <div class="circ-box">
                     <h3>Office Order</h3>
                     <p>Dated 28/11/2022</p>
                  </div>
                  <div class="circ-box">
                     <h3>Establishment Order 39/2022</h3>
                     <p>Dated 28/11/2022</p>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</section>

@endsection