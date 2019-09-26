@extends('layouts.admin')

@section('content')

	<div class="page-title">
		<h3>Admin</h3>
		<div class="pull-right">
			<a href="{{ route('admins.create') }}" class="btn btn-success">Add New Admin</a>
		</div>
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
							 <table id="admin-table" class="display table table-bordered v-align-m" style="width: 100%; cellspacing: 0;">
								<thead>
									<tr>
										<th>First Name</th>
										<th>Last Name</th>
										<th>Email</th>
										<th>Phone</th>
										<th>Title</th>
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
			jQuery('#admin-table').DataTable({
				processing: true,
				serverSide: true,
				ajax: "{{ route('get-admins') }}",
					language: {
					  oPaginate: {
						  sNext: '<i class="fa fa-angle-right"></i>',
						  sPrevious: '<i class="fa fa-angle-left"></i>',						 
					  }
				 } ,	
				columns: [
					{data: 'first_name', name: 'first_name'},
					{data: 'last_name', name: 'last_name'},
					{data: 'email', name: 'email'},
					{data: 'phone', name: 'phone'},
					{data: 'title', name: 'title'},
					{data: 'action', name: 'action', orderable: false, searchable: false }
				]
			});
		});
    </script>
@endsection