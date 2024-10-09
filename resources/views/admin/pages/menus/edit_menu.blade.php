@extends('admin.layouts.master')

@section('title')
Menu Edit - Admin Panel
@endsection

@section('styles')
<style>
    .form-check-label {
        text-transform: capitalize;
    }
</style>
@endsection


@section('admin-content')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script> 
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.20.0/jquery.validate.min.js"></script>
<script type="text/javascript">
  $(document).ready(function() {
    var selectedValue = $('#menutype').val(); // Get the selected value of the dropdown
    addmenutype(selectedValue);
    // Attach the addmenutype function to the change event of the menutype dropdown
    $('#menutype').change(function() {
        var selectedValue = $(this).val(); 
        // Get the selected value of the dropdown
        addmenutype(selectedValue); // Call addmenutype function with the selected value
    });
});
function addmenutype(selectedValue) {
    if (selectedValue !== '') {
        handleMenuType(selectedValue);
    }
}

function handleMenuType(id) {
    if (id == '1') {
        $('#txtDoc').show();
        $('#txtPDF').hide();
        $('#txtweb').hide();
    } else if (id == '2') {
        $('#txtDoc').hide();
        $('#txtPDF').show();
        $('#txtweb').hide();
        // $('#media').hide();
    } else if (id == '3') {
        $('#txtDoc').hide();
        $('#txtPDF').hide();
        $('#txtweb').show();
    } else {
        $('#txtDoc').hide();
        $('#txtPDF').hide();
        $('#txtweb').hide();
    }
}
</script>
<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <div class="breadcrumbs-area clearfix">
                <h4 class="page-title pull-left">Menu Edit</h4>
                <ul class="breadcrumbs pull-left">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li><a href="{{ route('admin.menus.index') }}">All Menus</a></li>
                    <li><span>Edit Menu </span></li>
                </ul>
            </div>
        </div>
        <div class="col-sm-6 clearfix">
            @include('admin.layouts.partials.logout')
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12 col-md-12 col-lg-12">
        <div class="card">
            <div class="card-body">
                @if ($message = Session::get('success'))
                <div class="alert alert-success">
                    <p>{{ $message }}</p>
                </div>
                @endif
                @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Whoops!</strong> There were some problems with your input.<br><br>
                    <ul>
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
                <form action="{{ route('admin.menus.update' , $data->id) }}" name="form1" id="form1" method="post"
                    enctype="multipart/form-data" accept-charset="utf-8">
                    @csrf
                    @method('PUT')

                    <div class="panel-body">
                        
                    <div class="row">
                            <div class="col-12 col-md-3 col-lg-3">
                                <div class="form-group">
                                    <label>Menu Language:</label>
                                    <span class="star">*</span>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 col-lg-6">
                                <div class="input_class form-group">
                                <?php
                                    $language = get_language();
                                    foreach($language as $value) {
                                    ?>
                                    <input type="radio" name="language" autocomplete="off" id="txtlanguage"
                                        onclick="getMenu(this.value,{{$data->id}})" value="{{$value->lang_code}}"
                                        @if((!empty($data->language_id) ? $data->language_id : old('language')) == $value->lang_code) checked @endif
                                        class="@error('language') is-invalid @enderror" /> {{$value->name}} &nbsp;
                                <?php } ?>

                                    <!-- <input type="radio" name="language" autocomplete="off" id="txtlanguage"
                                        onclick="getMenu(this.value);" value="2"
                                        @if((!empty($data->language_id)?$data->language_id:old('language'))==2) checked
                                    @endif class="@error('language') is-invalid @enderror" />Hindi &nbsp; -->
                                    @if($errors->has('language'))
                                    <p class="text-danger">{{ $errors->first('language') }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                       
                            <div  id="content1"  style="display:<?php echo !empty($data->language_id)? "block" : "none" ;?>">
                                <?php  $language_id=!empty($data->language_id)?$data->language_id:old('language'); if(isset($language_id)): ?>
                                <?php if(!isset($data->menu_child_id))$data->menu_child_id=''; ?>
                                <?php echo primarylink_menu($language_id, $data->menu_child_id) ?>
                                <?php endif; ?>
                            </div>
                        

                        <div class="row">
                            <div class="col-12 col-md-3 col-lg-3">
                                <div class="form-group">
                                    <label>Slugs:</label>
                                    <span class="star">*</span>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 col-lg-6">
                                <div class="form-group">
                                    <input name="url" autocomplete="off" type="text"  minlength=""
                                        class="input_class form-control @error('url') is-invalid @enderror "
                                        id="txteMenu_title"
                                        value="{{!empty($data->menu_url)?$data->menu_url:old('url')}}" />
                                    @error('url')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-md-3 col-lg-3">
                                <div class="form-group">
                                    <label>Menu Title:</label>
                                    <span class="star">*</span>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 col-lg-6">
                                <div class="form-group">
                                    <input name="menu_title"  minlength="2"
                                       autocomplete="off" type="text"
                                        class="input_class form-control  @error('menu_title') is-invalid @enderror"
                                        id="menu_title"
                                        value="{{ !empty($data->menu_name)?$data->menu_name:old('menu_name')}}" />
                                    @error('menu_title')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 col-md-3 col-lg-3">
                                <div class="form-group">
                                    <label>Menu Type:</label>
                                    <span class="star">*</span>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 col-lg-6">
                                <div class="input_class form-group">

                                    <select name="menutype" id="menutype"
                                        class="form-control @error('menutype') is-invalid @enderror" autocomplete="off"  >
                                
                                        <option value="">Select</option>
                                        <?php 
                                        $menuTypeArray = array("1"=>" Content ","2"=>"File Upload","3"=>"Web Site Url");
                                        foreach($menuTypeArray as $key=>$value)
                                        {
                                        ?>
                                        <option value="{{$key}}" 
                                        @if((!empty($data->menu_type)?$data->menu_type:old('menutype'))==$key)
                                            selected @endif><?php echo $value; ?></option>
                                            
                                        <?php 
                                        }
                                        ?>
                                    </select>
                                    @error('menutype')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-md-3 col-lg-3">
                                <div class="form-group">
                                    <label>Short Description:</label>
                                    <span class="star">*</span>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 col-lg-6">
                                <div class="form-group">
                                    <textarea name="welcomedescription" maxlength="120" autocomplete="off"
                                        class="input_class form-control summernote-simple @error('welcomedescription')  is-invalid @enderror  ">{{!empty($data->welcomedescription)?$data->welcomedescription:old('welcomedescription')}}</textarea>
                                    @error('welcomedescription')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div id="txtDoc" style="display: none;">
                            <!-- <div class="row">
                                <div class="col-12 col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label>Meta Keyword:</label>
                                        <span class="star">*</span>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 col-lg-6">
                                    <div class="form-group">
                                        <input name="metakeyword" maxlength="64" autocomplete="off" type="text"
                                            class="input_class form-control" id="metakeyword"
                                            value="{{!empty($data->menu_keyword)?$data->menu_keyword:old('metakeyword')}}" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label>Meta Description:</label>
                                        <span class="star">*</span>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 col-lg-6">
                                    <div class="form-group">
                                        <input name="metadescription" maxlength="64" autocomplete="off" type="text"
                                            class="input_class form-control" id="metadescription"
                                            value="{{!empty($data->menu_description)?$data->menu_description:old('metadescription')}}" />
                                    </div>
                                </div>
                            </div> -->
                            <div class="row">
                                <div class="col-12 col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label>Description:</label>
                                        <span class="star">*</span>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 col-lg-6">
                                    <div class="form-group">
                                        <textarea name="description" id="description"
                                            class="form-control summernote-simple " rows="3" aria-hidden="true"><?php echo !empty($data->content)?$data->content:old('description'); ?></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label>Image Upload:</label>
                                        <!-- <span class="star">*</span> -->
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 col-lg-6">
                                    <div class="form-group">
                                        <input type="file" value="{{old('img_upload')}}" name="img_upload"
                                            class="input_class w-50 inline-block" id="txtupload" />
                                        <a class="w-50" target="_blank"
                                            href="{{ URL::asset('public/uploads/admin/cmsfiles/menus/')}}/{{$data->img_upload}}">
                                            View Images</a>
                                        <input type="hidden" name="oldimg_upload" class="input_class w-50 inline-block"
                                            value="<?php echo !empty($data->img_upload)?$data->img_upload:''; ?>" />
                                        <span class="txtupload_error" style="color:red;"></span>
                                        @error('img_upload')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="txtPDF" style="display: none;">
                            <div class="row">
                                <div class="col-12 col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label>Document Upload:</label>
                                        <span class="star">*</span>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 col-lg-6">
                                    <div class="form-group">
                                        <input type="file" value="{{old('txtupload')}}" name="txtupload"
                                            onchange="onlytxtuploadimg_and_pdf(this);" class="input_class w-50 inline-block"
                                            id="txtimg_pdf" />
                                        <a class="w-50" target="_blank"
                                            href="{{ URL::asset('public/uploads/admin/cmsfiles/menus/')}}/{{$data->doc_upload}}">
                                            View PDF</a>


                                        <input type="hidden" name="oldupload" class="input_class w-50 inline-block"
                                            value="<?php echo !empty($data->doc_upload)?$data->doc_upload:''; ?>" />
                                        <span class="txtimg_pdf_error" style="color:red;"></span>
                                        @error('txtupload')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="txtweb" style="display: none;">
                            <div class="row">
                                <div class="col-12 col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label>Web Site Link:</label>
                                        <span class="star">*</span>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 col-lg-6">
                                    <div class="form-group">
                                        <input type="text" maxlength="164" name="txtweblink" id="txtweblink"
                                            class="input_class form-control" autocomplete="off"
                                            placeholder="https://www.xyz.com"
                                            value="{{!empty($data->menu_links)?$data->menu_links:old('txtweblink')}}" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-md-3 col-lg-3">
                                <div class="form-group">
                                    <label>Menu Position:</label>
                                    <span class="star">*</span>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 col-lg-6">
                                <div class="form-group">
                                    <select name="txtposition" class="input_class form-control" id="txtposition"
                                        autocomplete="off">
                                        <option value=""> Select </option>
                                        <?php
                                            $statusArray = get_content_position();
                                            foreach($statusArray as $key=>$value) {
                                                ?>
                                        <option value="<?php echo $key; ?>"
                                            <?php if((!empty($data->menu_position)?$data->menu_position:old('txtposition'))==$key) echo "selected"; ?>>
                                            <?php echo $value; ?></option>
                                        <?php  }?>
                                    </select>
                                    @error('txtposition')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-md-3 col-lg-3">
                                <div class="form-group">
                                    <label>Menu Status:</label>
                                    <span class="star">*</span>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 col-lg-6">
                                <div class="form-group">
                                    <select name="txtstatus" class="input_class form-control" id="txtstatus"
                                        autocomplete="off">
                                        <option value=""> Select </option>
                                        <?php
                                        $statusArray = get_status();
                                        foreach($statusArray as $key=>$value) {
                                            ?>
                                        <option value="<?php echo $key; ?>"
                                            <?php if((!empty($data->approve_status)?$data->approve_status:old('txtstatus'))==$key) echo "selected"; ?>>
                                            <?php echo $value; ?></option>
                                        <?php  }?>
                                    </select>
                                    @error('txtstatus')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                                <div class="col-12 col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label>Banner Image:</label>
                                        
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 col-lg-6">
                                    <div class="form-group">
                                        <input type="file" value="{{old('banner_img')}}" name="banner_img"
                                            class="input_class w-50 inline-block" id="banner_img" />
                                            <?php
                                            if(!empty($data->banner_img)){
                                            ?>
                                        <a class="w-50" target="_blank"
                                            href="{{ URL::asset('public/uploads/admin/cmsfiles/menus/banner')}}/{{$data->banner_img}}">
                                            View Images</a>
                                            <?php }?>
                                        <input type="hidden" name="oldbanner_img" class="input_class w-50 inline-block"
                                            value="<?php echo !empty($data->banner_img)?$data->banner_img:''; ?>" />
                                        <span class="txtupload_error" style="color:red;"></span>
                                        @error('banner_img')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                        <div class="col-12 col-md-3 col-lg-3">
                                            </div>
                            <div class="col-12 col-md-6 col-xm-6">
                                <div class="">

                                    <input name="cmdsubmit" type="submit" class="btn btn-success" id="cmdsubmit"
                                        value="Submit" />&nbsp;
                                    <a href="{{ url('admin/menus')}}" class="btn btn-primary">Back</a>
                                    <input type="hidden" name="random" value="" />
                                </div>
                            </div>
                        </div>
                    </div>

                </form>


            </div>
        </div>
    </div>
</div>

<script src="{{ URL::asset('public/admin/assets/js/validate.js')}}"></script>
<script type="text/javascript">
function getMenu(lang,id) {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    //  var csrfHash = $('input[name="_token"]').val();
    //generate the parameter for the php script
    var data = "language=" + id;

    var linkurl = "{{ url('/admin/get_primarylink_menu')}}";
    //alert(linkurl);
    jQuery.ajax({
        url: linkurl,
        type: "POST",
        //headers: headers,
        data: {
            id: lang,
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            get_primarylink_menu: 'get_primarylink_menu'
        },
        cache: false,
        success: function(html) {
            var Obj = JSON.parse(html);
            ///alert(Obj);
            //jQuery('input[name="_token"]').val("");
            //  jQuery('input[name="_token"]').val(Obj.csrfhash);

            //hide the progress bar
            jQuery("#loading").hide();

            //add the content retrieved from ajax and put it in the #content div
            jQuery("#content1").html(Obj.html);

            //display the body with fadeIn transition
            jQuery("#content1").fadeIn("slow");
        },
    });
    var linkurl1 = "{{ url('/admin/get_menu_details')}}";
    //alert(linkurl);
    jQuery.ajax({
        url: linkurl1,
        type: "POST",
        //headers: headers,
        data: {
            language: lang,
            id: id,
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            get_menu_details: 'get_menu_details'
        },
        cache: false,
        success: function(html) {
            var Obj = JSON.parse(html);
            alert(Obj);
            //jQuery('input[name="_token"]').val("");
            //  jQuery('input[name="_token"]').val(Obj.csrfhash);

            //hide the progress bar
            jQuery("#loading").hide();

            //add the content retrieved from ajax and put it in the #content div
            jQuery("#content1").html(Obj.html);

        },
    });
}

   $(document).ready(function() {

       var id = @php
            if (!empty(old('menutype'))) {
                echo old('menutype');
            } else {
                echo !empty($data->menu_type) ? $data->menu_type : '0';
            }
        @endphp;
    //alert(id);
    if (id == '1') {
        jQuery('#txtDoc').css('display', 'block')
        jQuery('#txtDoc').css('txtPDF', 'none')
        jQuery('#txtweb').css('txtPDF', 'none')

    } else if (id == '2') {
        document.getElementById('txtDoc').style.display = 'none';
        document.getElementById('txtPDF').style.display = 'block';
        document.getElementById('txtweb').style.display = 'none';
        // document.getElementById('media').style.display = 'none';
    } else if (id == '3') {
        document.getElementById('txtDoc').style.display = 'none';
        document.getElementById('txtPDF').style.display = 'none';
        document.getElementById('txtweb').style.display = 'block';
    } else {
        jQuery('#txtDoc').css('display', 'none')
        jQuery('#txtDoc').css('txtPDF', 'none')
        jQuery('#txtweb').css('txtPDF', 'none')
    }
});
</script>
@endsection