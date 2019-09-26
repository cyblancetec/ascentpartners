@extends('layouts.admin')

@section('content')


<div class="page-title"><h3>Create Stakeholder</h3></div>   
<div id="main-wrapper">
	<div class="panel panel-dark">
		<div class="panel-body">
			@if ($errors->count() > 0)
				<div class="alert alert-danger">
					<ul>
						@foreach($errors->all() as $error)
							<li>{{ $error }}</li>
						@endforeach
					</ul>
				</div>
			@endif 
			<div class="row">
				<div class="col-lg-7 col-md-10">
					<div role="tabpanel">
						<!-- Nav tabs -->
						<form action="{{ route('stakeholders.store') }}" method="post" enctype="multipart/form-data" class="form-horizontal">
							{{ csrf_field() }}
							<div class="form-group ">					
								<div class="col-md-12">
									<label class="control-label pb7" for="alias_name">Stakeholder*</label>
									<input type="text" class="form-control" name="alias_name" value="{{ old('alias_name') }}">
								</div>
							</div>
							<ul class="nav nav-tabs" role="tablist">
								<li role="presentation" class="active"><a href="#tab1" role="tab" data-toggle="tab">English</a></li>
								<li role="presentation"><a href="#tab2" role="tab" data-toggle="tab">Simplified Chinese</a></li>
								<li role="presentation"><a href="#tab3" role="tab" data-toggle="tab">Traditional Chinese </a></li>
							</ul>
							<div class="tab-content">
								@php $languages = App\Language::all();
								$i=1;
								if($languages) foreach($languages as $language) { @endphp
								<div role="tabpanel" class="tab-pane @if($i==1) active @endif fade in" id="tab{{ $i }}">
									<div class="form-group ">
										<div class="col-md-8">
											<label class="control-label pb7" for="alias_name">Title*</label>
											<input type="text" class="form-control" name="title_{{ $language->alias_name }}" value="{{ old('title_'.$language->alias_name) }}">
										</div>
									</div>
								</div>
								@php $i++; } @endphp
							</div>
							<div class="form-group">							
								<div class="col-md-12">
									<label class="control-label pb7" for="textbox_support_required">Text box support required*</label>
                                    <label class="control-label checkbox-inline"><input type="radio" name="textbox_support_required" value="yes" @if(old('textbox_support_required')=='yes') {{ 'checked' }} @endif> Yes</label>
                                    <label class="control-label checkbox-inline"><input type="radio" name="textbox_support_required" value="no" @if(old('textbox_support_required')=='no') {{ 'checked' }} @endif> No</label>
								</div>
							</div>
							<div class="form-group">							
								<div class="col-md-12">
									<label class="control-label pb7"  for="survey_choice">Choice for survey</label>
									<select name="survey_choice" class="form-control">
										<option value="Stakeholder only" @if(old('survey_choice')=='Stakeholder only') {{ 'selected' }} @endif>Stakeholder only</option>
										<option value="Stakeholder and Company" @if(old('survey_choice')=='Stakeholder and Company') {{ 'selected' }} @endif>Stakeholder and Company</option>
									</select>
								</div>
							</div>	
							<div class="form-group ">
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
</div>
@endsection