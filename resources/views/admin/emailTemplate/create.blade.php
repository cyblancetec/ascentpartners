@extends('layouts.admin')

@section('content')
<script src="{{ url('/vendor/unisharp/laravel-ckeditor/ckeditor.js') }}"></script>
<div class="page-title"><h3>Create Email Template</h3></div>         
<div id="main-wrapper">
	<div class="panel panel-dark">
		<div class="panel-body">
			 <div class="row">
                <div class="col-lg-12 col-md-12">
                    @if ($errors->count() > 0)
                        <div class="alert alert-danger">
                            <ul>
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                 </div>
				 <div class="col-lg-8 col-md-8">
					<div role="tabpanel">
                        <form action="{{ route('email-templates.store') }}" method="post" enctype="multipart/form-data" class="form-horizontal">
                            {{ csrf_field() }}
                            <div class="form-group">
                                <div class="col-md-8">
                                    <label class="control-label pb7" for="alias_name">Email Template*</label>
                                    <select name="alias_name" class="form-control">
                                        <option value="">Select Template</option>
                                        @foreach($templateNames as $key => $value)
                                            @php $selected = ''; @endphp
                                            @if(old('alias_name') == $key)
                                                @php $selected = 'selected'; @endphp
                                            @endif
                                        <option value="{{ $key }}" {{ $selected }}>{{ $value }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-12">
                                    <label class="control-label pb7" for="alias_name">Available Variables</label>
                                    <div class="col-lg-12 p0 sortcodeEmail" > 
                                        <span>[FISCAL_YEAR]</span> <span>[COMPANY]</span> <span>[SURVEY_URL]</span> <span>[FIRST_NAME]</span> <span>[USER_EMAIL]</span> <span>[USER_PASSWORD]</span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group"></div>
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
                                    <div class="form-group">									
                                        <div class="col-md-12">
                                            <label class="control-label pb7" for="subject_{{ $language->alias_name }}">{{ $language->name }} Subject*</label>
                                            <input type="text" class="form-control" name="subject_{{ $language->alias_name }}" value="{{ old('subject_'.$language->alias_name) }}">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-12">			
                                            <label class="pb7 control-label" for="content_{{ $language->alias_name }}">{{ $language->name }} Content*</label>
                                            <textarea class="form-control" name="content_{{ $language->alias_name }}" rows="20">{{ old('content_'.$language->alias_name) }}</textarea>
                                        </div>
                                    </div>                                    
                                </div>
                                <script>
                                    CKEDITOR.replace( 'content_{{ $language->alias_name }}' );
                                </script>
                            @php $i++; } @endphp
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