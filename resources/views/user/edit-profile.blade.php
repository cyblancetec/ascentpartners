@extends('layouts.app')

@section('content')

<div class="page-inner page-login displaytable">	
	<div class="displaytable-cell">
		<div class="container">
			<div class="row">
				<div class="col-md-8 center">
					<div class="edit-box">
						<div class="page-title"><h3>{{ trans('messages.EditProfile') }}</h3></div>
						@if ($errors->count() > 0)
							<div class="alert alert-danger">
								<ul>
									@foreach($errors->all() as $error)
										<li>{{ $error }}</li>
									@endforeach
								</ul>
							</div>
						@endif
						    
						<div class="login-box-inner">
                            @if (\Session::has('success'))
                                <div class="alert alert-success">
                                    <p>{{ \Session::get('success') }}</p>
                                </div>
                            @endif 
                            <form method="post" enctype="multipart/form-data">
                                {{ csrf_field() }}
                                <input type="hidden" name="_method" value="PUT">
                                <div class="row">
                                    <div class="form-group col-md-6 col-sm-6">
                                        <label for="first_name">{{ trans('messages.FirstName') }}*</label>
                                        <input type="text" class="form-control" name="first_name" value="@if(old('first_name')){{ old('first_name') }}@else{{ $user->first_name }}@endif">
                                    </div>			
                                    <div class="form-group col-md-6 col-sm-6">
                                        <label for="last_name">{{ trans('messages.LastName') }}</label>
                                        <input type="text" class="form-control" name="last_name" value="@if(old('last_name')){{ old('last_name') }}@else{{ $user->last_name }}@endif">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-6 col-sm-6">
                                        <label for="email">{{ trans('messages.Email') }}*</label>
                                        <input type="email" class="form-control" name="email" value="@if(old('email')){{ old('email') }}@else{{ $user->email }}@endif">
                                    </div>
                                    <div class="form-group col-md-6 col-sm-6">
                                        <label for="phone">{{ trans('messages.Phone') }}</label>
                                        <a href="javascript:void(0);" id="add_another_phone" class="pull-right">{{ trans('messages.AddPhone') }}</a>
                                        @php if(old('phone')) {
                                            $phones = old('phone');
                                        } else {
                                            $phones = json_decode($user->phone);
                                        }
                                        if(!empty($phones)) { 
                                            foreach ($phones as $phone) { @endphp
                                                <input class="form-control" type="text" name="phone[]" value="{{ $phone }}">
                                            @php }
                                        }else { @endphp
                                            <input class="form-control" type="text" name="phone[]" value="">
                                        @php } @endphp
                                        <div id="phoneDiv"></div>

                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group col-md-6 col-sm-6">
                                        <label for="title">{{ trans('messages.Title') }}*</label>
                                        <input type="text" class="form-control" name="title" value="@if(old('title')){{ old('title') }}@else{{ $user->title }}@endif">
                                    </div>
                                    <div class="form-group col-md-6 col-sm-6">
                                        <label for="department">{{ trans('messages.Department') }}*</label>
                                        <input type="text" class="form-control" name="department" value="@if(old('department')){{ old('department') }}@else{{ $user->department }}@endif">
                                    </div>
                                </div>						
                                <div class="row">
                                    <div class="form-group col-md-6 col-sm-6">
                                        <label for="language_preference">{{ trans('messages.LanguagePreference') }}</label>
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
                                    <div class="form-group col-md-6 col-sm-6">
                                        {{--
                                        <label for="company_id">{{ trans('messages.Company') }}*</label>
                                        <select class="form-control" name="company_id">
                                            <option value="">{{ trans('messages.SelectCompany') }}</option>
                                            @php
                                            $companies = App\Company::all()->sortBy('name');
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
                                        --}}
                                    </div>
                                </div>
                                <div class="row">								
                                    <div class="form-group col-md-4">
                                        <button type="submit" name="submit" class="btn btn-primary">{{ trans('messages.Submit') }}</button>
                                    </div>
                                </div>
                            </form>      
						</div>
 					</div>
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
                    jQuery('#phoneDiv').append('<input class="form-control" type="text" name="phone[]" value="" style="margin-top:10px;">');
                }
            });
        });
    </script>
@endsection