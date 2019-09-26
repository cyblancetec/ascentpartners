@extends('layouts.admin')

@section('content')
<div class="page-title"><h3>Update ESG Aspects</h3></div>
<div id="main-wrapper">
	<div class="panel panel-dark">
		<div class="panel-body">
			<div class="row">
                @if ($errors->count() > 0)
                    <div class="error-msg" >
                        <ul>
                            <li class="alert alert-danger">
                            @foreach($errors->all() as $error)
                                {{ $error }}<br>
                            @endforeach
                            </li>
                        </ul>
                    </div>
                @endif
				<div class="col-lg-6 col-md-8">
					<div role="tabpanel">
                        <form action="{{ route('esg.update', $esg->id) }}" method="post" enctype="multipart/form-data" class="form-horizontal">
                            {{ csrf_field() }}
                            <input type="hidden" name="_method" value="PUT">
                            <div class="form-group ">
								<div class="col-md-6">
                                    <label class="control-label pb7" for="alias_name">ESG Aspect*</label>
                                    <input type="text" class="form-control" name="alias_name" value="@if(old('alias_name')){{ old('alias_name') }}@else{{ $esg->alias_name }}@endif">
                                </div>
                                <div class="col-md-6">
                                    <label class=" control-label pb7" for="alias_name">Category*</label>
                                    <select name="category_id" class="form-control">
                                        <option value="">Select Category</option>
                                        @foreach($esg_categories as $esg_category)
                                            <option value="{{ $esg_category->id }}" @if(old('category_id')==$esg_category->id){{ 'selected' }}@elseif($esg->category_id==$esg_category->id){{ 'selected' }}@endif>{{ $esg_category->en_title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group "></div>
                            <ul class="nav nav-tabs" role="tablist">
                                <li role="presentation" class="active"><a href="#tab1" role="tab" data-toggle="tab">English</a></li>
                                <li role="presentation"><a href="#tab2" role="tab" data-toggle="tab">Simplified Chinese</a></li>
                                <li role="presentation"><a href="#tab3" role="tab" data-toggle="tab">Traditional Chinese </a></li>
                            </ul>
                            <div class="tab-content">
                                @php $languages = App\Language::all();
                                $i=1;
                                if($languages) foreach($languages as $language) { 
                                    $translation = App\EsgTranslation::where('esg_id', $esg->id)
                                                            ->where('locale', $language->alias_name)
                                                            ->first();
                                @endphp
                                <div role="tabpanel" class="tab-pane @if($i==1) active @endif fade in" id="tab{{ $i }}">
                                    <div class="form-group">
									   <div class="col-md-12">
                                           <label class="control-label pb7" for="alias_name">Title*</label>
                                            <input type="hidden" name="tid_{{ $language->alias_name }}" value="{{ $translation->id }}">
                                            <input type="text" class="form-control" name="title_{{ $language->alias_name }}" value="@if(old('title_'.$language->alias_name)){{ old('title_'.$language->alias_name) }}@else{{ $translation->title }}@endif">
                                        </div>
                                    </div>
                                    <div class="form-group">
									   <div class="col-md-12">
                                           <label for="information" class="control-label pb7">Description*</label>
                                            <textarea class="form-control" name="information_{{ $language->alias_name }}">@if(old('information_'.$language->alias_name)){{ old('information_'.$language->alias_name) }}@else{{ $translation->information }}@endif</textarea>
                                        </div>
                                    </div>
                                </div>
                                @php $i++; } @endphp
                            </div>
							<div class="form-group">
								<div class="col-md-12">
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