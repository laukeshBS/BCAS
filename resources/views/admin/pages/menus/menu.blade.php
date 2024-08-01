
@extends('admin.layouts.master')

@section('title')
Menus Create - Admin Panel
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
                <h4 class="page-title pull-left">Menu List</h4>
                <ul class="breadcrumbs pull-left">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    @if(!empty($id))
                    <li><a href="{{ route('admin.menus.index') }}"> All Menu</a></li>
                    <li><span> <?php echo $parentMenu->menu_name; ?></span></li>
                    @else
                    <li><span> Menu List</span></li>
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
             <a  style="float: right;" href="{{ url('/admin/menus')}}" class="btn btn-primary" >Back</a>

            @else
            <a  style="float: right;" href="{{URL::to('admin/menus/create')}}" class="btn btn-primary pull-right"> Add Menu</a>
           
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
                <form action="{{ url('/admin/menus')}}" class="search_inbox" name="form1" id="form1" method="post" accept-charset="utf-8">
             
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
                            <select   onchange="search(this);" name="approve_status" id="approve_status" class="form-control">
                              <option value=""> Select </option>
                                <?php
                                $statusArray = get_status();
                                foreach($statusArray as $key=>$value) {
                                    ?>
                                    <option value="<?php echo $key; ?>" <?php if(Session::get('approve_status')==$key) echo "selected"; ?>><?php echo $value; ?></option>
                                <?php  }?>    
                           </select>
                            </div>
                       
                        <div class="form-group col-md-1">
                        <label for="language">Language: </label>
                        </div>
                        <div class="form-group col-md-2">
                            <select  onchange="search(this);" name="language_id" id="language_id" class="form-control">
                            <?php
                                $language = get_language();
                                foreach($language as $value) {
                                ?>    
                                <option value="{{$value->lang_code}}" <?php if(Session::get('language_id')==$value->lang_code) echo "selected"; ?> > {{$value->name}}</option>
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
					<table class="table table-striped table-bordered table-hover" >
						<thead>
							<tr>
								<th>#</th>
								<th>Menu Name</th>
                                @if(!empty($id))
                                <th>Parent Menu Name</th>
                                @endif
                                <th>Menu Order</th>
								<th>Menu Status</th>
								<th>Language</th>
								<th>Options</th>
							</tr>
						</thead>
						
						<tbody id="list">
						
						<?php
							if(count($list) > 0)
							{
							$count = 1;
							foreach($list as $row):
						?>
							<tr>
								<td><?php echo $count++; ?></td>
								<td><?php echo $row->menu_name; ?></td>
                                @if(!empty($id))
                                <td><?php echo $parentMenu->menu_name; ?></td>
                                @endif
                                <td><?php echo $row->page_order??0; ?> <i id="{{$row->id}}" onclick="editmenu(this);"  class="fa editbut fa-edit"></i>
                                <span  id="page_order_{{$row->id}}" style="display:none" >
                                <input class="w-25" type="number"
                                onchange="savedata(this);" id="{{$row->id}}" name="page_order" value="" /></span>
                                <p class="text-success" id="success_{{$row->id}}"></p>
                            </td>
								<td><?php  
                                    if(has_child($row->id, $row->language_id) > 0):
                                        ?>
                                        <strong><a href="{{route('admin.menus.show', $row->id)}}"><?php echo status($row->approve_status); ?></a></strong><br/>(Click for Sub Menu)
                                        <?php
                                    else:
                                        echo status($row->approve_status);
                                    endif;
                                    
                                    ?></td> 
								<td><?php echo language($row->language_id); ?></td>
								<form action="{{ route('admin.menus.destroy',$row->id) }}"  method="POST"> 
                                            <td>
                                                 <a class="btn btn-primary" href="{{ route('admin.menus.edit',$row->id) }}">Edit</a>
                                            @csrf
                                            @method('DELETE')

                                   <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                   </td>
                                        </form>
							</tr>
						<?php
							endforeach;
							
							} else {
						?>
							<tr>
								<td colspan="5" class="text-center"> No Record Added. </td>
							</tr>
						<?php

							}
						?>
						</tbody>
					</table>
                    {!! $list->withQueryString()->links('pagination::bootstrap-5') !!}
				</div>
                    <!-- /.table-responsive -->
                </div>
                <!-- /.panel-body -->
            </div>
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
     function editmenu(data) {
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
       
        var linkurl = "{{ url('/admin/update_menu_orders')}}";
        
        jQuery.ajax({
            url: linkurl,
            type: "POST",
            data: {id: id,page_order:page_order ,update_menu_orders:'update_menu_orders'},
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
