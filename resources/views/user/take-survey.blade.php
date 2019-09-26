@extends('layouts.app')

@section('content')
<div class="page-inner page-login displaytable">	
	<div class="displaytable-cell">
		<div class="container">
			<div class="row">
				<div class="col-md-6 center">
					<div class="login-box">
						 <div class="page-title"><h3>{{ trans('messages.TakeSurvey') }}</h3></div>
						 <div class="login-box-inner">
							 @if ($errors->count() > 0)
								<div class="alert alert-danger">
									<ul>
										@foreach($errors->all() as $error)
											<li>{{ $error }}</li>
										@endforeach
									</ul>
								</div>
							@endif
							<form method="post">
								{{ csrf_field() }}
								<div class="form-group">								
									<input type="text" name="email" placeholder="{{ trans('messages.EMailAddress') }}" class="form-control " value="{{ old('email') }}" />
								</div>
								<div class="form-group">								
									<select name="survey" class="form-control ">
										<option value="">{{ trans('messages.SelectSurvey') }}</option>
										@foreach($surveys as $survey)
										@php $selected = ($survey->id == old('survey') ? 'selected' : ''); @endphp
										<option value="{{ $survey->id }}" {{ $selected }}>{{ $survey->title }}</option>
										@endforeach
									</select>
								</div>
								<input type="submit" name="proceed" value="{{ trans('messages.Proceed') }}" class="btn  btn-primary ">
							</form>
						</div>  
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
