@extends('layouts.app')

@section('content')
<div class="page-inner">	
    <div class="lg-title">
        <div class="container">
            <h3>{!! trans('messages.WelcomeStakeholderPageHeader') !!}</h3>
        </div>
    </div>
    <section class="section UserPage">
		<div class="container" id="main-div"> 
			<div class="row">
				<div class="col-md-6 center">
					<div class="login-box">
						<div class="login-box-inner">
							<div id="error-div"></div>
							<form method="post">
								<div class="form-group">								
									<input type="text" id="email" placeholder="{{ trans('messages.EMailAddress') }}" class="form-control"/>
								</div>
								<input type="button" id="proceedBtn" value="{{ trans('messages.Proceed') }}" class="btn btn-primary">
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
    </section>
</div>
<script type="text/javascript">
	$(function(){

		var email = "{{$email}}";
		if(email!=''){

			welecome_continue(email);
		}

		$('#proceedBtn').click(function(){
			$('#error-div').html('');
			var email = $('#email').val();
			welecome_continue(email);
		});
	});

	function welecome_continue(email){

		$.ajax({
	            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
	            type: "POST",
	            url: "{{url('/en/stakeholder-welcome-continue')}}",
	            data: {email:email},
	            success:function(response){

	            	$('#main-div').html(response.html);
	            },
	            error: function(response) {
	                var data = response.responseJSON.errors;
	                errorsHtml = '<div class="alert alert-danger"><ul>';
	                $.each(data, function(key, value){
	                    errorsHtml += '<li>'+ value[0] + '</li>';
	                });
	                errorsHtml += '</ul></di>';
	                $('#error-div').html(errorsHtml);
	            },
	        });
	}
</script>
@endsection
