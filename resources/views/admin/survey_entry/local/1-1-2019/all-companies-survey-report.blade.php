@extends('layouts.admin')

@section('content')
<div class="page-title"><h3>Survey Report - Summary (All Companies)</h3></div>
<div id="main-wrapper">
	<div class="panel panel-dark">
		<div class="panel-body">
			<div class="form-group display_fieldSp">
				<form method="post" id="campanyFltrFrm">
					{{ csrf_field() }}
					<input type="hidden" id="campanyFltr" name="campanyFltr" value="{{ $input['company'] or old('campanyFltr') }}">
					<input type="hidden" id="industryFltr" name="industryFltr" value="{{ $input['industry'] or old('industryFltr') }}">
				</form>
				<form method="post">
					{{ csrf_field() }}
					<div class="display-inline form-inline m-r-sm">
	                    <span>Industry</span> 
	                    <select  class="form-control" id="industry" name="industry" aria-controls="" class="">
	                    	<option value="">Please select</option>
		                	@foreach($industries as $company)
		                	@php $selected = '';
					  		if(isset($input['industry']) && $input['industry'] == $company->industry_type) { 
								$selected = 'selected="selected"';
							} @endphp	
							<option value="{{ $company->industry_type }}" {{ $selected }}>{{ $company->industry_type }}</option>
							@endforeach
						</select>
	                </div>						
                	<div class="display-inline form-inline m-r-sm">
	                    <span>Company</span> 
	                    <select  class="form-control" id="company" name="company" aria-controls="" class="">	                    	
							@if(empty($companies) || $companies->isEmpty())
	                    		<option value="">Please select</option> 
	                    	@elseif(count($companies) > 1) 
	                    		<option value="">All</option> 
	                    	@endif
		                	@foreach($companies as $company)
		                	@php $selected = '';
					  		if(isset($input['company']) && $input['company'] == $company->id) { 
								$selected = 'selected="selected"';
							} @endphp	
							<option value="{{ $company->id }}" {{ $selected }}>{{ $company->name }}</option>
							@endforeach
						</select>
	                </div>	                
	               	<div class="display-inline form-inline m-r-sm">
	                    <span>Year</span> 
	                    <select  class="form-control" id="year" name="fiscal_year" aria-controls="" class="">							
							@if(empty($years))
	                    		<option value="">Select Year</option> 
	                    	@elseif(count($years) > 1) 
	                    		<option value="">All</option> 
	                    	@endif
						  	@foreach ( $years as $year ) 
						  		@php $selected = '';
						  		if(isset($input['fiscal_year']) && $input['fiscal_year'] == $year) { 
									$selected = 'selected="selected"';
								} @endphp
						    	<option value="{{ $year }}" {{ $selected }}>{{ $year }}</option>
						  	@endforeach
						</select>
	                </div>	                
	                <div class="display-inline form-inline m-r-sm">
	                    <span>Month</span> 
	                    <select  class="form-control" id="month" name="fiscal_month" aria-controls="" class="">	                    	
							@if(empty($months))
	                    		<option value="">Select Month</option> 
	                    	@elseif(count($months) > 1)
								<option value="">All</option>
							@endif	
							@foreach ( $months as $month ) 
								@php $selected = '';
								if(isset($input['fiscal_month']) && $input['fiscal_month'] == $month) { 
									$selected = 'selected="selected"';
								} @endphp
								<option value="{{ $month }}" {{ $selected }}>{{ $month }}</option>
							@endforeach
						</select>
	                </div>
					<div class="display-inline form-inline m-r-sm" style="margin-right: 0px;">
	                    <span>Stakeholder Group</span> 
	                    <select name="survey_choice" id="survey_choice" class="form-control" style="max-width: 120px;">	                    	
							@if(empty($survey_choices) || $survey_choices->isEmpty())
	                    		<option value="">Select Stakeholder Group</option> 
	                    	@elseif(count($survey_choices) > 1)
								<option value="">All</option>
							@endif	  
							@foreach ( $survey_choices as $choice )
								@php $selected = '';
								if(isset($input['survey_choice']) && $input['survey_choice'] == $choice->survey_choice) { 
									$selected = 'selected="selected"';
								} @endphp
								<option value="{{ $choice->survey_choice }}" {{ $selected }}>{{ $choice->survey_choice }}</option>
							@endforeach
						</select>                        
						@if($isSurvey)
							<button class="btn btn-primary btn-sm" id="searchFrm" name="search" style="margin-left: 10px;">Report</button>
						@else
							<button class="btn btn-primary btn-sm" id="searchFrm" name="search" style="margin-left: 10px;" disabled>Report</button>
						@endif
	                </div>
	            </form>
            </div>			
			@if(count($result1) > 0 || count($result2) > 0)
		 	<div class="row">  
		 		<div class="col-lg-3"></div>
				<div class="col-lg-6">
			 		<table class="Survey-Report-Table">
						<thead>
							<tr>
								<th width="70%">ESG</th>
								<th class="text-center">Average</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<th colspan="2" style="text-align: center;">Stakeholder</th>
							</tr>
							<tr>
							 	<td colspan="2">
							 		@php 
							 		$class = '';
							 		if(count($result1) > 5){
							 			echo '<div class="top5">TOP 5</div>';
							 			$class = 'Dark-table';
							 		}
							 		@endphp
						 			<table class="{{ $class }}">
						 				@php $i=1; @endphp
						 				@foreach($result1 as $row)
							 				<tr>
												<td width="70%">{{ $i }} {{ $row['alias_name'] }}</td>
												<td class="text-center">{{ $row['average'] }}</td>
											</tr>
											@php $i++; @endphp
						 				@endforeach
									</table>
								</td>
							</tr>
							@if(!isset($input['survey_choice']) || isset($input['survey_choice']) && $input['survey_choice'] != 'Stakeholder only')
							<tr>
								<th colspan="2" style="text-align: center;">Company</th>
							</tr>
							<tr>
							 	<td colspan="2">
							 		@php 
							 		$class = '';
							 		if(count($result2) > 5){
							 			echo '<div class="top5">TOP 5</div>';
							 			$class = 'Dark-table';
							 		}
							 		@endphp
						 			<table class="{{ $class }}">
						 				@php $i=1; @endphp
						 				@foreach($result2 as $row)
							 				<tr>
												<td width="70%">{{ $i }} {{ $row['alias_name'] }}</td>
												<td class="text-center">{{ $row['average'] }}</td>
											</tr>
											@php $i++; @endphp
						 				@endforeach
									</table>
								</td>
							</tr>
							@endif
						</tbody>
					</table>
			   	</div>
			   	<div class="col-lg-3"></div>
			</div>
			@endif
		</div>
	</div>
</div>
<script type="text/javascript">
jQuery( document ).ready(function() {
	jQuery( "#company" ).change(function(){
	    jQuery('#campanyFltr').val(jQuery(this).val());
	    jQuery('#campanyFltrFrm').submit();
	});
	jQuery( "#industry" ).change(function(){
	    jQuery('#industryFltr').val(jQuery(this).val());		
		if (jQuery('#industryFltr').val() == '') {
			jQuery('#campanyFltr').val('');
		}
	    jQuery('#campanyFltrFrm').submit();
	});
	jQuery( "#year" ).change(function(){
		var year = jQuery(this).val();
		filterDropDown('year', year);
	});

	jQuery( "#month" ).change(function(){	
		var year = jQuery('#year').val();
		var month = jQuery(this).val();
		if(year == ''){
			alert('Please select year first.');
			jQuery(this).val('');
		}else{
			filterDropDown('month', month);
		}
	});
});
function filterDropDown(field,value){

	var company = $('#company').val();
	var industry = $('#industry').val();	
	if(field == 'year') {
		data = { field: field, value: value, company: company, industry:industry }
	}else if(field == 'month') {
		var year = $('#year').val();		
		data = { field: field, value: value, company: company, year: year, industry:industry }
	}

	jQuery.ajax({
        url: "{{ route('filter-drop-down') }}", 
        data: data,
        success: function(response){			                        
            if(response.data.months){
	            $("#month").html('');
	            if(response.data.months.length > 1){
	            	$('#month').append($('<option>').text('All').attr('value', ''));
	        	}
	            $.each(response.data.months, function(i, value) {
		            $('#month').append($('<option>').text(value).attr('value', value));
		        });
	        }
			
			if(response.data.survey_choices){
	            $("#survey_choice").html('');
	            if(response.data.survey_choices.length > 1){
	            	$('#survey_choice').append($('<option>').text('All').attr('value', ''));
	            }
	            $.each(response.data.survey_choices, function(i, value) {					
		            $('#survey_choice').append($('<option>').text(value.survey_choice).attr('value', value.survey_choice));
		        });
	        }
			if (response.isSurvey == false) {				
				$('#searchFrm').attr("disabled",true);
			}else{
				$('#searchFrm').attr("disabled",false);
			}
        }
    });
}
</script>
@endsection