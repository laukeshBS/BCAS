
@extends('admin.layouts.master')

@section('title')
CommonTitle Create - Admin Panel
@endsection

@section('styles')
<style>
    .form-check-label {
        text-transform: capitalize;
    }
</style>
@endsection


@section('admin-content')
<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <div class="breadcrumbs-area clearfix">
                <h4 class="page-title pull-left">Training calendars list</h4>
                <ul class="breadcrumbs pull-left">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    @if(!empty($id))
                    <li><a href="{{ route('admin.training-calendars.index') }}"> All Training calendars list</a></li>
                    <li><span> <?php echo $parentCourseCategories->CoursCategories_name; ?></span></li>
                    @else
                    <li><span> Training calendars list</span></li>
                    @endif
                </ul>
            </div>
        </div>
        <div class="col-sm-6 clearfix">
            @include('admin.layouts.partials.logout')
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body"> 
<div id="page-wrapper">
    <div class="row">
        <div class="col-12 col-md-12 col-lg-12">
           
            @if(!empty($id))
             <a  style="float: right;" href="{{ url('/admin/training-calendars')}}" class="btn btn-primary" >Back</a>

            @else
            <a  style="float: right;" href="{{URL::to('admin/training-calendars/create')}}" class="btn btn-primary pull-right"> Add Calendar</a>
           
             @endif                    
        </div>
        <!-- /.col-12 col-md-12 col-lg-12 -->
       

  
  
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-12 col-md-12 col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                <div class="search-from">
                <form action="{{ url('/admin/training-calendars')}}" class="search_inbox" name="form1" id="form1" method="post" accept-charset="utf-8">
             
                    @csrf
                      <div class="form-row">
                       <div class="form-group">
                        <label for="Title">Title: </label>
                        </div>
                        <div class="form-group col-md-2">
                           <input onchange="search(this);" class="form-control" type="text" name="title" value="{{Session::get('Mtitle')??''}}">
                        </div>
                           <div class="form-group col-md-1">
                            <label for="Status">Status: </label>
                            </div>
                            <div class="form-group col-md-2">
                            <select   onchange="search(this);" name="status" id="status" class="form-control">
                              <option value=""> Select </option>
                                <?php
                                $statusArray = get_status();
                                foreach($statusArray as $key=>$value) {
                                    ?>
                                    <option value="<?php echo $key; ?>" <?php if(Session::get('status')==$key) echo "selected"; ?>><?php echo $value; ?></option>
                                <?php  }?>    
                           </select>
                            </div>
                       
                        <div class="form-group col-md-1">
                        <label for="language">Language: </label>
                        </div>
                        <div class="form-group col-md-2">
                            <select  onchange="search(this);" name="lang_code" id="lang_code" class="form-control">
                            <?php
                                $language = get_language();
                                foreach($language as $value) {
                                ?>    
                                <option value="{{$value->lang_code}}" <?php if(Session::get('lang_code')==$value->lang_code) echo "selected"; ?> > {{$value->name}}</option>
                                <?php } ?>
                            </select>

                        </div>
                        
                       
                        <div class="form-group col-md-1">
                           
                        <input onchange="search(this);" class="form-control btn btn-success" type="submit" name="search" value="Search">
                    
                        </div>
                       
                       
                     </div> 
                    </form>
                   
                </div>
                </div>
                

                @if ($message = Session::get('success'))
                <div class="alert alert-success">
                    <p>{{ $message }}</p>
                </div>
                @endif
               <div class="panel-body">
    <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Training Name</th>
                    <th>January</th>
                    <th>February</th>
                    <th>March</th>
                    <th>April</th>
                    <th>May</th>
                    <th>June</th>
                    <th>July</th>
                    <th>August</th>
                    <th>September</th>
                    <th>October</th>
                    <th>November</th>
                    <th>December</th>
                    <th>Status</th>
                    <th>Remarks</th>
                    <th>Positions</th>
                    <th>Language</th>
                    <th>Created By</th>
                    <th>Options</th>
                </tr>
            </thead>
            <tbody id="list">
                <?php
                if (count($calendars) > 0) {
                    $count = 1;
                    foreach ($calendars as $row) {
                ?>
                <tr>
                    <td><?php echo $count++; ?></td>
                    <td><?php echo htmlspecialchars($row->avSec_training); ?></td>
                    <td><?php echo htmlspecialchars($row->January); ?></td>
                    <td><?php echo htmlspecialchars($row->February); ?></td>
                    <td><?php echo htmlspecialchars($row->March); ?></td>
                    <td><?php echo htmlspecialchars($row->April); ?></td>
                    <td><?php echo htmlspecialchars($row->May); ?></td>
                    <td><?php echo htmlspecialchars($row->June); ?></td>
                    <td><?php echo htmlspecialchars($row->July); ?></td>
                    <td><?php echo htmlspecialchars($row->August); ?></td>
                    <td><?php echo htmlspecialchars($row->September); ?></td>
                    <td><?php echo htmlspecialchars($row->October); ?></td>
                    <td><?php echo htmlspecialchars($row->November); ?></td>
                    <td><?php echo htmlspecialchars($row->December); ?></td>
                    <td><?php echo status($row->status); ?></td>
                    <td><?php echo htmlspecialchars($row->remarks); ?></td>
                    <td><?php echo htmlspecialchars($row->positions); ?></td>
                    <td><?php echo language($row->lang_code); ?></td>
                    <form action="{{ route('admin.training-calendars.destroy', $row->id) }}" method="POST"> 
                        <td>
                            <a class="btn btn-primary" href="{{ route('admin.training-calendars.edit', $row->id) }}">Edit</a>
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                        </td>
                    </form>
                </tr>
                <?php
                    }
                } else {
                ?>
                <tr>
                    <td colspan="18" class="text-center">No Record Added.</td>
                </tr>
                <?php
                }
                ?>
            </tbody>
        </table>
        {!! $calendars->withQueryString()->links('pagination::bootstrap-5') !!}
    </div>
    <!-- /.table-responsive -->
</div>
<!-- /.panel-body -->

            <!-- /.panel -->
        </div>
        <!-- /.col-12 col-md-12 col-lg-12 -->
    </div>
    <!-- /.row -->
</div>
</div>
    <!-- /.row -->
</div>
<!-- Button trigger modal -->

<script>
     function editCommonTitle(data) {
        $("#page_order_"+data.id).toggle();
     }
     function savedata(data) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        var page_order =  data.value;
        var id =  data.id;
       
        var linkurl = "{{ url('/admin/update_CommonTitle_orders')}}";
        
        jQuery.ajax({
            url: linkurl,
            type: "POST",
            data: {id: id,page_order:page_order ,update_CommonTitle_orders:'update_CommonTitle_orders'},
            cache: false,
            success: function (html) {
                setTimeout(function(){
                    location.reload();
                }, 1000); 
                $("#page_order_"+data.id).hide();
                $("#success_"+data.id).html('This Postion is Updated');
            },
        });
       
        
     }
     
     
</script>

@endsection
