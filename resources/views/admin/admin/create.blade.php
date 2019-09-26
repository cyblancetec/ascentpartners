@extends('layouts.admin')

@section('content')
  <div class="page-title"><h3>Create Admin</h3></div>
         
		<div id="main-wrapper">
			<div class="panel panel-dark">
				<div class="panel-body">
					 <div class="row">
						 @if ($errors->count() > 0)
							<div class="alert alert-danger">
								<ul>
									@foreach($errors->all() as $error)
										<li> {{ $error }}</li>
									@endforeach
								</ul>
							</div>
						@endif    
						<div class="col-lg-5 col-md-5">	
							<form action="{{ route('admins.store') }}" method="post" enctype="multipart/form-data" class="form-horizontal">
								{{ csrf_field() }}
								<div class="form-group">
									<label class="col-md-4 control-label" for="first_name">First Name*</label>
									<div class="col-md-8">
										<input type="text" class="form-control" name="first_name" value="{{ old('first_name') }}">
									</div>
								</div>
								<div class="form-group">
									<label class="col-md-4 control-label" for="last_name">Last Name*</label>
									<div class="col-md-8">									
										<input type="text" class="form-control" name="last_name" value="{{ old('last_name') }}">
									</div>
								</div>
								<div class="form-group">
									<label class="col-md-4 control-label" for="email">Email*</label>
									<div class="col-md-8">
										<input type="email" class="form-control" name="email" value="{{ old('email') }}">
									</div>
								</div>
								<div class="form-group">
									<label class="col-md-4 control-label" for="password">Password*</label>
									<div class="col-md-8">
										<input type="password" class="form-control" name="password" value="">
									</div>
								</div>
								<div class="form-group">
									<label class="col-md-4 control-label" for="password_confirmation">Confirm Password*</label>
									<div class="col-md-8">
										<input type="password" class="form-control" name="password_confirmation" value="">
									</div>
								</div>
								<div class="form-group">		
									<label class="col-md-4 control-label" for="phone">Phone</label>
									<div class="col-md-8">									
										<input type="text" class="form-control" name="phone" value="{{ old('phone') }}">
									</div>
								</div>
								<div class="form-group">	
									<label class="col-md-4 control-label" for="title">Title*</label>
									<div class="col-md-8">								
										<input type="text" class="form-control" name="title" value="{{ old('title') }}">
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