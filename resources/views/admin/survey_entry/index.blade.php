@extends('layouts.admin')

@section('content')
    <div class="page-title">
        <h3>Survey Entries</h3>
	</div>
<div id="main-wrapper">
	<div class="panel panel-dark">
		<div class="panel-body">
            <div class="row">
                <div class="col-lg-12 col-md-12">								
                    <div class="table-responsive"> 
                        <table id="survey-entries-table" class="display table table-bordered table-striped dataTable" style="width: 100%; cellspacing: 0;">
                            <thead>
                                <tr>
                                    <th>Company</th>
                                    <th>Survey</th>
                                    <th>Email</th>
                                    <th>Stakeholder</th>
                                    <th>ESG</th>
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
        jQuery('#survey-entries-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('get-survey-entries') }}",
            language: {
			    oPaginate: {
				  sNext: '<i class="fa fa-angle-right"></i>',
				  sPrevious: '<i class="fa fa-angle-left"></i>',						 
				}
			 },	
            columns: [
                {data: 'company_id', name: 'company_id'},
                {data: 'survey_id', name: 'survey_id'},
                {data: 'email', name: 'email'},
                {data: 'stakeholder', name: 'stakeholder'},
                {data: 'esg', name: 'esg'},
            ]
        });
    });
    </script>
@endsection