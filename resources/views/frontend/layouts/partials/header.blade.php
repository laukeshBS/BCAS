<!-- header area start -->
@php 
  $pos=[1,4];
          $langid = Session::get('locale') ?? app()->getLocale();

            $res= get_menu($langid,$pos,0) ; $i=1; 
            $pageurl = clean_single_input(request()->segment(2));
@endphp
<section class="header-top">
    <div class="container">
      <div class="skip-wrper">
        <div title="<?php echo get_commontitle('skip-to-main-content',$langid)->title ?? ''; ?>" class="skip"> <?php echo get_commontitle('skip-to-main-content',$langid)->title ?? ''; ?></div>
        <div class="txt">Text  A-   A   A+</div>
       
      </div>
    </div>
  </section>
  <section class="logo-bar">
    <div class="container">
      <div class="row">
        <div class="col-xs-12 col-md-4 logo"><img src="{{ asset('public/frontend/img/logo.png') }}" class="img-fluid" alt=""></div>
        <div class="col-xs-12 col-md-4">
          <div class="logo-m"><img src="{{ asset('public/frontend/img/logo-02.png') }}" class="img-fluid" alt=""></div>
        </div>
        <div class="col-xs-12 col-md-4 logo-barbtn">
          <div class="logo-middle">
            <button type="button" class="btn rm"> <span><img src="{{ asset('public/frontend/img/module.png') }}" alt="<?php echo get_commontitle('restricted-module',$langid)->title ?? ''; ?>"></span>  <?php echo get_commontitle('restricted-module',$langid)->title ?? ''; ?></button>
            <div class="dropdown">
              <button class="btn English dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span><img src="{{ asset('public/frontend/img/lang.png') }}" alt=""></span>  <?php echo get_commontitle('language',$langid)->title ?? ''; ?>
                <span class="arrow"><img src="{{ asset('public/frontend/img/dropdown-ar.png') }}" alt=""></span>
              </button>
              <form id="lang-form">
                @csrf
                <div id="lang-select" class="dropdown-menu">
                <?php
                        $language = get_language();
                        foreach($language as $value) {
                        ?>
                    <a class="dropdown-item" href="#" onclick="changeLanguage('{{$value->lang_code}}')">{{$value->name}} </a>
                    <?php } ?>
                </div>
            </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="menubar-wrapper">
    <div class="container">
      <div class="menu-inner">
        <nav class="navbar navbar-expand-lg">
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-togglericons"></span>
            <span class="navbar-togglericons"></span>
            <span class="navbar-togglericons"></span>
          </button>

          <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
              <li class="nav-item {{ Route::is('/') ? 'active' : '' }}">
                <a class="nav-link no-dot" href="{{ url('/') }}"><i class="fa fa-home" aria-hidden="true"></i> <span class="sr-only">(current)</span></a>
              </li>
            
          
            @foreach($res as $mod)
            <li class="nav-item {{ Route::is('pages.*') && request()->is('pages/' . $mod->menu_url) ? 'active' : '' }} 
              @if(has_child($mod->id, $mod->language_id) > 0) dropdown @endif">
        <!-- <a class="nav-link" href="{{$mod->menu_url}}">{{$mod->menu_name}} </a> -->
                @if($mod->menu_type==2)
                    <a class="nav-link"  target="_blank" title="{{$mod->menu_name}}"  href="{{ URL::asset('public/uploads/admin/cmsfiles/menus/')}}/{{$mod->doc_upload}}"   > <span>{{$mod->menu_name}} </span></a>
                @elseif($mod->menu_type==3)
                   <a class="nav-link"  target="_blank" title="{{$mod->menu_name}}"  href="{{$mod->menu_links}}"  > <span>{{$mod->menu_name}} </span></a>
            
                @else
                    @if(has_child($mod->id, $mod->language_id) > 0)
                       <a class="nav-link  dropdown-toggle" id="navbarDropdown" title="{{$mod->menu_name}}"   role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" href="#"> <span>{{$mod->menu_name}} </span></a>
                    @else
                        <a class="nav-link"  title="{{$mod->menu_name}}"  href="@if($mod->menu_url=='#')''@else{{ url('/pages') }}/{{$mod->menu_url}}@endif"  > <span>{{$mod->menu_name}} </span></a>
                    @endif
                @endif
                 @if(has_child($mod->id, $mod->language_id) > 0)
                 @php $ress= get_menu($mod->language_id,$pos,$mod->id); @endphp 
                 <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    @foreach($ress as $mods)
                        
                            @if($mods->menu_type==2)
                            <a class="dropdown-item"  title="{{$mods->menu_name}}" target="_blank" href="{{ URL::asset('/public/uploads/admin/cmsfiles/menus/') }}/{{$mods->doc_upload}}"  > <span>{{$mods->menu_name}} </span></a>
                            @elseif($mods->menu_type==3)
                            <a class="dropdown-item"  title="{{$mods->menu_name}}" target="_blank" href="{{$mods->menu_links}}"  > <span>{{$mods->menu_name}} </span></a>
                        
                            @else
                            <a class="dropdown-item" title="{{$mods->menu_name}}"  href="@if($mods->menu_url=='#')''@else{{ url('/pages') }}/{{$mods->menu_url}}@endif"  > <span>{{$mods->menu_name}} </span></a>
                            @endif
                     
                    @endforeach
                </div>
                 @endif
              </li>
              @php $i++  @endphp
              @endforeach
            </ul>
            
          </div>
        </nav>
      </div>
    </div>
  </section>
<!-- header area end -->

<script>
  
   function changeLanguage(lang) {
      if(lang=='hi'){
       
        if (!confirm("क्या आप निश्चित हैं कि आपने भाषा बदल दी है?")) {
            return; // If the user cancels, do nothing
        }
      }else{

        if (!confirm("Are you sure you changed the language?")) {
            return; // If the user cancels, do nothing
        }
      }
         
        var token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        var url = "{{ url('/change-language')}}";
        
        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': token,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ lang: lang })
        })
        .then(response => response.json())
        .then(data => {
            // Handle response if needed
            if (data.success) {
               location.reload(); // Reload the page or update content dynamically
            } else {
                console.error('Error:', data.message);
            }
        })
        .catch(error => console.error('Error:', error));
    }
</script>
