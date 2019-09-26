@extends('layouts.admin')

@section('content')

	<div class="page-title"><h3>Stakeholders</h3>
		<div class="pull-right">
			<a href="{{ route('stakeholders.create') }}" class="btn btn-success">Add New Stakeholder</a>
		</div>
	</div>
        
<div id="main-wrapper">
	<div class="panel panel-dark">
		<div class="panel-body">
            @if (\Session::has('success'))           
                <div class="alert alert-success">
                    <p>{{ \Session::get('success') }}</p>
                </div>
            @endif
			 <div class="row">
				<div class="col-lg-12 col-md-12">								
					<div class="table-responsive">  
						<table id="stakeholder-table" class="display table table-bordered table-striped dataTable" style="width: 100%; cellspacing: 0;">
							<thead>
								<tr>
									<th>Stakeholders</th>
									<th>Text box support required</th>
									<th>Choice for survey</th>
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
        jQuery('#stakeholder-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('get-stakeholders') }}",
			language: {
			    oPaginate: {
				  sNext: '<i class="fa fa-angle-right"></i>',
				  sPrevious: '<i class="fa fa-angle-left"></i>',						 
				}
			 },	
            columns: [
                {data: 'alias_name', name: 'alias_name'},
                {data: 'textbox_support_required', name: 'textbox_support_required'},
                {data: 'survey_choice', name: 'survey_choice'},
                {data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });
    });
    </script>
@endsection