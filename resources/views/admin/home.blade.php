@extends('layouts.admin')

@section('content')
<div class="page-title"><h3>Admin Dashboard</h3> <p class="pull-right"> You are logged in, Admin!</p></div> 
 <div id="main-wrapper">
	 <div class="panel panel-dark dashbord-panel">
		 <div class="panel-body p0">
			<div class="row">
				<div class="col-lg-12 col-md-12">
				@if (session('status'))
					<div class="alert alert-success">
						{{ session('status') }}
					</div>
				@endif
				<ul class="cd-nav list-unstyled">
					<li >
						<a class="" href="{{ route('admins.index') }}">
							<span>
								<i class="fa fa-user" aria-hidden="true"></i>
							</span>
							<p>Admins</p>
						</a>
					</li>
					<!--<li>
						<a class=""  href="{{ route('companies.index') }}">
							<span><i class="fa fa fa-building-o" aria-hidden="true"></i></span>
							<p>Companies</p>
						</a>
					</li>
					<li>
						<a class="" href="{{ route('users.index') }}">	
							<span><i class="fa fa-user-circle-o" aria-hidden="true"></i></span> <p>Clients</p>
						</a>
					</li>-->
					<li>
						<a class=""  href="{{ route('companies.index') }}">
							<span><i class="fa fa-user-circle-o" aria-hidden="true"></i></span> <p>Clients</p>
						</a>
					</li>
					<li>
						<a class="" href="{{ route('users.index') }}">	
							<span><i class="fa fa-user-circle-o" aria-hidden="true"></i></span> <p>Users</p>
						</a>
					</li>
					<li>
						<a class="" href="{{ route('stakeholders.index') }}">
							<span><i class="fa fa-users" aria-hidden="true"></i></span> <p>Stakeholders</p>
						</a>					
					</li>
					<li>
						<a class="" href="{{ route('esg.index') }}">
							<span><i class="fa fa-dollar" aria-hidden="true"></i></span> <p>ESG Aspects</p>
						</a>
					</li>
					<li>
						<a class="" href="{{ url('/admin/surveys') }}">
							<span><i class="fa fa-file-text-o" aria-hidden="true"></i></span> <p>Surveys</p>
						</a>
					</li>
					<li>
						<a class="" href="{{ url('/admin/all-companies-survey-report') }}">
							<span class="arrow"><i class="fa fa-edit" aria-hidden="true"></i></span> <p>All Companies<br>Survey Report</p>
						</a>
					</li>
					<li>
						<a class="" href="{{ url('/admin/individual-company-survey-report') }}">
							<span class="arrow"><i class="fa fa-edit" aria-hidden="true"></i></span> <p>Individual Company<br>Survey Report</p>
						</a>
					</li>
					<li>
						<a class="" href="{{ url('/admin/email-templates') }}">
							<span><i class="fa fa-envelope" aria-hidden="true"></i></span> <p>Email Templates</p>
						</a>
					</li>
				</ul>
			   </div>
			</div>
		 </div>
	 </div>	
</div>

              


@endsection
