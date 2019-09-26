@extends('layouts.admin')

@section('content')
<div class="page-title"><h3>Update Admin</h3></div>
<div id="main-wrapper">
	<div class="panel panel-dark">
		<div class="panel-body">
			 <div class="row">
			 	@if ($errors->count() > 0)
					<div class="alert alert-danger" >
						<ul>
							@foreach($errors->all() as $error)
								<li> {{ $error }}</li>
							@endforeach
						</ul>
					</div>
				@endif 
			 	<div class="col-lg-5 col-md-5">	
        			<form action="{{ route('admins.update', $admin->id) }}" method="post" enctype="multipart/form-data" class="form-horizontal">
						{{ csrf_field() }}
						<input type="hidden" name="_method" value="PUT">
						<div class="form-group">
							<label class="col-md-4 control-label" for="first_name">First Name*</label>
							<div class="col-md-8">
								<input type="text" class="form-control" name="first_name" value="@if(old('first_name')){{ old('first_name') }}@else{{ $admin->first_name }}@endif">
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-4 control-label" for="last_name">Last Name*</label>
							<div class="col-md-8">							
								<input type="text" class="form-control" name="last_name" value="@if(old('last_name')){{ old('last_name') }}@else{{ $admin->last_name }}@endif">
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-4 control-label"  for="email">Email*</label>
							<div class="col-md-8">					
								<input type="email" class="form-control" name="email" value="@if(old('email')){{ old('email') }}@else{{ $admin->email }}@endif">
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
							<div class="col-md-8">
								<input type="text" class="form-control" name="phone" value="@if(old('phone')){{ old('phone') }}@else{{ $admin->phone }}@endif">
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-4 control-label" for="title">Title*</label>
							<div class="col-md-8">
								<input type="text" class="form-control" name="title" value="@if(old('title')){{ old('title') }}@else{{ $admin->title }}@endif">
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
										}else if($admin->language_preference == $language->alias_name) {
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
@endsection