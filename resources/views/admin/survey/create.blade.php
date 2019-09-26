@extends('layouts.admin')

@section('content')
<div class="page-title"><h3>Create Survey</h3></div>
<div id="main-wrapper">
	<div class="panel panel-dark">
		<div class="panel-body">
			 <div class="row">  
				@if ($errors->count() > 0)
					<div class="alert alert-danger">
						<ul>
							@foreach($errors->all() as $error)
								<li>{{ $error }}</li>
							@endforeach
						</ul>
					</div>
				@endif          
				<div class="col-lg-8 col-md-8">	
					<form action="{{ route('surveys.store') }}" method="post" enctype="multipart/form-data" class="form-horizontal CreateSurvey">
						{{ csrf_field() }}
						<div class="form-group">
							<div class="col-md-8">	
								<label class="control-label" for="title">Title*</label>
								<input type="text" class="form-control" name="title" value="{{ old('title') }}">
							</div>
						</div>
						<div class="form-group">
                            <div class="col-md-8">	
                                <label class="control-label" for="fiscal_entry">Fiscal entry*</label>
                            </div>
							<div class="col-md-8">
                                <div class="row">
                                    <div class="col-md-6">
                                        <!--<input type="text" class="form-control" name="fiscal_entry" value="{{ old('fiscal_entry') }}">-->
                                        <!--<input type="text" class="form-control" name="fiscal_year" placeholder="YYYY" value="{{ old('fiscal_year') }}">-->
                                        @php $currently_selected = date('Y'); 
										$future_year = date('Y',strtotime('+30 years')); 
										$latest_year = date('Y',strtotime('-1 year')); @endphp
										<select class="form-control" name="fiscal_year">
											<option value="">YYYY</option>
										  	@foreach ( range( $latest_year, $future_year ) as $i ) 
										  		@php $selected = '';
										  		if(old('fiscal_year') == $i) { 
													$selected = 'selected="selected"';
												} @endphp
										    	<option value="{{ $i }}" {{ $selected }}>{{ $i }}</option>
										  	@endforeach
										</select>
                                    </div>
                                    <div class="col-md-6">
                                        <select class="form-control" name="fiscal_month">
                                            <option value="">MM</option>
                                            @for ($i = 1; $i < 13; $i++)
                                                @php $selected = '';
                                                $month = date("F", mktime(0, 0, 0, $i, 10));
                                                if(old('fiscal_month') == $month) { 
                                                    $selected = 'selected="selected"';
                                                } @endphp
                                                <option value="{{ $month }}" {{ $selected }}>{{ sprintf("%02d", $i) }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
						
						<div class="form-group">						
							<div class="col-md-8">	
								<label class="control-label" for="company_id">Related Client (Company)*</label>
								<select class="form-control" name="company_id">
									<option value="">Select Company</option>
									@php
									$companies = App\Company::orderBy('name')->get();
									if($companies) foreach($companies as $company) { 
										$selected = '';
										if(old('company_id') == $company->id) {
											$selected = 'selected="selected"';
										}
									@endphp
										<option value="{{ $company->id }}" {{ $selected }}>{{ $company->name }}</option>
									@php } @endphp
								</select>
							</div>
						</div>
						<div class="form-group stakeholdersGroup">
							<div class="col-md-8">
								<label class="control-label col-md-12 p0" for="company_id">Stakeholders*</label>
						 		<input type="hidden" name="stakeholder_ids" id="stakeholder_ids" value="{{ old('stakeholder_ids') }}">
								<input type="checkbox" id="shChkUnckh"> <label for="shChkUnckh" id="shChkUnckhSpan">Check All</label>
								<label class="control-label pull-right p0">Suggested sample size</label>
								<ul id="stakeholders" class="stakeholders">
									@php $stakeholderIds = explode(',', old('stakeholder_ids')); @endphp
									@php if(old('stakeholderArary')){
										$stakeholders = array();
										$stakeholderArary = old('stakeholderArary');
										foreach($stakeholderArary as $postStakeholders){
											$strStakeholders = explode(' || ', $postStakeholders);
											$stakeholders[] = (object)array('id' => $strStakeholders[0], 'alias_name' => $strStakeholders[1]);
										}
									} @endphp
									@foreach($stakeholders as $stakeholder)
										@php $checked=''; @endphp
										@if(in_array($stakeholder->id, $stakeholderIds))
											@php $checked='checked'; @endphp
										@endif
										<li class="ui-state-default">
											<input type="hidden" name="stakeholderArary[]" value="{{ $stakeholder->id }} || {{ $stakeholder->alias_name }}">
											<input type="checkbox" id="stakeholderFor_{{ $stakeholder->id }}" name="shid" value="{{ $stakeholder->id }}" {{ $checked }}>
											<label for="stakeholderFor_{{ $stakeholder->id }}">{{ $stakeholder->alias_name }}</label>
											<div class=" pull-right" >  
												<input class="check-number" type="number" name="stakeholder_size_{{ $stakeholder->id }}" value="{{ old('stakeholder_size_'.$stakeholder->id) }}" placeholder="Sample Size" min="0">
												<span class="ui-icon ui-icon-arrowthick-2-n-s"></span>
											</div>
										</li>
									@endforeach
								</ul>
							</div>
						</div>
						<div class="form-group stakeholdersGroup">
							<div class="col-md-8">
								<label class="control-label col-md-12 p0" for="esg_ids">ESG's*</label>						
								<input type="hidden" name="esg_ids" id="esg_ids" value="{{ old('esg_ids') }}">
								<input type="checkbox" id="esgChkUnckh"> 
								<label for="esgChkUnckh" id="esgChkUnckhSpan">Check All</label>
								@php $esgIds = explode(',', old('esg_ids'));
								if(old('esgArary')){
									$esgs = array();
									$esgArary = old('esgArary');
									foreach($esgArary as $postEsgs){
										$strEsgs = explode(' || ', $postEsgs);
										$esgs[] = (object)array('id' => $strEsgs[0], 'alias_name' => $strEsgs[1], 'category_id' => $strEsgs[2]);
									}
								} @endphp
								<input type="hidden" id="count_category" value="{{ count($esg_categories) }}">
								@foreach($esg_categories as $esg_category)
									<label class="category-label col-md-12">{{ $esg_category->en_title }}</label>
									<ul id="esg_{{ $esg_category->id }}" class="stakeholders esgs">
									@foreach($esgs as $esg)
										@if($esg_category->id == $esg->category_id)
											@php $checked=''; @endphp
											@if(in_array($esg->id, $esgIds))
												@php $checked='checked'; @endphp
											@endif
											<li class="ui-state-default">
												<input type="hidden" name="esgArary[]" value="{{ $esg->id }} || {{ $esg->alias_name }} || {{ $esg->category_id }}">
												<input type="checkbox" id="stakeholdersFor2_{{ $esg->id }}" name="esgid" value="{{ $esg->id }}" {{ $checked }}>
                                                <label for="stakeholdersFor2_{{ $esg->id }}">{{ $esg->alias_name }}</label>
												<div  class=" pull-right" >  
													<span class="ui-icon ui-icon-arrowthick-2-n-s"></span>
												</div>
                                                
											</li>
										@endif
									@endforeach
									</ul>
								@endforeach
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-8">
								<label class="control-label" for="expiry_date">Expiry Date* </label>
                                <div class="datepickerIcon">
								<input type="text" class="form-control " id="expiry_date" name="expiry_date" value="{{ old('expiry_date') }}">
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
	var count_category = $( "#count_category" ).val();

    function shChkChkbox(){
        var stakeholder_ids = '';
        $("#stakeholders input[name=shid]").each(function () {
            if ($(this).is(':checked')) {
                stakeholder_ids += $(this).val() + ',';
            }
        });
        stakeholder_ids = stakeholder_ids.slice(0, -1);
        $('#stakeholder_ids').val(stakeholder_ids);
    }

    function esgChkChkbox(){
        var esg_ids = '';
        for(var i = 1; i <= count_category; i++ ){
	        $("#esg_"+i+" input[name=esgid]").each(function () {
	            if ($(this).is(':checked')) {
	                esg_ids += $(this).val() + ',';
	            }
	        });
	    }
        esg_ids = esg_ids.slice(0, -1);
        $('#esg_ids').val(esg_ids);
    }

    $(function() {

        $("#stakeholders input[name=shid]").click(function () {
            shChkChkbox();
        });
        for(var i = 1; i <= count_category; i++ ){
	        $("#esg_"+i+" input[name=esgid]").click(function () {
	            esgChkChkbox();
	        });
    	}

        $("#shChkUnckh").click(function () {
            if ($("#shChkUnckh").is(':checked')) {
                $("#stakeholders input[name=shid]").each(function () {
                    $(this).prop("checked", true);
                });
                $('#shChkUnckhSpan').text('Uncheck All');
            } else {
                $("#stakeholders input[name=shid]").each(function () {
                    $(this).prop("checked", false);
                });
                $('#shChkUnckhSpan').text('Check All');
            }
            shChkChkbox();
        });

        $("#esgChkUnckh").click(function () {
            if ($("#esgChkUnckh").is(':checked')) {
            	for(var i = 1; i <= count_category; i++ ){
	                $("#esg_"+i+" input[name=esgid]").each(function () {
	                    $(this).prop("checked", true);
	                });
            	}
                $('#esgChkUnckhSpan').text('Uncheck All');
            } else {
            	for(var i = 1; i <= count_category; i++ ){
	                $("#esg_"+i+" input[name=esgid]").each(function () {
	                    $(this).prop("checked", false);
	                });
            	}
                $('#esgChkUnckhSpan').text('Check All');
            }
            esgChkChkbox();
        });

        $( "#stakeholders" ).sortable({
            update: function( event, ui ) { 
                var stakeholder_ids = '';
                $(this).find('li').each(function( index ) {
                    var sid = $(this).find("input[name='shid']:checked").val();
                    if(sid != null) {
                        stakeholder_ids += sid + ',';
                    }
                });
                stakeholder_ids = stakeholder_ids.slice(0, -1);
                $('#stakeholder_ids').val(stakeholder_ids);
            }
        });

        for(var i = 1; i <= count_category; i++ ){
	        $( "#esg_"+i ).sortable({
	            update: function( event, ui ) { 
	                var esg_ids = '';
	                $('.esgs').find('li').each(function( index ) {
	                    var sid = $(this).find("input[name='esgid']:checked").val();
	                    if(sid != null) {
	                        esg_ids += sid + ',';
	                    }
	                });
	                esg_ids = esg_ids.slice(0, -1);
	                $('#esg_ids').val(esg_ids);
	            }
	        });
	    }
		$( "#expiry_date" ).datepicker();
    });
</script>
@endsection