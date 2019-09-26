@extends('layouts.admin')

@section('content')
<div class="page-title">
	<h3>Survey Report - Individual Company</h3>
	<div class="pull-right display-inline" >
		@if(!empty($survey_detail))
	    	<a href="{{ route('export.file',['id'=>$survey_detail['id'],'type'=>'csv']) }}" class="btn btn-primary btn-sm"><i class="fa fa-download"></i> Export logs</a>
    	@endif
    </div>
</div>
<div id="main-wrapper">
	<div class="panel panel-dark">
		<div class="panel-body">
			<div class="form-group display_fieldSp">
				@if ($errors->count() > 0)
					<div class="alert alert-danger">
						<ul>
							@foreach($errors->all() as $error)
								<li>{{ $error }}</li>
							@endforeach
						</ul>
					</div>
				@endif 
				<form method="post" id="campanyFltrFrm">
					{{ csrf_field() }}
					<input type="hidden" id="campanyFltr" name="campanyFltr" value="{{ $input['company'] or old('campanyFltr') }}">
					<!--<input type="hidden" id="yearFltr" name="yearFltr" value="{{ $input['year'] or old('yearFltr') }}">
					<input type="hidden" id="monthFltr" name="monthFltr" value="{{ $input['month'] or old('monthFltr') }}">-->
				</form>
				<form method="post" id="IndividualCompanyFrm">
					{{ csrf_field() }}
	                <div class="display-inline form-inline m-r-sm">
	                    <span>Company</span> 
	                    <select class="form-control" id="company" name="company" aria-controls="" class="">
	                    	<option value="">Please select</option>
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
						<select  class="form-control" id="year" name="year" aria-controls="" class="">
	                    	@if(empty($years))
	                    		<option value="">Select Year</option> 
	                    	@elseif(count($years) > 1) 
	                    		<option value="">All</option> 
	                    	@endif
						  	@foreach ( $years as $year ) 
						  		@php $selected = '';
						  		if(isset($input['year']) && $input['year'] == $year) { 
									$selected = 'selected="selected"';
								} @endphp
						    	<option value="{{ $year }}" {{ $selected }}>{{ $year }}</option>
						  	@endforeach
						</select>						
	                </div>
	                <div class="display-inline form-inline m-r-sm">
	                    <span>Month</span> 	                    
	                    <select  class="form-control" id="month" name="month" aria-controls="" class="">
	                    	@if(empty($months))
	                    		<option value="">Select Month</option> 
	                    	@elseif(count($months) > 1)
								<option value="">All</option>
							@endif	                    	
							@foreach ( $months as $month ) 
								@php $selected = '';
								if(isset($input['month']) && $input['month'] == $month) { 
									$selected = 'selected="selected"';
								} @endphp
								<option value="{{ $month }}" {{ $selected }}>{{ $month }}</option>
							@endforeach
						</select>
	                </div>
					<div class="display-inline form-inline m-r-sm" style="margin-right: 0px;">
	                    <span>Stakeholder Group</span> 
	                    <select name="survey_choice" id="survey_choice" class="form-control">	                    	
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
            @if(!empty($survey_detail))
            <div class="form-group">
				<p class="display-inline m-r-sm"><b>Survey Title:</b> {{ $survey_detail['title'] or '' }}</p> 
				<p class="display-inline m-r-sm"><b>Total Completed Survey:</b> {{ $survey_detail['total_completed_survey'] }}</p>
			</div>
			<div class="row">  
				<div class="col-lg-5">
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
				<div class="col-lg-7">
					<div class="summery-table">
						<table class="">
							<thead>
								<tr>
									<th align="center" width="33%">Stakeholders</th>
									<th width="33%">Total Completed / Suggested Sample Size</th>
									<th width="33%">%</th>
								</tr>
							</thead>
							<tbody>
								@foreach($result3 as $row)
									<tr>
										<td>
											{{ $row['alias_name'] }}
											@if($row['textbox_support_required'] == 'yes')
												@if($row['total_comment']!=0)
												<a href="javascript:void(0)" data-toggle="modal" data-target="#ViewModal" onclick="show_comment({{ $row['id'] }},{{ $row['survey_id'] }});">view ({{ $row['total_comment'] }})</a>
												@endif
											@endif
										</td>
										<td align="center">
											@php 
												if($row['sample_size']=='0'){
													echo 'NA';
												}else{ @endphp
													{{ $row['completed_survey'] }}/{{ $row['sample_size'] }}
												@php }
											@endphp
											
										</td>
										<td>
											@php 
												if($row['sample_size']=='0'){ 
													echo 'NA'; 
												}else{
													@endphp
													<div class="progress">
														<div class="progress-bar" style="width:{{ $row['average'] }}%">{{ $row['average'] }}%</div>
													</div>
													@php 
												} 
											@endphp
										</td>
									</tr>
								@endforeach
							</tbody>
						</table>
					</div>
				</div>
			</div>
			@endif
		</div>
	</div>
    <div id="ViewModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog ">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body" id="popupContent"></div>
            </div>
        </div>
    </div>	
</div>
<script type="text/javascript">
function show_comment(id,survey_id)
{
    jQuery.ajax({
    	url: "{{ url('/admin/get-stakeholder-comments') }}/"+id+"/"+survey_id, 
    	success: function(result){
    		jQuery('#popupContent').html(result);
    		jQuery('#view-table').DataTable({
		        searching: false,
		        info: false,
		        lengthChange: false,
		        ordering: false,
		        language: {
		            oPaginate: {
		              sNext: '<i class="fa fa-angle-right"></i>',
		              sPrevious: '<i class="fa fa-angle-left"></i>',						 
		            }
		         },	
		    });
    	}
    });
}

jQuery( document ).ready(function() {
	jQuery( "#company" ).change(function(){
	    jQuery('#campanyFltr').val(jQuery(this).val());
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
	if(field == 'year') {
		data = { field: field, value: value, company: company }
	}else if(field == 'month') {
		var year = $('#year').val();		
		data = { field: field, value: value, company: company, year: year }
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