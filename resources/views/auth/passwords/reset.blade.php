@extends('layouts.app')

@section('content')
<div class="page-inner page-login displaytable">	
		<div class="displaytable-cell">
			<div class="container">
				<div class="row">
					<div class="col-md-6 center">
					<div class="login-box">
						<div class="page-title"><h3>{{ trans('messages.ResetPassword') }}</h3></div>
						<div class="login-box-inner">
							<form class="form-horizontal" method="POST" action="{{ route('password.request') }}">
								{{ csrf_field() }}
								<input type="hidden" name="token" value="{{ $token }}">
								<div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
									<div class="col-md-12">
										<input id="email" type="email" placeholder="{{ trans('messages.EMailAddress') }}" class="form-control" name="email" value="{{ $email or old('email') }}" readonly>

										@if ($errors->has('email'))
											<span class="help-block">
												<strong>{{ $errors->first('email') }}</strong>
											</span>
										@endif
									</div>
								</div>
								<div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
									<div class="col-md-12">
										<input id="password" type="password" placeholder="{{ trans('messages.NewPassword') }}" class="form-control" name="password" required>

										@if ($errors->has('password'))
											<span class="help-block">
												<strong>{{ $errors->first('password') }}</strong>
											</span>
										@endif
									</div>
								</div>

								<div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
									<div class="col-md-12">
										<input id="password-confirm" type="password" placeholder="{{ trans('messages.ConfirmPassword') }}" class="form-control" name="password_confirmation" required>

										@if ($errors->has('password_confirmation'))
											<span class="help-block">
												<strong>{{ $errors->first('password_confirmation') }}</strong>
											</span>
										@endif
									</div>
								</div>

								<div class="form-group">
									<div class="col-md-12">
										<button type="submit" class="btn btn-primary">
											{{ trans('messages.ResetPassword') }}
										</button>
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
