@extends('layouts.admin')
@section('content')
<div class="page-title"><h3>Update Client</h3></div>  
<div id="main-wrapper">
	<div class="panel panel-dark">
		<div class="panel-body">
			 <div class="row">
			 	@if ($errors->count() > 0)
				  <div class="alert alert-danger" >
						<ul>
							@foreach($errors->all() as $error)
								<li>{{ $error }}</li>
							@endforeach
						</ul>
					</div>
		        @endif
			 	<div class="col-lg-6 col-md-8">	
					<form action="{{ route('companies.update', $company->id) }}" method="post" enctype="multipart/form-data" class="form-horizontal">
						{{ csrf_field() }}
						<input type="hidden" name="_method" value="PUT">
						<div class="form-group">
							<label class="col-md-4 control-label" for="name">Company Name*</label>
							<div class="col-md-8">							
								<input type="text" class="form-control" name="name" value="@if(old('name')){{ old('name') }}@else{{ $company->name }}@endif">
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-4 control-label" for="stock_code">Stock Code*</label>
							<div class="col-md-8">
								<input type="text" class="form-control" name="stock_code" value="@if(old('stock_code')){{ old('stock_code') }}@else{{ $company->stock_code }}@endif">
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-4 control-label" for="industry_type">Sector*</label>
							<div class="col-md-8">							
								<select name="industry_type" class="form-control">
                                    <option value="">Select Sector</option>
                                    @foreach($industryTypes as $industryType)
                                        @php $selected = ''; 
										if(old('industry_type') == $industryType){
											$selected = 'selected="selected"';
										}else if($company->industry_type == $industryType) {
											$selected = 'selected="selected"';
										} @endphp
                                    <option value="{{ $industryType }}" {{ $selected }}>{{ $industryType }}</option>
                                    @endforeach
                                </select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-4 control-label" for="fiscal_year">Fiscal Year End Month*</label>
							<div class="col-md-8">
                                <div class="datepickerIcon">
								    <input type="text" class="form-control" id="fiscal_year" name="fiscal_year" value="@if(old('fiscal_year')){{ old('fiscal_year') }}@else{{ $company->fiscal_year }}@endif">
                                </div>
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
	$(function() {
		$( "#fiscal_year" ).datepicker();
	});
</script>
@endsection