@extends('layouts.app')

@section('content')
<link href="{{ asset('css/jquery.tag-editor.css') }}" rel="stylesheet">
<script src="{{ asset('js/jquery.tag-editor.js') }}"></script>
<div class="page-inner page-login displaytable">	
	<div class="displaytable-cell">
		<div class="container">
			<div class="row">
				<div class="col-md-12">
					<div class="row SurveyTakerMessages">
                        @if ($success!='')
                            <div class="col-md-6 inline-block">
                               <div class="alert alert-success">{!! $success !!}</div>
                            </div>
                        @endif
                        @if ($error!='')
                            <div class="col-md-6 inline-block">
                               <div class="alert alert-danger">{!! $error !!}</div>
                            </div>
                        @endif
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-6 center">
					<div class="login-box">
						<div class="page-title"><h3>{{ trans('messages.SurveyTaker') }}</h3></div>
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
							@if (\Session::has('success'))
								<div class="alert alert-success">
									<p>{{ \Session::get('success') }}</p>
								</div>
							@endif
							<form method="post">
								{{ csrf_field() }}
								<div class="form-group">									
									<!--<input type="text" name="email" id="email" placeholder="{{ trans('messages.EMailAddress') }}" class="form-control" value="{{ old('email') }}" />-->
									<!--<input type="text" name="email" id="email" class="form-control" value="{{ old('email') }}" />-->
									<textarea name="email" id="email" class="form-control" style="height: auto;">{{ old('email') }}</textarea>
									<span class="text-info">Note: For add multiple email please use comma sign.</span>
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
<script>
	$(function() {
        $('#email').tagEditor({
        	delimiter: ', ',
        	maxTags: 20,
            forceLowercase: true,
            placeholder: "{{ trans('messages.EMailAddress') }}",
            beforeTagSave: function(field, editor, tags, tag, val) {
            	var filter = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
            	if (!filter.test(val)) {
            		return false;
            	}
		    },
        });
    });
</script>
@endsection
