<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1" name="viewport"/>
    <meta charset="UTF-8">
    <link rel="icon" href="{{ asset('public/images/favicon.ico')}}" type="image/x-icon" />
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
	<link href="{{ asset('css/modern.css') }}" rel="stylesheet">
	<link href="{{ asset('css/admin.css') }}" rel="stylesheet">	
	
	
    
    <!-- Scripts -->
	<script src="{{ asset('js/app.js') }}"></script>	
	<script src="{{ asset('js/jquery-2.1.4.min.js') }}"></script>	
	<script src="{{ asset('js/jquery-ui.min.js') }}"></script>	
	<script src="{{ asset('js/waves.min.js') }}"></script>
    <script src="{{ asset('js/jquery.slimscroll.js') }}"></script>
	<script src="{{ asset('js/modern.js') }}"></script>
	
</head>
	
<body>
<div class="page-inner page-login displaytable">	
	<div class="displaytable-cell">
		<div class="container">
			<div class="row">
				<div class="col-md-6 center">
                    <div class="form-group text-center">
                        <img src="{{ asset('images/login_logo.jpg') }}" >
                    </div>
					<div class="login-box">
						<div class="page-title"><h3>RESET PASSWORD</h3></div>
						<div class="login-box-inner">
							@if (session('status'))
								<div class="alert alert-success">
									{{ session('status') }}
								</div>
							@endif
							
							@if (session('status'))
								<form class="form-horizontal">
									{{ csrf_field() }}									
									<div class="form-group">
										<div class="col-md-12">
											<center>
											<button type="button" class="btn btn-primary" style="padding: 6px 15px;" onclick="window.location='{{ route("admin.login") }}'">
												CLOSE
											</button>
											</center>
										</div>
									</div>
								</form>
							@else
								<form class="form-horizontal" method="POST" action="{{ route('admin.password.email') }}">
									{{ csrf_field() }}
									<div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
										<div class="col-md-12">
											<input id="email" placeholder="Email Address" type="email" class="form-control" name="email" value="{{ old('email') }}" required>
											@if ($errors->has('email'))
												<span class="help-block">
													<strong>{{ $errors->first('email') }}</strong>
												</span>
											@endif
										</div>
									</div>
	
									<div class="form-group">
										<div class="col-md-12">
											<button type="submit" class="btn btn-primary" style="padding: 6px 15px;">
												SEND PASSWORD RESET LINK
											</button>
											<!--<a class="btn btn-link" href="{{ route('admin.login') }}" style="padding-right: 0px;">
												Sign In
											</a>-->
										</div>
									</div>
								</form>
							@endif
						</div>
					</div>
				</div>
			</div>
    	</div>
	</div>
</div>
</body>
