@extends('layouts.app')
@section('content')
<div class="page-inner">	
    <div class="lg-title">
        <div class="container">
            <h3 class="capitalize">{{ trans('messages.Welcome') }} {{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</h3>
        </div>
    </div>
	 <section class="section UserPage">
		<div class="container">
			<div class="row">
				<div class="col-sm-6 col-md-6 col-lg-6">
					<img src="{{ url('images/authenicated_user.jpg') }} " class="img-responsive" />
					<br>
				</div>
				<div class="col-sm-6 col-md-6 col-lg-6">
					{!! trans('messages.AuthenicatedUser') !!}
				</div>
			</div>
		</div>
	</section>
</div>
@endsection
