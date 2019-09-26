@extends('layouts.app')

@section('content')

<script src="{{ asset('js/jquery.superslides.js') }}"></script>
<link href="{{ asset('css/superslides.css') }}" rel="stylesheet">

<!--<div id="slides">
    <div class="slides-container">
        <img src="{{ url('images/slide_images/641e36c877d18f8b3d84c9358577a0ceef2f019a.jpg') }} " class="img-responsive" />
        <img src="{{ url('images/slide_images/3aa4e88de685a13b92173c863b9fc13d7f106e17.jpg') }} " class="img-responsive" />
        <img src="{{ url('images/slide_images/c3f3e44e519a965febd8b7b9877fd004cafe75b6.jpg') }} " class="img-responsive" />
        <img src="{{ url('images/slide_images/dfe8aa7828b5163dbff3ead2c4fd10e68c86e141.jpg') }} " class="img-responsive" />
    </div>
	<nav class="slides-navigation">
      <a href="#" class="next">
         <img src="{{ url('images/next.png') }} " />
      </a>
      <a href="#" class="prev">
        <img src="{{ url('images/prev.png') }} " />
      </a>
    </nav>
</div>-->
<div id="slides" class="flexslider blete-testimonial">
	<ul class="slides">                            
		<li style="background-image: url('{{ asset('images/slide_images/641e36c877d18f8b3d84c9358577a0ceef2f019a.jpg') }}')" >
			
		</li>
		<li style="background-image: url('{{ asset('images/slide_images/3aa4e88de685a13b92173c863b9fc13d7f106e17.jpg') }}')" >
			
		</li>
		<li style="background-image: url('{{ asset('images/slide_images/c3f3e44e519a965febd8b7b9877fd004cafe75b6.jpg') }}')" >
			
		</li>
		<li style="background-image: url('{{ asset('images/slide_images/dfe8aa7828b5163dbff3ead2c4fd10e68c86e141.jpg') }}')" >
			
		</li>
	</ul>
</div>
<div class="container">
    <div class="row">
        <!--<div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Home</div>

                <div class="panel-body">
                    This is home page!
                </div>
            </div>
        </div>-->
    </div>
</div>

<script>
$('#slides').flexslider({
	animation: "slide",
	animationLoop: true, 
	slideshowSpeed: 5000,
});
</script>
@endsection
