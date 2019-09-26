@extends('layouts.admin')

@section('content')
<div class="page-title"><h3>Create Client</h3></div>
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
				<div class="col-lg-6 col-md-8">	
					<form action="{{ route('companies.store') }}" method="post" enctype="multipart/form-data" class="form-horizontal">
						{{ csrf_field() }}
						<div class="form-group">
							<label class="col-md-4 control-label" for="name">Company Name*</label>
							<div class="col-md-8">	
								<input type="text" class="form-control" name="name" value="{{ old('name') }}">
							</div>
						</div>
						<div class="form-group">	
							<label class="col-md-4 control-label" for="stock_code">Stock Code*</label>
							<div class="col-md-8">	
								<input type="text" class="form-control" name="stock_code" value="{{ old('stock_code') }}">
							</div>
						</div>
						<div class="form-group">	
							<label class="col-md-4 control-label" for="industry_type">Sector*</label>
							<div class="col-md-8">								
								<select name="industry_type" class="form-control">
                                    <option value="">Select Sector</option>
                                    @foreach($industryTypes as $industryType)
                                        @php $selected = ''; @endphp
                                        @if(old('industry_type') == $industryType)
                                            @php $selected = 'selected'; @endphp
                                        @endif
                                    <option value="{{ $industryType }}" {{ $selected }}>{{ $industryType }}</option>
                                    @endforeach
                                </select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-4 control-label" for="fiscal_year">Fiscal Year End Month*</label>
							<div class="col-md-8">
                                <div class="datepickerIcon">
								    <input type="text" class="form-control" id="fiscal_year" name="fiscal_year" value="{{ old('fiscal_year') }}">
                                </div>
							</div>
						</div>
						<!--<div class="form-group">
							<label class="col-md-4 control-label" for="contact_person"><b>Contact Person</b></label>
							<div class="col-md-8">&nbsp;</div>
						</div>
						<div class="form-group">
							<label class="col-md-4 control-label" for="first_name">First Name*</label>
							<div class="col-md-8">	
								
								<input type="text" class="form-control" name="first_name" value="{{ old('first_name') }}">
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-4 control-label" for="last_name">Last Name</label>
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
							<div class="col-md-8" id="phoneDiv">
								
								
								@php if(old('phone')) {
									$phones = old('phone');
								}
								if(!empty($phones)) { 
									foreach ($phones as $phone) { @endphp
										<input class="form-control" type="text" name="phone[]" value="{{ $phone }}" style="margin-bottom:10px;">
									@php }
								}else { @endphp
										<input class="form-control" type="text" name="phone[]" value="" style="margin-bottom:10px;">
								@php } @endphp
                                <div id="phoneDivInner"></div>
                                <a href="javascript:void(0);" id="add_another_phone" class="pull-right">Add phone</a>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-4 control-label" for="title">Title*</label>
							<div class="col-md-8">	
								
								<input type="text" class="form-control" name="title" value="{{ old('title') }}">
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-4 control-label" for="department">Department*</label>
							<div class="col-md-8">	
								<input type="text" class="form-control" name="department" value="{{ old('department') }}">
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
						</div>-->
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
	$(function() {
		$( "#fiscal_year" ).datepicker();
	});
</script>
@endsection