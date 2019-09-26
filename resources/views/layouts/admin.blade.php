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
	<link href="{{ asset('css/jquery-ui.css') }}" rel="stylesheet">
	<link href="{{ asset('css/jquery.datatables.css') }}" rel="stylesheet">
	<link href="{{ asset('public/css/fontawesome/css/font-awesome.min.css') }}" rel="stylesheet" type="text/css"/>
	<link href="{{ asset('css/app.css') }}" rel="stylesheet">	
	<link href="{{ asset('css/modern.css') }}" rel="stylesheet">
	<link href="{{ asset('css/blue.css') }}" rel="stylesheet">
	<link href="{{ asset('css/waves.css') }}" rel="stylesheet">
	<link href="{{ asset('css/admin.css') }}" rel="stylesheet">	
	
	
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
		<script src="{{ asset('js/ie.js') }}"></script>
        <link href="{{ asset('css/ie.css') }}" rel="stylesheet">
    <![endif]-->
	<script src="{{ asset('js/jquery-2.1.4.min.js') }}"></script>	
	
    <!--<script src="https://code.jquery.com/jquery-1.12.4.js"></script>-->
	
	<script src="{{ asset('js/jquery-ui.min.js') }}"></script>	
	
	<script src="{{ asset('js/jquery.datatables.js') }}"></script>	
	<script src="{{ asset('js/jquery.slimscroll.js') }}"></script>
	
	<!--<script src="{{ asset('js/waves.min.js') }}"></script>-->
	<script src="{{ asset('js/modern.js') }}"></script>
</head>

 <body class="page-header-fixed page-sidebar-fixed">
      <div class="overlay"></div>
	  <main class="page-content content-wrap">
            <div class="navbar">
                <div class="navbar-inner">
                    <div class="sidebar-pusher">
                        <a href="javascript:void(0);" class="waves-effect waves-button waves-classic push-sidebar">
                            <i class="fa fa-bars"></i>
                        </a>
                    </div>
                    <div class="logo-box">
                        <a class="logo-text" href="{{ url('/') }}">
							<img src="{{asset('images/ap_logo.gif')}}" />
						</a>
                    </div><!-- Logo Box -->                   
                    <div class="topmenu-outer">
                        <div class="top-menu">               
							<ul class="nav navbar-nav navbar-left">
                                <li>		
                                    <a href="javascript:void(0);" class="sidebar-toggle"><i class="fa fa-bars"></i></a>
                                </li>                                                     
                            </ul>
                            <ul class="nav navbar-nav navbar-right">
								 <!-- Authentication Links -->
								@if(Auth::guard('admin')->user())
									<li class="dropdown">
										<a href="javascript:void(0);" class="dropdown-toggle log-out " data-toggle="dropdown" role="button" aria-expanded="false">
										   {{ Auth::guard('admin')->user()->first_name }} <i class="fa fa-angle-down"></i>
										</a>

										<ul class="dropdown-menu" role="menu">
											<li>
												<a href="{{ route('admin.logout') }}"
												   onclick="event.preventDefault();
													 document.getElementById('admin-logout-form').submit();">
													Logout
												</a>

												<form id="admin-logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
													{{ csrf_field() }}
												</form>
											</li>
										</ul>
									</li>
								@else
									<li><a href="{{ route('admin.login') }}">Login</a></li>
								@endif                                                          
                            </ul><!-- Nav -->
                        </div><!-- Top Menu -->
                    </div>
                </div>
            </div><!-- Navbar -->
            <div class="page-sidebar sidebar">
                <div class="page-sidebar-inner slimscroll">                
                    <ul class="menu accordion-menu">
					    <li class="{!! Request::is('admin/admins*') ?  'active' : ''!!}">
							<a class="waves-effect waves-button" href="{{ route('admins.index') }}">
								<i class="fa fa-user" aria-hidden="true"></i> <p>Admins</p>
							</a>
						</li>
                        <li class="{!! Request::is('admin/companies*') ?  'active' : ''!!}">
							<a class="waves-effect waves-button"  href="{{ route('companies.index') }}">
								<i class="fa fa-user-circle-o" aria-hidden="true"></i><p>Clients</p>
							</a>
						</li>
                        <li class="{!! Request::is('admin/users*') ?  'active' : ''!!}">
							<a class="waves-effect waves-button" href="{{ route('users.index') }}">	
								<i class="fa fa-user-circle-o" aria-hidden="true"></i> <p>Users</p>
							</a>
						</li>
                        <li class="{!! Request::is('admin/stakeholders*') ?  'active' : ''!!}">
							<a class="waves-effect waves-button" href="{{ route('stakeholders.index') }}">
								<i class="fa fa-users" aria-hidden="true"></i> <p>Stakeholders</p>
							</a>
						</li>
                        <li class="{!! Request::is('admin/esg*') ?  'active' : ''!!}">
							<a class="waves-effect waves-button" href="{{ route('esg.index') }}">
								<i class="fa fa-dollar" aria-hidden="true"></i> <p>ESG Aspects</p>
							</a>
						</li>
                       
                        <li class="{!! Request::is('admin/surveys*') ?  'active' : ''!!}">
							<a class="waves-effect waves-button" href="{{ url('/admin/surveys') }}">
								<i class="fa fa-file-text-o" aria-hidden="true"></i> <p>Surveys</p>
							</a>
						</li>
                        {{--<li class="{!! Request::is('admin/survey_entries*') ?  'active' : ''!!}">
							<a class="waves-effect waves-button" href="{{ url('/admin/survey_entries') }}">
								<i class="fa fa-edit" aria-hidden="true"></i> <p>Survey Entries</p>
							</a>
						</li>--}}
                        <li class="droplink {!! Request::is('admin/all-companies-survey-report') || Request::is('admin/individual-company-survey-report') ?  'active' : ''!!}">
							<a class="waves-effect waves-button">
								<i class="fa fa-edit" aria-hidden="true"></i>  <p>Survey Report</p>
                                <span class="arrow"></span>
							</a>
                            <ul class="sub-menu">
                                <li class="{!! Request::is('admin/all-companies-survey-report') ?  'active' : ''!!}">
                                    <a class="waves-effect waves-button" href="{{ url('/admin/all-companies-survey-report') }}">
										<p>All Companies</p>
                                    </a>
                                </li>
                                <li class="{!! Request::is('admin/individual-company-survey-report') ?  'active' : ''!!}">
                                    <a class="waves-effect waves-button" href="{{ url('/admin/individual-company-survey-report') }}">
                                        <p>Individual Company</p>
                                    </a>
                                </li>
                            </ul>
						</li>
						 <li class="{!! Request::is('admin/email-templates*') ?  'active' : ''!!}">
							<a class="waves-effect waves-button" href="{{ url('/admin/email-templates') }}">
								<i class="fa fa-envelope" aria-hidden="true"></i> <p>Email Templates</p>
							</a>
						</li>
                    </ul>
                </div><!-- Page Sidebar Inner -->
            </div><!-- Page Sidebar -->
		  	<div class="page-inner">
		  	 @yield('content')
		  	
				<div class="page-footer">
					<p class="no-s">Copyright © 2018 All rights reserved.</p>
				</div>
			</div>
	 </main>
	 <script>
        jQuery(function(){
            Resizefunction();

        });
        jQuery(window).resize(function(){
            Resizefunction();
        });
        function Resizefunction(){
            var Wiheight = $(window).height();
            var OutTop = $('.panel').offset().top;
            var Foheight = $('.page-footer').innerHeight();
            var BordHi = Wiheight-OutTop-Foheight-20;
            $('.panel').css('min-height',BordHi+'px').css('margin','0px');
            
        }
         var extensions = {
            //"sFilter": "dataTables_filter Slectbox",
            "sLength": "dataTables_length Slectbox"
        }
        // Used when bJQueryUI is false
        $.extend($.fn.dataTableExt.oStdClasses, extensions);
        // Used when bJQueryUI is true
        $.extend($.fn.dataTableExt.oJUIClasses, extensions);
    </script>
</body>	
</html>
