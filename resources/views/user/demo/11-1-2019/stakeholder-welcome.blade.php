@extends('layouts.app')

@section('content')
<div class="page-inner">	
    <div class="lg-title">
        <div class="container">
            <h3>{!! trans('messages.WelcomeStakeholderPageHeader') !!}</h3>
        </div>
    </div>
    <section class="section UserPage">
		<div class="container"> 
			<div class="panel panel-default">
				<div class="panel-body">
					@php						
						$content=trans('messages.WelcomeStakeholderPageBody');						
						if(strpos($content,'[Company]') !== false){
							$content=str_replace("[Company]",$company,$content);
						}
						if(strpos($content,'[Fiscal Year]') !== false){
							$content=str_replace("[Fiscal Year]",$fiscal_year,$content);
						}
					@endphp
					{!! $content !!}
					
					<hr style="border-width: 3px;">
					<p class="text-center"><b><u>{!! trans('messages.WelcomeStakeholderPageFooter') !!} | 
					请选择您的语言 | 請選擇您的語言。</u></b></p>
					<div class="row">
						<div class="col-md-4 text-left">
							<a href="{{ url('/zh/stakeholder') }}" class="btn btn-primary">简体中文</a>
						</div>
						<div class="col-md-4 text-center">
							<a href="{{ url('/zh-Hant/stakeholder') }}" class="btn btn-primary">繁體中文</a>
						</div>
						<div class="col-md-4 text-right">
							<a href="{{ url('/en/stakeholder') }}" class="btn btn-primary">English</a>
						</div>
					</div>
				</div>
			</div>
		</div>
    </section>
</div>
@endsection
