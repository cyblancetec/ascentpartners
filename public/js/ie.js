$(function (e) {
	$('.nav-tabs a').click(function (e) {
		e.preventDefault();
		//console.log('in');
		var hrefTab = $(this).attr('href');
		//console.log(hrefTab);
		$('.nav-tabs li').removeClass('active');
		$(this).parent('li').addClass('active');
		$('.tab-content .tab-pane').hide().removeClass('active');;
		$(hrefTab).show().addClass('active');
		//$(this).tab('show');
	});
});