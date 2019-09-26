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
					<input type="hidden" id="campanyFltr" name="campanyFltr">
				</form>
				<form method="post">
					{{ csrf_field() }}
	                <div class="display-inline form-inline m-r-sm">
	                    <span>Company</span> 
	                    <select class="form-control" id="company" name="company" aria-controls="" class="">
		                	@foreach($companies as $company)
		                	@php $selected = '';
					  		if(isset($input['company']) && $input['company'] == $company->id) { 
								$selected = 'selected="selected"';
							} @endphp	
							<option value="{{ $company->id }}" {{ $selected }}>{{ $company->name }}</option>
							@endforeach
						</select>
	                </div>
	                <?php /* ?>
	                <div class="display-inline form-inline m-r-sm">
	                    <span>Year</span> 
	                    @php $currently_selected = date('Y'); 
						$earliest_year = date("Y",strtotime("-20 year"));; 
						$latest_year = date('Y'); @endphp
						<select class="form-control" name="year" aria-controls="" class="">
							<option value="">YYYY</option>
						  	@foreach ( range( $latest_year, $earliest_year ) as $i ) 
						  		@php $selected = '';
						  		if(isset($input['year']) && $input['year'] == $i) { 
									$selected = 'selected="selected"';
								} @endphp
						    	<option value="{{ $i }}" {{ $selected }}>{{ $i }}</option>
						  	@endforeach
						</select>
	                </div>
	                <?php */ ?>
	                <div class="display-inline form-inline m-r-sm">
	                    <span>Year</span> 
	                    <select  class="form-control" name="year" aria-controls="" class="">
							<option value="">All</option>
						  	@foreach ( $years as $year ) 
						  		@php $selected = '';
						  		if(isset($input['year']) && $input['year'] == $year) { 
									$selected = 'selected="selected"';
								} @endphp
						    	<option value="{{ $year }}" {{ $selected }}>{{ $year }}</option>
						  	@endforeach
						</select>
	                </div>
	                <?php /* ?>
	                <div class="display-inline form-inline m-r-sm">
	                    <span>Month</span> 
	                    <select class="form-control" name="month" aria-controls="" class="">
	                    	<option value="">MM</option>
							@for ($i = 1; $i < 13; $i++)
								@php $selected = '';
								$month = date("F", mktime(0, 0, 0, $i, 10));
								if(isset($input['month']) && $input['month'] == $month) { 
									$selected = 'selected="selected"';
								} @endphp
								<option value="{{ $month }}" {{ $selected }}>{{ $month }}</option>
							@endfor
						</select>
	                </div>
	                <?php */ ?>
	                <div class="display-inline form-inline m-r-sm">
	                    <span>Month</span> 
	                    <select  class="form-control" name="month" aria-controls="" class="">
	                    	<option value="">All</option>
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
	                    <select name="survey_choice" class="form-control">
	                    	<option value="">All</option>
							<option value="Stakeholder only" @if(isset($input['survey_choice']) && $input['survey_choice'] == 'Stakeholder only') {{ 'selected="selected"' }} @endif>Stakeholder only</option>
							<option value="Stakeholder and Company" @if(isset($input['survey_choice']) && $input['survey_choice'] == 'Stakeholder and Company') {{ 'selected="selected"' }} @endif>Stakeholder and Company</option>
						</select>
                        <button class="btn btn-primary btn-sm" name="search" style="margin-left: 10px;">Report</button>
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
												<a href="javascript:void(0)" data-toggle="modal" data-target="#ViewModal" onclick="show_comment({{ $row['id'] }});">view ({{ $row['total_comment'] }})</a>
												@endif
											@endif
										</td>
										<td align="center">{{ $row['completed_survey'] }}/{{ $row['sample_size'] }}</td>
										<td>
											<div class="progress">
												<div class="progress-bar" style="width:{{ $row['average'] }}%">{{ $row['average'] }}%</div>
											</div>
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
function show_comment(id)
{
    jQuery.ajax({
    	url: "{{ url('/admin/get-stakeholder-comments') }}/"+id, 
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
});
</script>
@endsection