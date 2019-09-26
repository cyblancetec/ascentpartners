jQuery(function($){
	jQuery(".navbar-toggle").click(function() {
	if($("#Mobile-nav").is(":hidden")){
			//$("#Mobile_nav").hide();
			$("#Mobile-nav").show();
			$('#Mobile-nav').animate({right: '0px'});
			$('#main_wrapper, #header').animate({
			'right' : "260px" //moves left
			});
			//$("#Mobile_nav2").show();

		} else{
			$('#main_wrapper, #header').animate({right: '0px'});
			$('#Mobile-nav').animate({right: '-260px'}, function() {
				$(this).hide();
			});
		}
	});
});

jQuery(window).resize(function() {
	$('#main_wrapper, #header').animate({right: '0px'});
	$('#Mobile-nav').animate({right: '-260px'});
	$('#Mobile-nav').hide();
});