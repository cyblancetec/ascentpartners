@extends('layouts.app')

@section('content')
<div class=" page-inner page-login displaytable">	
	<div class="displaytable-cell">
		<div class="container">
			<div class="row">
				<div class="col-md-6 center">
					<div class="login-box">
						<div class="page-title"><h3>{{ trans('messages.ResetPassword') }}</h3></div>
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
											<button type="button" class="btn btn-primary" style="padding: 6px 15px;" onclick="window.location='{{ route("login") }}'">
												{{ trans('messages.Close') }}
											</button>
											</center>
										</div>
									</div>
								</form>
							@else
								<form class="form-horizontal" method="POST" action="{{ route('password.email') }}">
									{{ csrf_field() }}
	
									<div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
	
	
										<div class="col-md-12">
											<input id="email" type="email" placeholder="{{ trans('messages.EMailAddress') }}" class="form-control" name="email" value="{{ old('email') }}" required>
	
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
												{{ trans('messages.SendPasswordResetLink') }}
											</button>
											<!--<a class="btn btn-link" href="{{ route('login') }}" style="padding-right: 0px;">
												{{ trans('messages.SignIn') }}?
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
@endsection
