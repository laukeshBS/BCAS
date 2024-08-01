
@extends('admin.layouts.master')

@section('title')
Certificates Categories Create - Admin Panel
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
                <h4 class="page-title pull-left">Certificates Categories List</h4>
                <ul class="breadcrumbs pull-left">
                    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    @if(!empty($id))
                    <li><a href="{{ route('admin.division.training.certificates_categories.index') }}"> All Certificates Categories</a></li>
                    <li><span> <?php echo $parentcertificatesCategories->CoursCategories_name; ?></span></li>
                    @else
                    <li><span> Certificates Categories List</span></li>
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
             <a  style="float: right;" href="{{ url('/admin/division/training/certificates_categories')}}" class="btn btn-primary" >Back</a>

            @else
            <a  style="float: right;" href="{{URL::to('admin/division/training/certificates_categories/create')}}" class="btn btn-primary pull-right"> Add certificates Category</a>
           
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
                <form action="{{ url('/admin/division/training/certificates_categories')}}" class="search_inbox" name="form1" id="form1" method="post" accept-charset="utf-8">
             
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
					<table class="table table-striped table-bordered table-hover" >
						<thead>
							<tr>
								<th>#</th>
								<th> Name</th>
                                <th> Description</th>
                                <th> Order</th>
								<th> Status</th>
								<th>Language</th>
								<th>Options</th>
							</tr>
						</thead>
						
						<tbody id="list">
						
						@forelse($list as $index => $row)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $row->title }}</td>
                            <td>{!! $row->description !!}</td>
                            <td>{{ $row->position ?? 0 }}</td>
                            <td>
                                @if(has_child($row->id, $row->language_id) > 0)
                                <strong><a href="{{ route('admin.certificates.categories.show', $row->id) }}">{{ status($row->approve_status) }}</a></strong>
                                @else
                                {{ status($row->status) }}
                                @endif
                            </td>
                            <td>{{ language($row->language_id) }}</td>
                            <td>
                                <a class="btn btn-primary" href="{{ route('admin.division.training.certificates_categories.edit', $row->id) }}">Edit</a>
                                <form action="{{ route('admin.division.training.certificates_categories.destroy', $row->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">No Record Added.</td>
                        </tr>
                        @endforelse
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


@endsection
