@extends('layouts.app')

@section('content')
<div class="page-inner page-login displaytable">	
	<div class="displaytable-cell">
		<div class="container">
			<div class="row">
				<div class="col-md-6 center">
					<div class="login-box">
						<div class="page-title"><h3>{{ trans('messages.SignIn') }}</h3></div>
						<div class="login-box-inner">
							<form class="form-horizontal" method="POST" action="{{ route('login') }}">
								{{ csrf_field() }}
								<div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
									<div class="col-md-12">
										<input id="email" type="email" placeholder="{{ trans('messages.EMailAddress') }}" class="form-control" name="email" value="{{ old('email') }}" required autofocus>
										@if ($errors->has('email'))
											<span class="help-block">
												<strong>{{ $errors->first('email') }}</strong>
											</span>
										@endif
									</div>
								</div>
								<div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
									<div class="col-md-12">
										<input id="password" type="password" placeholder="{{ trans('messages.Password') }}" class="form-control" name="password" required>
										@if ($errors->has('password'))
											<span class="help-block">
												<strong>{{ $errors->first('password') }}</strong>
											</span>
										@endif
									</div>
								</div>
								<div class="form-group">
									<div class="col-md-12">
										<div class="checkbox">
											<label>
												<input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> {{ trans('messages.RememberMe') }}
											</label>
										</div>
									</div>
								</div>
								<div class="form-group">
									<div class="col-md-12">
										<button type="submit" class="btn btn-primary">
											{{ trans('messages.SignIn') }}
										</button>
										<a class="btn btn-link" href="{{ route('password.request') }}" >
											{{ trans('messages.ResetPassword') }}
										</a>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
