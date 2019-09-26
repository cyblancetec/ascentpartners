@extends('layouts.admin')

@section('content')
<div class="page-title">
	<h3>Survey Report - Individual Company</h3>
	<div class="pull-right display-inline" id="export-div" style="display: none;">
		<a id="export-btn" href="#" class="btn btn-primary btn-sm"><i class="fa fa-download"></i> Export logs</a>
    </div>
</div>
<div id="main-wrapper">
	<div class="panel panel-dark">
		<div class="panel-body">
			<div class="form-group display_fieldSp">
				<form method="post" id="searchFrm">
					{{ csrf_field() }}
	                <div class="display-inline form-inline m-r-sm">
	                    <span>Company</span> 
	                    <select class="form-control" id="company" name="company" aria-controls="" class="">
	                    	<option value="">Please select</option>
		                	@foreach($companies as $company)
		                	<option value="{{ $company->id }}">{{ $company->name }}</option>
							@endforeach
						</select>
	                </div>
	                <div class="display-inline form-inline m-r-sm">
	                    <span>Year</span>
						<select  class="form-control" id="year" name="year" aria-controls="" class="">
	                    	<option value="">Please select</option> 
						</select>						
	                </div>
	                <div class="display-inline form-inline m-r-sm">
	                    <span>Month</span> 	                    
	                    <select  class="form-control" id="month" name="month" aria-controls="" class="">
	                    	<option value="">Please select</option> 
						</select>
	                </div>
					<div class="display-inline form-inline m-r-sm" style="margin-right: 0px;">
	                    <span>Stakeholder Group</span> 
	                    <select name="survey_choice" id="survey_choice" class="form-control">	                    	
							<option value="">Please select</option> 
						</select>
						<button type="button" id="searchFrmBtn" name="search" class="btn btn-primary btn-sm" style="margin-left: 10px;" disabled>Report</button>
						
	                </div>
                </form>
            </div>
            <div id="report-div"></div>
		</div>
	</div>
</div>
<script type="text/javascript">
jQuery( document ).ready(function() {

	var please_select = '<option value="">Please select</option>';
	jQuery( "#company" ).change(function(){
		$('#year').find('option').remove().end().append(please_select);
		$('#month').find('option').remove().end().append(please_select);
		$('#survey_choice').find('option').remove().end().append(please_select);
		filterReport();
	});
	jQuery( "#year" ).change(function(){
		$('#month').find('option').remove().end().append(please_select);
		$('#survey_choice').find('option').remove().end().append(please_select);
		filterReport();
	});
	jQuery( "#month" ).change(function(){
		$('#survey_choice').find('option').remove().end().append(please_select);
		filterReport();
	});
	jQuery( "#searchFrmBtn" ).click(function(){

		if(jQuery( "#year" ).val() != '' && jQuery( "#month" ).val() != ''){
			filterReport('yes');
		}else if(jQuery( "#year" ).val() == ''){
			alert('Please select year.');
		}else if(jQuery( "#month" ).val() == ''){
			alert('Please select month.');
		}
	});
});

function filterReport(srch = ''){

	$('#report-div').html('');
	var company =  $('#company').val();
    var fiscal_year = $('#year').val();
    var fiscal_month = $('#month').val();
    var survey_choice = $('#survey_choice').val();
    var search = srch;

	jQuery.ajax({
		headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
		type: "POST",
		url: "{{ route('individual-company-filter') }}", 
		data: {company: company, fiscal_year: fiscal_year, fiscal_month: fiscal_month, survey_choice: survey_choice, search: search},
		success: function(response){
			
			if(response.years.length > 0){
				var str = '';
				if(response.years.length > 1){
					str += '<option value="">Please select</option>';
				}
				$.each(response.years, function(key, value){
					str += '<option value="'+value+'">'+value+'</option>';
				});
				$('#year').find('option').remove().end().append(str);
				if(fiscal_year!=''){
					$('#year').val(fiscal_year);
				}
			}

			if(response.months.length > 0){
				var str = '';
				if(response.months.length > 1){
					str += '<option value="">Please select</option>';
				}
				$.each(response.months, function(key, value){
					str += '<option value="'+value+'">'+value+'</option>';
				});
				$('#month').find('option').remove().end().append(str);
				if(fiscal_month!=''){
					$('#month').val(fiscal_month);
				}
			}

			if(response.stakeholder_groups.length > 0){
				var str = '';
				if(response.stakeholder_groups.length > 1){
					str += '<option value="">All</option>';
				}
				$.each(response.stakeholder_groups, function(key, value){
					str += '<option value="'+value+'">'+value+'</option>';
				});
				$('#survey_choice').find('option').remove().end().append(str);
				if(survey_choice!=''){
					$('#survey_choice').val(survey_choice);
				}			
			}

			if (response.isSurvey == false) {				
				$('#searchFrmBtn').attr("disabled",true);
			}else{
				$('#searchFrmBtn').attr("disabled",false);
			}

			if(response.result!=''){
				$('#report-div').html(response.result);
			}			
		}
	});
}
</script>
@endsection