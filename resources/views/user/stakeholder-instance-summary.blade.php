@extends('layouts.app')

@section('content')
<div class="page-inner">	
    <div class="lg-title">
        <div class="container">
            <h3>{{ trans('messages.SurveyInstanceSummary') }}</h3>
        </div>
    </div>
    <section class="section UserPage">
		<div class="container"> 
			<div class="panel panel-default">
				<div class="panel-body">
					<div class="row">
						<div class="col-md-12">
							<div class="form-horizontal">
								<div class="form-group">
									<p class="col-lg-1 col-sm-1 control-label">{{ trans('messages.Survey') }}</p>
									<div class="col-md-4">
										<form method="post">
											{{ csrf_field() }}

											{{ old('survey') }}
											<select name="survey" class="form-control" onchange="this.form.submit()">
												<option value="">{{ trans('messages.Pleaseselect') }}</option>
												@foreach($surveys as $survey)
													@php $selected = ''; 
													if($survey_id == $survey->id){
														$selected = 'selected="selected"';
													} @endphp
													<option value="{{ $survey->id }}" {{ $selected }}>{{ $survey->title }}</option>
												@endforeach
											</select>
										</form>
									</div>
								</div>
								@php if(isset($survey_summary) && !empty($survey_summary)){ @endphp								
									<div class="form-group">
										<p class="col-md-12" style="margin: 0px;"><b>{{ trans('messages.SurveyTitle') }}</b>: {{ $survey_summary->title }} &nbsp; &nbsp; <b>{{ trans('messages.TotalCompletedSurvey') }}</b>: {{ $survey_summary->total_survey }}</p>
									</div>
									<div class="summery-table"> 
										<table class="">
											<thead>
												<th align="center" width="33%">{{ trans('messages.Stakeholders') }}</th>
												<th width="33%">{{ trans('messages.TotalCompletedSampleSize') }}</th>
												<th width="33%">%</th>
											</thead>	
											<tbody>
												@php
												$locale = LaravelLocalization::getCurrentLocale();
												$sql = 'SELECT ssh.stakeholder_id, ssh.sample_size, sht.title, sh.textbox_support_required
												FROM survey_stakeholders as ssh, stakeholder_translations as sht, stakeholders as sh
												WHERE ssh.stakeholder_id = sht.stakeholder_id
												AND sh.id=ssh.stakeholder_id
												AND ssh.survey_id='.$survey_summary->id.'
												AND sht.locale="'.$locale.'"';
												$survey_stakeholders = DB::select($sql);
												foreach($survey_stakeholders as $survey_stakeholder){
	
													$sql = 'SELECT count(se.id) as total 
														FROM survey_entries as se
														LEFT JOIN survey_entry_stakeholders as ses ON se.id = ses.survey_entry_id
														WHERE se.survey_id='.$survey_summary->id.'
														AND ses.stakeholder_id="'.$survey_stakeholder->stakeholder_id.'"';
														$survey_entries = DB::select($sql);
	
													echo '<tr>';
														echo '<td>';
															echo $survey_stakeholder->title;
															if($survey_stakeholder->textbox_support_required == 'yes'){
																$sql = 'SELECT count(se.id) as total 
																FROM survey_entries as se
																LEFT JOIN survey_entry_stakeholders as ses ON se.id = ses.survey_entry_id
																WHERE se.survey_id='.$survey_summary->id.'
																AND ses.stakeholder_id="'.$survey_stakeholder->stakeholder_id.'"
																AND ses.stakeholder_comment <> ""';
																$stakeholder_comment = DB::select($sql);
																															
																if($stakeholder_comment[0]->total != '0'){ @endphp
																	<!--[if IE 8]>
																	<a class="OpenModalBox" href="javascript:void(0);"  onclick="show_comment2({{ $survey_stakeholder->stakeholder_id }},{{ $survey_summary->id }});">{{ trans('messages.view') }} ({{ $stakeholder_comment[0]->total }})</a>
																	<![endif]-->
																	<a class="OpenModalBox" href="javascript:void(0)" data-toggle="modal" data-target="#ViewModal" onclick="show_comment({{ $survey_stakeholder->stakeholder_id }},{{ $survey_summary->id }});">{{ trans('messages.view') }} ({{ $stakeholder_comment[0]->total }})</a>
																@php }
															}
														echo '</td>';
														if($survey_stakeholder->sample_size=='0'){
															echo '<td>NA</td>';														
														}else{
															echo '<td>'.$survey_entries[0]->total.'/'.$survey_stakeholder->sample_size.'</td>';
														}													
														echo '<td>';														
															if($survey_stakeholder->sample_size=='0'){
																echo 'NA';
															}else{
																$average = $survey_entries[0]->total*100/$survey_stakeholder->sample_size;
																if($average >= 100 ) {
																	$average = '100.00';
																}else{
																	$average = number_format($survey_entries[0]->total*100/$survey_stakeholder->sample_size, 2);    
																}
																echo '<div class="progress">';
																	echo '<div class="progress-bar" style="width:'.$average.'%">'.$average.'%</div>';
																echo '</div>';
															}														
														echo '</td>';
													echo '</tr>';
												}
												@endphp
											</tbody>
										</table>
									</div>
								@php } @endphp
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
    </section>
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
    	url: "{{ url('/get-stakeholder-comments') }}/"+id+"/"+survey_id, 
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
</script>
<!--[if IE 8]>
<script>
function show_comment2(id,survey_id)
{
	$('#ViewModal').show();
	$('body').append('<div class="modal-backdrop fade in"></div>');
    jQuery.ajax({
    	url: "{{ url('/get-stakeholder-comments') }}/"+id+"/"+survey_id, 
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

$(function (e) {
	$('#ViewModal .modal-header .close, .modal-backdrop').click(function (e) {
		$('#ViewModal').hide();
	$('.modal-backdrop').remove();
	});
});
</script>
<![endif]-->
@endsection
