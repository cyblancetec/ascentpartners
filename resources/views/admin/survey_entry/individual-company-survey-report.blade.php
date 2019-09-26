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
				 		@php $class = '';
				 		if(count($result1) > 5){
				 			echo '<div class="top5">TOP 5</div>';
				 			$class = 'Dark-table';
				 		} @endphp
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
				@if($survey_choice != 'Stakeholder only')
				<tr>
					<th colspan="2" style="text-align: center;">Company</th>
				</tr>
				<tr>
				 	<td colspan="2">
				 		@php $class = '';
				 		if(count($result2) > 5){
				 			echo '<div class="top5">TOP 5</div>';
				 			$class = 'Dark-table';
				 		} @endphp
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
								@php if($row['sample_size']=='0'){
									echo 'NA';
								}else{ @endphp
									{{ $row['completed_survey'] }}/{{ $row['sample_size'] }}
								@php } @endphp
							</td>
							<td>
								@php if($row['sample_size']=='0'){ 
									echo 'NA'; 
								}else{ @endphp
									<div class="progress">
										<div class="progress-bar" style="width:{{ $row['average'] }}%">{{ $row['average'] }}%</div>
									</div>
								@php } @endphp
							</td>
						</tr>
					@endforeach
				</tbody>
			</table>
		</div>
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
@if($survey_detail['id']!='')
<script type="text/javascript">
	$(function(){
		$('#export-div').show();
		$('#export-btn').attr('href', "{{ route('export.file',['id'=>$survey_detail['id'],'type'=>'csv']) }}")
	});
</script>
@else
<script type="text/javascript">
	$(function(){
		$('#export-div').hide();
		$('#export-btn').attr('href', "#")
	});
</script>
@endif
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
</script>