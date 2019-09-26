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
	<div class="col-lg-3"></div>
</div>
			