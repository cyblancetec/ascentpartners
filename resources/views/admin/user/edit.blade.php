@extends('layouts.admin')

@section('content')
<div class="page-title"><h3>Update User</h3></div>  
<div id="main-wrapper">
	<div class="panel panel-dark">
		<div class="panel-body">
			 <div class="row">
			 	@if ($errors->count() > 0)
		           	<div class="alert alert-danger">
						<ul>
		                    @foreach($errors->all() as $error)
		                        <li>{{ $error }}</li>
		                    @endforeach
		                </ul>
		            </div>
		        @endif
			 	<div class="col-lg-6 col-md-8">	
					<form action="{{ route('users.update', $user->id) }}" method="post" enctype="multipart/form-data" class="form-horizontal">
						{{ csrf_field() }}
						<input type="hidden" name="_method" value="PUT">
						<div class="form-group">
							<label class="col-md-4 control-label" for="company_id">Company*</label>
							<div class="col-md-8">	
								<select class="form-control" name="company_id">
									<option value="">Select Company</option>
									@php
									$companies = App\Company::orderBy('name')->get();;
									if($companies) foreach($companies as $company) { 
										$selected = '';
										if(old('company_id') == $company->id) {
											$selected = 'selected="selected"';
										}else if($user->company_id == $company->id) {
											$selected = 'selected="selected"';
										}
									@endphp
										<option value="{{ $company->id }}" {{ $selected }}>{{ $company->name }}</option>
									@php } @endphp
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-4 control-label" for="first_name">First Name*</label>
							<div class="col-md-8">								
								<input type="text" class="form-control" name="first_name" value="@if(old('first_name')){{ old('first_name') }}@else{{ $user->first_name }}@endif">
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-4 control-label" for="last_name">Last Name</label>
							<div class="col-md-8">	
								<input type="text" class="form-control" name="last_name" value="@if(old('last_name')){{ old('last_name') }}@else{{ $user->last_name }}@endif">
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-4 control-label" for="email">Email*</label>
							<div class="col-md-8">									
								<input type="email" class="form-control" name="email" value="@if(old('email')){{ old('email') }}@else{{ $user->email }}@endif">
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-4 control-label" for="password">Password</label>
							<div class="col-md-8">
								<input type="password" class="form-control" name="password" value="">
								<p class="text-danger">Note: Leave blank if you don't want to change it.</p>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-4 control-label" for="phone">Phone</label>
							<div class="col-md-8"  id="phoneDiv">
								
								@php if(old('phone')) {
									$phones = old('phone');
								} else {
									$phones = json_decode($user->phone);
								}
								if(!empty($phones)) { 
									foreach ($phones as $phone) { @endphp
										<input class="form-control" type="text" name="phone[]" value="{{ $phone }}" style="margin-bottom:10px;">
									@php }
								}else { @endphp
									<input class="form-control" type="text" name="phone[]" value="">
								@php } @endphp
                                <div id="phoneDivInner"></div>
								<a href="javascript:void(0);" id="add_another_phone" class="pull-right ">Add phone</a>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-4 control-label" for="title">Title*</label>
							<div class="col-md-8">	
								<input type="text" class="form-control" name="title" value="@if(old('title')){{ old('title') }}@else{{ $user->title }}@endif">
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-4 control-label" for="department">Department*</label>
							<div class="col-md-8">
								<input type="text" class="form-control" name="department" value="@if(old('department')){{ old('department') }}@else{{ $user->department }}@endif">
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-4 control-label" for="language_preference">Language Preference</label>
							<div class="col-md-8">
								<select class="form-control" name="language_preference">
									@php
									$languages = App\Language::all();
									if($languages) foreach($languages as $language) { 
										$selected = '';
										if(old('language_preference') == $language->alias_name) {
											$selected = 'selected="selected"';
										}else if($user->language_preference == $language->alias_name) {
											$selected = 'selected="selected"';
										}
									@endphp
									<option value="{{ $language->alias_name }}" {{ $selected }}>{{ $language->name }}</option>
									@php } @endphp
								</select>
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-4">	
								<button type="submit" class="btn btn-primary">Submit</button>
							</div>
						</div>
					</form>               
				</div>
			</div>
		</div>
    </div>
</div>
    <script>
        jQuery(function() {
            jQuery( "#add_another_phone" ).click(function() {
                var count = jQuery("input[name='phone[]']" ).length;
                if(count < 5){
                    jQuery('#phoneDivInner').append('<input class="form-control" type="text" name="phone[]" value="" style="margin-bottom:10px;">');
                }
            });
        });
    </script>
@endsection