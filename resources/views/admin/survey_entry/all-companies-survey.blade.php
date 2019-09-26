@extends('layouts.admin')

@section('content')
<div class="page-title"><h3>Survey Report - Summary (All Companies)</h3></div>
<div id="main-wrapper">
	<div class="panel panel-dark">
		<div class="panel-body">
			<div class="form-group display_fieldSp">
				<form method="post" id="searchFrm">
					{{ csrf_field() }}
					<div class="display-inline form-inline m-r-sm">
	                    <span>Industry</span> 
	                    <select  class="form-control" id="industry" name="industry" aria-controls="" class="">
	                    	<option value="">Please select</option>
		                	@foreach($industries as $industry)
		                	@php $selected = '';
					  		if(isset($input['industry']) && $input['industry'] == $industry->industry_type) { 
								$selected = 'selected="selected"';
							} @endphp	
							<option value="{{ $industry->industry_type }}" {{ $selected }}>{{ $industry->industry_type }}</option>
							@endforeach
						</select>
	                </div>						
                	<div class="display-inline form-inline m-r-sm">
	                    <span>Company</span> 
	                    <select  class="form-control" id="company" name="company" aria-controls="" class="">	                    	
							<option value="">Select Company</option> 
						</select>
	                </div>	                
	               	<div class="display-inline form-inline m-r-sm">
	                    <span>Year</span> 
	                    <select  class="form-control" id="year" name="fiscal_year" aria-controls="" class="">							
							<option value="">Select Year</option> 
						</select>
	                </div>	                
	                <div class="display-inline form-inline m-r-sm">
	                    <span>Month</span> 
	                    <select  class="form-control" id="month" name="fiscal_month" aria-controls="" class="">	                    	
							<option value="">Select Month</option>
						</select>
	                </div>
					<div class="display-inline form-inline m-r-sm" style="margin-right: 0px;">
	                    <span>Stakeholder Group</span> 
	                    <select name="survey_choice" id="survey_choice" class="form-control" style="max-width: 120px;">	                    	<option value="">Select Stakeholder Group</option> 
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
	jQuery( "#industry" ).change(function(){
		var str_company = '<option value="">Please Company</option>';
		$('#company').find('option').remove().end().append(str_company);
		var str_year = '<option value="">Please Year</option>';
		$('#year').find('option').remove().end().append(str_year);
		var str_month = '<option value="">Please Month</option>';
		$('#month').find('option').remove().end().append(str_month);
		var str_choice = '<option value="">Select Stakeholder Group</option>';
		$('#survey_choice').find('option').remove().end().append(str_choice);
		filterReport();
	});
	jQuery( "#company" ).change(function(){
		var str_year = '<option value="">Please Year</option>';
		$('#year').find('option').remove().end().append(str_year);
		var str_month = '<option value="">Please Month</option>';
		$('#month').find('option').remove().end().append(str_month);
		var str_choice = '<option value="">Select Stakeholder Group</option>';
		$('#survey_choice').find('option').remove().end().append(str_choice);
		filterReport();
	});
	jQuery( "#year" ).change(function(){
		var str_month = '<option value="">Please Month</option>';
		$('#month').find('option').remove().end().append(str_month);
		var str_choice = '<option value="">Select Stakeholder Group</option>';
		$('#survey_choice').find('option').remove().end().append(str_choice);
		filterReport();
	});
	jQuery( "#month" ).change(function(){
		var str_choice = '<option value="">Select Stakeholder Group</option>';
		$('#survey_choice').find('option').remove().end().append(str_choice);
		filterReport();
	});
	jQuery( "#searchFrmBtn" ).click(function(){
		filterReport('yes');
	});
});

function filterReport(srch = ''){

	$('#report-div').html('');
	var industry = $('#industry').val();
    var company =  $('#company').val();
    var fiscal_year = $('#year').val();
    var fiscal_month = $('#month').val();
    var survey_choice = $('#survey_choice').val();
    var search = srch;

	jQuery.ajax({
		headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
		type: "POST",
		url: "{{ route('all-companies-filter') }}", 
		data: {industry: industry, company: company, fiscal_year: fiscal_year, fiscal_month: fiscal_month, survey_choice: survey_choice, search: search},
		success: function(response){
			
			if(response.companies.length > 0){
				var str = '';
				if(response.companies.length > 1){
					str += '<option value="">All</option>';
				}
				var company_arr = [];
				$.each(response.companies, function(key, value){
					company_arr.push(value.id);
					str += '<option value="'+value.id+'">'+value.name+'</option>';
				});
				$('#company').find('option').remove().end().append(str);
				if(jQuery.inArray(company, company_arr) > -1){
					$('#company').val(company);
				}
			}

			if(response.years.length > 0){
				var str = '';
				if(response.years.length > 1){
					str += '<option value="">All</option>';
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
					str += '<option value="">All</option>';
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