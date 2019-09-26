@extends('layouts.admin')

@section('content')

<div class="page-title"><h3>Update Survey</h3></div>
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
					<form action="{{ route('surveys.update', $survey->id) }}" method="post" enctype="multipart/form-data" class="form-horizontal CreateSurvey">
					{{ csrf_field() }}
						<input type="hidden" name="_method" value="PUT">
						<input type="hidden" name="id" value="{{ $survey->id }}">
						<div class="form-group">
							<div class="col-md-8">	
								<label class="control-label" for="title">Title*</label>
								<input type="text" class="form-control" name="title" value="@if(old('title')){{ old('title') }}@else{{ $survey->title }}@endif">
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-8">	
								<label class="control-label" for="fiscal_entry">Fiscal entry*</label>
                            </div>
                            <div class="col-md-8">
                                <div class="row">
                                <div class="col-md-6">
                                    <!--<input type="text" class="form-control" name="fiscal_entry" value="@if (old('fiscal_entry')) {{ old('fiscal_entry') }} @else {{ $survey->fiscal_entry }} @endif">-->
                                    @php $fiscal_entry = explode(' / ', $survey->fiscal_entry); @endphp
                                    <!--<input type="text" class="form-control" name="fiscal_year" placeholder="YYYY" value="@if (old('fiscal_year')) {{ old('fiscal_year') }} @else {{ $fiscal_entry[0] }} @endif">-->
                                    @php $currently_selected = date('Y'); 
									$future_year = date('Y',strtotime('+30 years')); 
									$latest_year = date('Y',strtotime('-1 year')); @endphp
                                    <select class="form-control" name="fiscal_year">
										<option value="">YYYY</option>
									  	@foreach ( range( $latest_year, $future_year ) as $i ) 
									  		@php $selected = '';
									  		if(old('fiscal_year') == $i) { 
												$selected = 'selected="selected"';
											}elseif($fiscal_entry[0] == $i){
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
                                            @php $month = date("F", mktime(0, 0, 0, $i, 10)); 
                                            if(old('fiscal_month')) { 
                                                $fiscal_month=old('fiscal_month');
                                            }else{ 
                                                $fiscal_month=$fiscal_entry[1]; 
                                            }
                                            $selected = ($fiscal_month == $month ? 'selected="selected"' : ''); @endphp
                                            <option value="{{ $month }}" {{ $selected }}>{{ sprintf("%02d", $i) }}</option>
                                        @endfor
                                    </select>
                                </div>
                                </div>
                            </div>
						</div>
						<div class="form-group">
							<div class="col-md-8">	
								<label class="control-label" for="company_id">Related Client(Company)*</label>
								<select class="form-control" name="company_id">
									<option value="">Select Company</option>
									@php
									$companies = App\Company::orderBy('name')->get();
									if($companies) foreach($companies as $company) { 
										$selected = '';
										if(old('company_id') == $company->id) {
											$selected = 'selected="selected"';
										}else if($survey->company_id == $company->id) {
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
								@php $stakeholderIds = App\SurveyStakeholder::select('stakeholder_id', 'sample_size')->where('survey_id', $survey->id)->get();
								$shIdStr = '';
								foreach($stakeholderIds  as $stakeholderId){
									$shIdStr .= $stakeholderId->stakeholder_id.',';
								}
								$shIdStr = substr($shIdStr, 0, -1);
								@endphp
								<input type="hidden" name="stakeholder_ids" id="stakeholder_ids" value="@if(old('stakeholder_ids')){{ old('stakeholder_ids') }}@else{{ $shIdStr }}@endif">
								<input type="checkbox" id="shChkUnckh"> 
								<label for="shChkUnckh" id="shChkUnckhSpan">Check All</label>
								<label class="control-label pull-right p0">Suggested sample size</label>
								<ul id="stakeholders" class="stakeholders">
									@php if(old('stakeholderArary')){
										$stakeholderIds = explode(',', old('stakeholder_ids')); 
										$stakeholders = array();
										$stakeholderArary = old('stakeholderArary');
										foreach($stakeholderArary as $postStakeholders){
											$strStakeholders = explode(' || ', $postStakeholders);
											$stakeholders[] = (object)array('id' => $strStakeholders[0], 'alias_name' => $strStakeholders[1]);
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
													<input type="number" class="check-number" name="stakeholder_size_{{ $stakeholder->id }}" value="{{ old('stakeholder_size_'.$stakeholder->id) }}" placeholder="Sample Size" min="0">
													<span class="ui-icon ui-icon-arrowthick-2-n-s"></span>
												</div>
											</li>
										@endforeach
									@php }else{ @endphp
										@php $shIdArray = array();
										foreach($stakeholderIds  as $stakeholderId){
											$shId = $stakeholderId->stakeholder_id;
											$shIdArray[] = $shId;
											$alias_name = App\Stakeholder::select('alias_name')->where('id', $shId)->first();
											@endphp
											<li class="ui-state-default">
												<input type="hidden" name="stakeholderArary[]" value="{{ $shId }} || {{ $alias_name->alias_name }}">
												<input type="checkbox" id="stakeholderFor_{{ $shId }}" name="shid" value="{{ $shId }}" checked>
                                                <label for="stakeholderFor_{{ $shId }}">{{ $alias_name->alias_name }}</label>
												<div class=" pull-right" >  
													<input type="number" class="check-number" name="stakeholder_size_{{ $shId }}" value="{{ $stakeholderId->sample_size }}" placeholder="Sample Size" min="0">
													<span class="ui-icon ui-icon-arrowthick-2-n-s"></span>
												</div>
											</li>
										@php } @endphp
										@foreach($stakeholders as $stakeholder)
											@if(!in_array($stakeholder->id, $shIdArray))
											<li class="ui-state-default">
												<input type="hidden" name="stakeholderArary[]" value="{{ $stakeholder->id }} || {{ $stakeholder->alias_name }}">
												<input type="checkbox" name="shid" value="{{ $stakeholder->id }}">
												{{ $stakeholder->alias_name }}
												<div class=" pull-right" >
													<input type="number" class="check-number" name="stakeholder_size_{{ $stakeholder->id }}" placeholder="Sample Size" min="0">
													<span class="ui-icon ui-icon-arrowthick-2-n-s"></span>
												</div>
											</li>
											@endif
										@endforeach
									@php } @endphp
								</ul>
							</div>
						</div>
						<div class="form-group stakeholdersGroup">
							<div class="col-md-8">	
								<label class="control-label col-md-12 p0"  for="company_id">ESG's*</label>
								@php $esgIds = App\SurveyEsg::select('esg_id')->where('survey_id', $survey->id)->get();
								$esgIdStr = '';
								foreach($esgIds  as $esgId){
									$esgIdStr .= $esgId->esg_id.',';
								}
								$esgIdStr = substr($esgIdStr, 0, -1);
								@endphp
								<input type="hidden" name="esg_ids" id="esg_ids" value="@if(old('esg_ids')){{ old('esg_ids') }}@else{{ $esgIdStr }}@endif">
								<input type="checkbox" id="esgChkUnckh"> <label for="esgChkUnckh" id="esgChkUnckhSpan">Check All</label>
								<input type="hidden" id="count_category" value="{{ count($esg_categories) }}">
								@foreach($esg_categories as $esg_category)
									<label class="category-label col-md-12">{{ $esg_category->en_title }}</label>
									<ul id="esg_{{ $esg_category->id }}" class="stakeholders esgs">
										@php if(old('esgArary')){
											$esgIds = explode(',', old('esg_ids')); 
											$esgs = array();
											$esgArary = old('esgArary');
											foreach($esgArary as $postEsgs){
												$strEsgs = explode(' || ', $postEsgs);
												$esgs[] = (object)array('id' => $strEsgs[0], 'alias_name' => $strEsgs[1], 'category_id' => $strEsgs[2]);
											} @endphp
											@foreach($esgs as $esg)
												@if($esg_category->id == $esg->category_id)
													@php $checked=''; @endphp
													@if(in_array($esg->id, $esgIds))
														@php $checked='checked'; @endphp
													@endif
													<li class="ui-state-default">
														<input type="hidden" name="esgArary[]" value="{{ $esg->id }} || {{ $esg->alias_name }} || {{ $esg->category_id }}">
														<input type="checkbox" id="esgsFor2_{{ $esg->id }}" name="esgid" value="{{ $esg->id }}" {{ $checked }}>
                                                        <label for="esgsFor2_{{ $esg->id }}">{{ $esg->alias_name }}</label>
														<div  class="pull-right" > 
															<span class="ui-icon ui-icon-arrowthick-2-n-s"></span>
														</div>
                                                        
													</li>
												@endif
											@endforeach
										@php }else{ @endphp
											@php $esgIdArray = array();
											foreach($esgIds  as $esgId){
												$esgId = $esgId->esg_id;
												$esgIdArray[] = $esgId;
												$alias_name = App\Esg::select('alias_name','category_id')->where('id', $esgId)->first();
												@endphp
												@if($esg_category->id == $alias_name->category_id)
													<li class="ui-state-default">
														<input type="hidden" name="esgArary[]" value="{{ $esgId }} || {{ $alias_name->alias_name }} || {{ $alias_name->category_id }}">
														<input type="checkbox" id="esgsFor2_{{ $esgId }}" name="esgid" value="{{ $esgId }}" checked>
														<div  class="pull-right" > 
															<span class="ui-icon ui-icon-arrowthick-2-n-s"></span>
														</div>
                                                        <label for="esgsFor2_{{ $esgId }}">{{ $alias_name->alias_name }}</label>
													</li>
												@endif
											@php } @endphp
											@foreach($esgs as $esg)
												@if(!in_array($esg->id, $esgIdArray))
													@if($esg_category->id == $esg->category_id)
														<li class="ui-state-default">
															<input type="hidden" name="esgArary[]" value="{{ $esg->id }} || {{ $esg->alias_name }} || {{ $esg->category_id }}">
															<input type="checkbox" id="esgsFor2_{{ $esg->id }}" name="esgid" value="{{ $esg->id }}">
                                                            <label for="esgsFor2_{{ $esg->id }}">{{ $esg->alias_name }}</label>
															<div  class="pull-right" > 
																<span class="ui-icon ui-icon-arrowthick-2-n-s"></span>
															</div>
															
														</li>
													@endif	
												@endif
											@endforeach
										@php } @endphp
									</ul>
								@endforeach
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-8">	
								<label class="control-label" for="expiry_date">Expiry Date*</label>
                                <div class="datepickerIcon">
								    <input type="text" class="form-control" name="expiry_date" id="expiry_date" value="@if(old('expiry_date')){{ old('expiry_date') }}@else{{ date('m/d/Y', strtotime($survey->expiry_date)) }}@endif">
                                </div>
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-8">	
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
	                	console.log($(this).val());
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