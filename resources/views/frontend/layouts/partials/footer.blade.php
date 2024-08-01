@php $langid = Session::get('locale') ?? app()->getLocale() @endphp
<section class="Quick-link">
   <div class="container">
      <div class="Quick-link-wrap">
         <div class="text-Quick">
            <h2 class="ab-heading"><span class="hdbor-lft"></span><?php echo get_commontitle('quick-link',$langid)->title ?? ''; ?> <span class="hdbor-rgt"></span></h2>
         </div>
      </div>
      <div class="blinks-wrpper">
         <div class="blinks">
            <div class="owl-carousel owl-theme" id="quick-slides">
               <div class="item">
                  <img src="{{ asset('public/frontend/img/qucik-link/data-gov.png')}}" alt="">
               </div>
               <div class="item">
                  <img src="{{ asset('public/frontend/img/qucik-link/digital_india.png')}}" alt="">
               </div>
               <div class="item">
                  <img src="{{ asset('public/frontend/img/qucik-link/go.png')}}" alt="">
               </div>
               <div class="item">
                  <img src="{{ asset('public/frontend/img/qucik-link/iig.png')}}" alt="">
               </div>
               <div class="item">
                  <img src="{{ asset('public/frontend/img/qucik-link/india-gov.png')}}" alt="">
               </div>
               <div class="item">
                  <img src="{{ asset('public/frontend/img/qucik-link/mygov.png')}}" alt="">
               </div>
               <div class="item">
                  <img src="{{ asset('public/frontend/img/qucik-link/PMNRF.png')}}" alt="">
               </div>
            </div>
         </div>
      </div>
   </div>
</section>


<footer class="bfooter">
        <div class="container">
            <div class="row">
                <div class="col-md-4 position-relative">
                    <div class="b-contact">
                        <img class="img-fluid" src="{{ asset('public/frontend/img/f-logo.png')}}">
                        <h3>Contact Us</h3>
                        <address>
                            <p><i class="fa fa-map-marker" aria-hidden="true"></i>
                                Airport, II Floor Udaan
                                Bhawan,<br>New Delhi-110003</p>
                            <p><a href="tel:911123311443"><i class="fa fa-phone" aria-hidden="true"></i>
                                911 123 311443</a></p>
                            <p><a href="mailto:yourmail@domain.com"><i class="fa fa-envelope" aria-hidden="true"></i> yourmail@domain.com</a></p>
                            <p>
                                 Contents Owned and provided by Bureau
                                of Civil Aviation Security.</p>
                            <p><i class="fa fa-copyright" aria-hidden="true"></i>
                                2024 Bureau of Civil Aviation Security,
                                All rights reserved.</p>        
                        </address>
                    </div>
                </div>
                <div class="col-md-8 row">
                    <div class="col-md-4 pr-0">
                        <div class="inner-footer">
                            <h2><?php echo get_commontitle('imp-links',$langid)->title ?? ''; ?> </h2>
                            <ul>
                                <li><a href=""> Vacancy Positions</a></li>
                                <li><a href=""> Vacancy Circulars</a></li>
                                <li><a href=""> Recruitment Rules</a></li>
                                <li><a href=""> Trainings</a></li>
                                <li><a href=""> Air Travel</a></li>
                                <li><a href=""> Travel Smart</a></li>
                                <li><a href=""> Acts And Rules</a></li>
                                <li><a href=""> Travel Tips</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-4 p-0">
                        <div class="inner-footer">
                            <h2><?php echo get_commontitle('other-links', $langid)->title ?? ''; ?>                            </h2>
                            <ul>
                                <li><a href=""> About us</a></li>
                                <li><a href=""> BCAS Headquaters</a></li>
                                <li><a href=""> Regional Offices</a></li>
                                <li><a href=""> Feedback</a></li>
                                <li><a href=""> Survey</a></li>
                                <li><a href=""> Disclaimer</a></li>
                                <li><a href=""> Website Policies</a></li>
                                <li><a href=""> Site Map</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-4 pl-0">
                        <div class="inner-footer">
                            <h2><?php echo get_commontitle('feedback-form',$langid)->title ?? ''; ?></h2>
                            <button class="btn btn-primary w-100 f-btn"><?php echo get_commontitle('give-your-feedback',$langid)->title ?? ''; ?></button>
                        </div>
                        <div class="inner-footer social">
                            <h2><?php echo get_commontitle('follow-us',$langid)->title ?? ''; ?> :</h2>
                            <a href=""><img src="{{ asset('public/frontend/img/footer/footer-link.png')}}"></a>
                            <a href=""><img src="{{ asset('public/frontend/img/footer/footer-twi.png')}}"></a>
                            <a href=""><img src="{{ asset('public/frontend/img/footer/fotter-fb.png')}}"></a>
                            <a href=""><img src="{{ asset('public/frontend/img/footer/footer-you.png')}}"></a>
                            <p><?php echo get_commontitle('last-updated',$langid)->title ?? ''; ?> - Jun 26, 2024</p>
                            <p><?php echo get_commontitle('visitor-no',$langid)->title ?? ''; ?>.  {{ $visitorCount }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>


  

<!--Travel Safe Modal -->
<div class="modal fade" id="Travel-Safe" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog Permitted-w" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Travel Safe and Smart</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="permitted-body Safe">
            <h6>PREPARATIONS</h6>
            <p>Necessary preparations can be made by you before arrival at the airport which will help you to move more quickly and efficiently through the security processes. Here you will find suggestions on what to wear to the airport and how to pack for your trip. We've also included a pre-flight checklist to help you to travel safe & smart.</p>
            <h6>Dress</h6>
            <p>Security does not require any particular style or type of clothing. However, certain clothing and accessories can set off an alarm on the metal detector and may affect the pax flow through the screening points. Here you will find tips to help you <a href="#">Click here</a></p>
            <h6>Pack Smart</h6>
            <p>There are restrictions on what you can pack in your hand baggage and registered baggage. All of your baggage will be screened and possibly hand-searched as part of the security measures. This inspection may include emptying most or all of the articles in your bag. Here you will find tips to help you pack. 
               <a href="#">Click here</a></p>
            <h6>Final Checklist</h6>
            <p>You're dressed, packed and ready to go. Here is a pre-flight checklist to help you to travel safe and smart. Read the instructions printed on the air ticket and contact your airline or travel agent for additional information. <a href="#">Click here</a> </p>
            <h6>Access Requirements</h6>
            <p>You can enter into the passenger terminal if you have either a confirmed air ticket for the journey or a valid airport entry permit. Visitors can buy ticket to enter into the visitor’ s area in the terminal. Waitlisted passengers are advised to contact the airlines office on the landside of the terminal for confirmation of their tickets.</p>
            <h6>Security Process and Procedures</h6>
            <p>Familiarize yourself with the Security Process and Procedures. It will help you to play an active role in ensuring your own safety and avoid inconvenience to yourself.</p>
           
        </div>
      </div>
    </div>
  </div>
</div>



<!--Travel Tips Modal -->
<div class="modal fade" id="Travel-tips" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog Permitted-w" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Travel Tips</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="permitted-body Prohibited">
            <h6>A MUST read for anyone Traveling by air </h6> 
            <h6>Following tips will help you reduce your wait time at the security checkpoint.</h6>
            <div class="travel-txt">
               <ul>
                  <li>Do NOT pack or bring prohibited items to the airport. Read the <span>Permitted and Prohibited Items</span> </li>
                  <li>Refrain from carrying unverified gifts or presents in wrapped package. If the package alarms, screener will need to unwrap it to investigate the source of the alarm.</li>
                  <li>Shoes, clothing items and other accessories that contain metal will alarm the metal detector. As a result the screener will require you to undergo further checks which may include pat down frisking.</li>
                  <li><span class="pt">Put all undeveloped films and cameras with film in your Cabin (carry-on) baggage. </span> Checked baggage screening equipment may damage undeveloped films.</li>
                  <li>Carry-on baggage is limited to one carry-on bag plus one personal item. Personal items include laptops, purses, small backpacks, briefcases, or camera cases. Remember, 1+1.</li>
                  <li>Place identification tags on all your baggage. <span class="pt"> Don't forget to label your laptop computer. </span> These are one of the most forgotten items at Screening Checkpoints. </li>
                  <li>Do NOT pack or bring prohibited items to the airport. Read the Permitted and Prohibited Items</li>
                  <li>Refrain from carrying unverified gifts or presents in wrapped package. If the package alarms, screener will need to unwrap it to investigate the source of the alarm. </li>
                  <li> Protect yourself and do not pack valuables in your checked baggage.</li>
                  <h6>Your Cabin/Carry on Baggage</h6>
                  <li> Do NOT pack or bring prohibited items to the airport. Read the <span>Permitted and Prohibited Items</span> </li>
                  <li>
                  Refrain from carrying unverified gifts or presents in wrapped package. If the package alarms, screener will need to unwrap it to investigate the source of the alarm.
                  </li>
                  <li>Shoes, clothing items and other accessories that contain metal will alarm the metal detector. As a result the screener will require you to undergo further checks which may include pat down frisking.</li>
                  <li> <span class="pt"> Put all undeveloped films and cameras with film in your Cabin (carry-on) baggage. </span> Checked baggage screening equipment may damage undeveloped films.</li>
                  
               </ul>
            </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!--Air-Travel Modal -->
<div class="modal fade" id="Air-Travel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog Permitted-w" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Air Travel in India</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="permitted-body">
            
        <div class="travel-txt">
         <ul>
            <li>Due to introduction of enhanced security measures based on threat assessment across the country, BCAS continues to improve and redefine security procedures to make it secure and passenger friendly. </li>
            <li>Useful tips and informative material given below will ensure a smooth passage thorough security at any of the civil airports in India.</li>
            <li> <span>Travel Tips</span>  - A must read for anyone traveling by air. </li>
            <li> <span>Travel Safe and Smart</span> – Necessary preparations can be made by you before you arrive at the airport... </li>
            <li> <span>Permitted & Prohibited Items </span>- You could be surprised at the items, which are permitted & prohibited on-board an aircraft. </li>
            <li> <span>Transporting Special Items </span>- Some helpful information that explains the security screening procedures as they may apply to unique items. </li>
            <li> <span>Traveling with Children</span> - Everything you need to consider when traveling with children... </li>
            <li>Special </span> - Covers numerous topics such as film, pets, religious/cultural needs, sporting equipment, etc... - Covers numerous topics such as film, pets, religious/cultural needs, sporting equipment, etc... </li>
            <li> <span>Persons with Disabilities & Medical Conditions</span> - This area is extensive. BCAS has divided the area into groups and has trained the screener workforce appropriately. </li>
            <li> <span>Security Awareness</span> - Learn what to look for. what to do... </li>
            <li>
            <span>Frequently Asked Questions</span> - Answers to your most Frequently Asked Questions.. - Answers to your most Frequently Asked Questions.
            </li>
            
         </ul>
        </div>
            
        </div>
      </div>
    </div>
  </div>
</div>

<!--Permitted Modal -->
<div class="modal fade" id="Permitted" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog Permitted-w" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Permitted and Prohibited Items</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="permitted-body">
            <h6>List of Prohibited items in Checked and Cabin baggage:</h6>
            <div class="permitted-icon">
               <span>
                  <img src="{{ asset('public/frontend/img/Poup/acid.png')}}" alt="">
                  <p>Acids</p>
               </span>
               <span>
                  <img src="{{ asset('public/frontend/img/Poup/poi-toxics.png')}}" alt="">
                  <p>Poisons, Toxic</p>
               </span>
               <span>
                  <img src="{{ asset('public/frontend/img/Poup/liquied.png')}}" alt="">
                  <p>Flammable Liquids</p>
               </span>
               <span>
                  <img src="{{ asset('public/frontend/img/Poup/expolisives.png')}}" alt="">
                  <p>Explosives</p>
               </span>
               <span>
                  <img src="{{ asset('public/frontend/img/Poup/blaech.png')}}" alt="">
                  <p>Bleach</p>
               </span>
            </div>
            <div class="Compressed">
               <ul>
                  <li>Compressed gases - deeply refrigerated, flammable, non-flammable and poisonous such as butane oxygen, liquid nitrogen, aqualung cylinders and compressed gas cylinders</li>
                  <li>Corrosives such as acids, alkalis, mercury and wet cell batteries and apparatus containing mercury</li>
                  <li>Explosives, munitions, fireworks and flares, ammunition including blank cartridges, handguns, fire works, pistol caps</li>
                  <li>Flammable liquids and solids such as lighter refills, lighter fuel, matches, paints, thinners, fire-lighters, lighters that need inverting before ignition, matches (these may be carried on the person), radioactive material, briefcases and attache case with installed alarm devices</li>
                  <li>Oxidizing materials such as bleaching powder and peroxides</li>
                  <li>Poisons and infectious substances such as insecticides, weed-killers and live virus materials</li>
                  <li>Anything that possesses and/or is capable of possessing and/or emitting a conspicuous and/or offensive odour</li>
                  <li>Other dangerous articles such as magnetized materials, offensive or irritating materials</li>
               </ul>
            </div>
            <div class="permitted-icon permittedds">
               <span>
                  <img src="{{ asset('public/frontend/img/Poup/pi.png')}}" alt="">
                  
               </span>
               <span>
                  <img src="{{ asset('public/frontend/img/Poup/b.png')}}" alt="">
                  
               </span>
               <span>
                  <img src="{{ asset('public/frontend/img/Poup/f.png')}}" alt="">
                
               </span>
               <span>
                  <img src="{{ asset('public/frontend/img/Poup/p.png')}}" alt="">
               
               </span>
             
               <span>
                  <img src="{{ asset('public/frontend/img/Poup/cr.png')}}" alt="">
               </span>
               <span>
                  <img src="{{ asset('public/frontend/img/Poup/k.png')}}" alt="">
               </span>
            </div>
            <div class="Compressed">
               <ul>
                  <li>Dry cell batteries</li>
                  <li>Knives, scissors, Swiss army knives and other sharp instruments</li>
                  <li>Toy replicas of fire arms and ammunition</li>
                  <li>Weapons such as whips, nan-chakus, baton, or stun gun</li>
                  <li>Electronic devices which cannot be switched off</li>
                  <li>Aerosols and liquids*</li>
                  <li>Any other items which are deemed security hazards by local law</li>
               </ul>
            </div>
        </div>
      </div>
    </div>
  </div>
</div>


</body>

</html>
