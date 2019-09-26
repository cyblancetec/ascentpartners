<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="{{ asset('public/images/favicon.ico')}}" type="image/x-icon" />
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/jquery.datatables.css') }}" rel="stylesheet">
	<link href="{{ asset('css/flexslider.css') }}" rel="stylesheet">
	<link href="{{ asset('css/style.css') }}" rel="stylesheet">
	<link href="{{ asset('public/css/fontawesome/css/font-awesome.min.css') }}" rel="stylesheet" type="text/css"/>
	
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>

    <![endif]-->
    
    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
	
    <!--[if IE 8]>
        <script src="{{ asset('js/jquery-1.11.1.min.js') }}"></script>
        <link href="{{ asset('css/ie.css') }}" rel="stylesheet">
    <![endif]-->
	<script src="{{ asset('js/jquery.flexslider.js')}}"></script>
    <script src="{{ asset('js/jquery.datatables.js') }}"></script>
	<script src="{{ asset('js/basic.js')}}"></script>
	
    
    
	
</head>
	
<body>
    <div id="main_wrapper">
		<div id="header" class="min_1000 overlay_elem">
        	<nav class="navbar navbar-default navbar-static-top">            	
                <div class="navbar-header">
                    <!-- Collapsed Hamburger -->
					<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
                    <!-- Branding Image -->
                    <a class="navbar-brand" href="{{ url('/') }}">
                       	<img src="{{asset('images/ap_logo.gif')}}" />
                    </a>
                </div>
                @php $menu = 'show';
                $local = app()->getLocale();
                if(!Auth::check() && (Request::is($local.'/stakeholder-welcome*') || Request::is($local.'/stakeholder') || Request::is($local.'/stakeholder-engagement') || Request::is($local.'/thank-you') || Request::is($local.'/completed-survey'))){
                    $menu = 'hide';
                } @endphp
				<div id="Desktop-nav" class="collapse navbar-collapse pull-right" id="app-navbar-collapse">
                    <ul class="nav navbar-nav">
                        <!-- Authentication Links -->
                        @if($menu=='show')
                            <li><a href="{{ url('/') }}">{{ trans('messages.Home') }}</a></li>
                            <li>
                                <a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                    {{ trans('messages.StakeholderEngagement') }}
    								<i class="fa fa-angle-down"></i>
                                </a>
                                <ul class="dropdown-menu" role="menu">
                                    <li><a href="{{ url('/take-survey') }}">{{ trans('messages.TakeSurvey') }}</a></li>
                                    <li><a href="{{ url('/stakeholder-instance-summary') }}">{{ trans('messages.SurveyInstanceSummary') }}</a></li>
                                    <li><a href="{{ url('/send-survey') }}">{{ trans('messages.SurveyInvitation') }}</a></li>
                                </ul>
                            </li>
                            @guest
                            <li><a href="{{ route('login') }}">{{ trans('messages.SignIn') }}</a></li>
                            @else
                            <li class="dropdown profile-dropdown">
                                <a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                    {{ Auth::user()->first_name }} <i class="fa fa-angle-down"></i>
                                </a>
                                <ul class="dropdown-menu" role="menu">
                                    <li><a href="{{ url('/edit-profile/'.Auth::user()->id ) }}">{{ trans('messages.EditProfile') }}</a></li>
                                    <li>
                                        <a href="{{ route('logout') }}"
                                           onclick="event.preventDefault();
                                             document.getElementById('logout-form').submit();">
                                            {{ trans('messages.SignOut') }}
                                        </a>

                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                            {{ csrf_field() }}
                                        </form>
                                    </li>
                                </ul>
                            </li>
                            @endguest
                        @endif
						<li>
                            <a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                <img src="{{asset('images/'.$local.'.png')}}"> {{ trans('messages.'.$local) }} 
								<i class="fa fa-angle-down"></i>
                            </a>
                            <ul id="language_menu" class="dropdown-menu" role="menu">
                                <li>
                                    <a href="{{ LaravelLocalization::getLocalizedURL('en', null, [], true) }}"> 
                                        <img src="{{asset('images/en.png')}}"> {{ trans('messages.en') }}
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ LaravelLocalization::getLocalizedURL('zh', null, [], true) }}">
                                        <img src="{{asset('images/zh.png')}}"> {{ trans('messages.zh') }}
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ LaravelLocalization::getLocalizedURL('zh-Hant', null, [], true) }}">
                                        <img src="{{asset('images/zh-Hant.png')}}"> {{ trans('messages.zh-Hant') }}
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
                <div id="Mobile-nav" class="collapse navbar-collapse pull-right" id="app-navbar-collapse">      
					<div class="header_left">
						 <a class="navbar-brand" href="{{ url('/') }}">
							<img src="{{asset('images/ap_logo.gif')}}" />
						 </a>
					</div>
                    <ul class="nav navbar-nav">
                        <!-- Authentication Links -->
                        @if($menu=='show')
                            <li><a href="{{ url('/') }}">{{ trans('messages.Home') }}</a></li>
                            <li>
                                <a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                    {{ trans('messages.StakeholderEngagement') }}
    								<i class="fa fa-angle-down"></i>
                                </a>
                                <ul class="dropdown-menu" role="menu">
                                    <li><a href="{{ url('/take-survey') }}">{{ trans('messages.TakeSurvey') }}</a></li>
                                    <li><a href="{{ url('/stakeholder-instance-summary') }}">{{ trans('messages.SurveyInstanceSummary') }}</a></li>
                                    <li><a href="{{ url('/send-survey') }}">{{ trans('messages.SurveyInvitation') }}</a></li>
                                </ul>
                            </li>
                            @guest
                                <li><a href="{{ route('login') }}">{{ trans('messages.SignIn') }}</a></li>
                            @else
                                <li class="dropdown">
                                    <a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                        {{ Auth::user()->first_name }} <i class="fa fa-angle-down"></i>
                                    </a>

                                    <ul class="dropdown-menu" role="menu">
                                        <li><a href="{{ url('/edit-profile/'.Auth::user()->id ) }}">{{ trans('messages.EditProfile') }}</a></li>
                                        <li>
                                            <a href="{{ route('logout') }}"
                                               onclick="event.preventDefault();
                                                 document.getElementById('logout-form').submit();">
                                                {{ trans('messages.SignOut') }}
                                            </a>

                                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                                {{ csrf_field() }}
                                            </form>
                                        </li>
                                    </ul>
                                </li>
                            @endguest
                        @endif
						 <li>
                            <a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                @php $local = app()->getLocale(); @endphp
								<img src="{{asset('images/'.$local.'.png')}}"> {{ trans('messages.'.$local) }} 
								<i class="fa fa-angle-down"></i>
                            </a>
                            <ul id="language_menu" class="dropdown-menu" role="menu">
                                <li>
                                    <a href="{{ LaravelLocalization::getLocalizedURL('en', null, [], true) }}"> 
                                        <img src="{{asset('images/en.png')}}"> {{ trans('messages.en') }}
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ LaravelLocalization::getLocalizedURL('zh', null, [], true) }}">
                                        <img src="{{asset('images/zh.png')}}"> {{ trans('messages.zh') }}
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ LaravelLocalization::getLocalizedURL('zh-Hant', null, [], true) }}">
                                        <img src="{{asset('images/zh-Hant.png')}}"> {{ trans('messages.zh-Hant') }}
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>            
        	</nav>
		</div>

        @yield('content')
		
        <!-- footer -->
        @if($menu=='show')
        <div id="footer" class="min_1000 overlay_elem">
            <div id="footer_menu">               
                <div class="footer_logo">
                    <img src="{{asset('images/SASB.jpg')}}">&nbsp;&nbsp;
                    <img src="{{asset('images/footer_logo.png')}}">
                </div>
            </div>

            <div id="footer_copyright">
                <table>
                    <tr>
                        <td>
                            {{ trans('messages.copyrights') }}
                            <br>
                            <!--<a href="#">Privacy</a> | <a href="#">Disclaimer</a> | <a href="#">Site Map</a>-->
                        </td>
                        <td width="40" align="right">
                            <a href="https://www.facebook.com/theascentpartners" target="_blank">
                                <img src="{{asset('images/ic_fb.png')}}" width="32" height="32" />
                            </a>
                        </td>
                        <td width="40" align="right">
                            <a href="https://www.linkedin.com/company/ascent-partners-group-" target="_blank">
                                <img src="{{asset('images/ic_ln.png')}}" width="32" height="32" />
                            </a>
                        </td>
                        <td width="40" align="right">
                            <a href="https://plus.google.com/+Ascentpartners168" rel="publisher" target="_blank">
                                <img src="{{asset('images/ic_gp.png')}}" width="32" height="32" />
                            </a>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        @endif
        <!-- footer end-->
        <script>
            jQuery(function (){
                Resizefunction();
            });
            jQuery(window).resize(function () {
                Resizefunction();
            });
            /*jQuery(window).load(function () {
                Resizefunction();
            });*/
            function Resizefunction() {			
                var Wiheight = $(window).innerHeight();	
                var HiHeight = $('#header').innerHeight();
                var Foheight = $('#footer').innerHeight();
                var BordHi = Wiheight - Foheight -HiHeight-1;
                $('.page-inner.page-login').css('height', BordHi + 'px');
                $('.page-inner').css('min-height', BordHi + 'px');
                $('#slides').css('height', BordHi + 'px');
				$('.flexslider .slides > li').css('height', BordHi + 'px');
            }
        </script>
    </div>
</body>
</html>
