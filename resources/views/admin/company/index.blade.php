@extends('layouts.admin')

@section('content')
<div class="page-title"><h3>Clients</h3>
	<div class="pull-right">
		<a href="{{ route('companies.create') }}" class="btn btn-success">Add New Client</a>       
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
						<table id="company-table" class="display table table-bordered table-striped dataTable" style="width: 100%; cellspacing: 0;">
							<thead>
								<tr>
									<th>Company Name</th>
									<th>Stock Code</th>
									<th>Sector</th>
									<th>Fiscal Month</th>
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
        jQuery('#company-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('get-companies') }}",
			language: {
			    oPaginate: {
				  sNext: '<i class="fa fa-angle-right"></i>',
				  sPrevious: '<i class="fa fa-angle-left"></i>',						 
				}
			 },	
            columns: [
                {data: 'name', name: 'name'},
                {data: 'stock_code', name: 'stock_code'},
                {data: 'industry_type', name: 'industry_type'},
                {data: 'fiscal_year', name: 'fiscal_year'},
                {data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });
    });
    </script>
@endsection