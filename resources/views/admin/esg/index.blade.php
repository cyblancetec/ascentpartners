@extends('layouts.admin')

@section('content')
<div class="page-title"><h3>ESG Aspects</h3>
	<div class="pull-right">
		<a href="{{ route('esg.create') }}" class="btn btn-success">Add New ESG Aspect</a>
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
						<table id="esg-table" class="display table table-bordered table-striped dataTable" style="width: 100%; cellspacing: 0;">
							<thead>
								<tr>
									<th>ESG Aspect</th>
									<th>Category</th>
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
        jQuery('#esg-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('get-esg') }}",
			language: {
			    oPaginate: {
				  sNext: '<i class="fa fa-angle-right"></i>',
				  sPrevious: '<i class="fa fa-angle-left"></i>',						 
				}
			 },	
            columns: [
                {data: 'alias_name', name: 'alias_name'},
                {data: 'category_id', name: 'category_id'},
                {data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });
    });
    </script>
@endsection