@extends('layouts.admin')

@section('content')
<div class="page-title">
	<h3>Email Template</h3>
	<!--<div class="pull-right">
		<a href="{{ route('email-templates.create') }}" class="btn btn-success">Add New Email Template</a>
	</div>-->
</div>
<div id="main-wrapper">
	<div class="panel panel-dark">
		<div class="panel-body">
			 <div class="row">
			 	@if (\Session::has('success'))
					<div class="alert alert-success">
						<p>{{ \Session::get('success') }}</p>
					</div>
				@endif
				<div class="col-lg-12 col-md-12">								
					<div class="table-responsive">     
						<table id="email-template-table" class="display table table-bordered v-align-m" style="width: 100%; cellspacing:0;">
							<thead>
								<tr>
									<th>Email Template</th>
									<th>Actions</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
				 </div>
			</div>
		</div>
	</div>
</div>
 <script type="text/javascript">
	jQuery(document).ready(function() {
		jQuery('#email-template-table').DataTable({
			processing: true,
			serverSide: true,
			ajax: "{{ route('get-email-templates') }}",
				  language: {
				  oPaginate: {
					  sNext: '<i class="fa fa-angle-right"></i>',
					  sPrevious: '<i class="fa fa-angle-left"></i>',						 
				  }
			 },
			columns: [
				{data: 'alias_name', name: 'alias_name'},
				{data: 'action', name: 'action', orderable: false, searchable: false }
			]
		});
	});
</script>
@endsection