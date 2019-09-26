@extends('layouts.app')

@section('content')
<div class="page-inner">	
    <div class="lg-title">
        <div class="container">
            <h3>{!! trans('messages.StakeholderEngagementPageTitle') !!}</h3>
        </div>
    </div>
    <section class="section UserPage">
		<div class="container"> 
        	<div class="panel panel-default">
				<div class="panel-body">
                    {!! trans('messages.StakeholderEngagementPageContent') !!}
				</div>
        	</div>
            @desktop
            <div class="panel panel-default DasktopPanel">
                <div class="panel-body">
                    @if (\Session::has('error'))
                        <div class="alert alert-danger">
                            <ul>
                                <li>{{ \Session::get('error') }}</li>
                            </ul>
                        </div>
                    @endif 
                    <form method="post">
                        {{ csrf_field() }}     
                        <div class="table-responsive Stakeholder-table row">
                            <div class="col-lg-12">
                                <table class="table table-bordered Stakeholder-table" width="100%">
                                    <thead>
                                        <tr>
                                            <th class="text-center border0" width="50%" colspan="6">{{ trans('messages.ImportantToStakeholder') }}</th>
                                            @if($survey_choice == 'Stakeholder and Company')
                                                <th class="border-none" style="width:20px;"></th>
                                                <th colspan="5" class="border0"  >{{ trans('messages.ImportantToCompany') }}</th>
                                            @endif
                                        </tr>
                                        <tr>
                                            <th style="width:300px;"></th>                                      
                                            <th class="text-center" style="font-size:11px; line-height:1.2;">{{ trans('messages.LeastImportant') }}</th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th  class="text-center" style="font-size:11px; line-height:1.2;">{{ trans('messages.MostImportant') }}</th>
                                            @if($survey_choice == 'Stakeholder and Company')
                                                <th class="border-none" style="width:20px;"></th>
                                                <th style="font-size:11px; line-height:1.2; text-align:center;">{{ trans('messages.LeastImportant') }}</th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th style="font-size:11px; line-height:1.2; text-align:center;">{{ trans('messages.MostImportant') }}</th>
                                            @endif
                                        </tr>
                                        <tr>
                                            <th  width="30%"></th>
                                             @for ($i = 1; $i < 6; $i++)
                                                <th class="text-center">
                                                    {{ $i }}
                                                </th>
                                            @endfor
                                            @if($survey_choice == 'Stakeholder and Company')
                                                <th class="border-none" style="width:20px;"></th>
                                                @for ($i = 1; $i < 6; $i++)
                                                <th class="text-center "  >
                                                    {{ $i }}
                                                </th>
                                                @endfor
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($esg_categories as $esg_category)
                                            @php $i=1; @endphp
                                            @foreach($esgs as $esg)
                                                @if($esg_category->id == $esg->category_id)
                                                    @if($i==1)
                                                        <tr>
                                                            <th class="border-none" colspan="6">
                                                                @php 

                                                                $local_key = LaravelLocalization::getCurrentLocale().'_title';
                                                                echo $esg_category->$local_key;
                                                                @endphp
                                                            </th>
                                                            @if($survey_choice == 'Stakeholder and Company')
                                                                <th class="border-none" colspan="6"></th>
                                                            @endif
                                                        </tr>
                                                    @endif
                                                    <tr>
                                                        <td class="align-middle text-left" align="left"  >
                                                            <span  style="display: inline-block; vertical-align: middle;"  title="{{ $esg->information }}">
                                                                <img style="margin-right:3px;" class="align-middle" src="{{ url('images/moreinfo.png') }}" width="20" height="20" align="middle">{{ $esg->title }} 
                                                            </span>
                                                        </td>
                                                        @for ($i = 1; $i < 6; $i++)
                                                            @php $checked = '';
                                                            if(isset($input['esg']['stakeholder'][$esg->id]) && $input['esg']['stakeholder'][$esg->id] == $i){
                                                                $checked = 'checked="checked"';
                                                            } @endphp
                                                            <td class="text-center" >
                                                                <input type="radio" name="esg[stakeholder][{{ $esg->id }}]" value="{{ $i }}" {{ $checked }}>
                                                            </td>
                                                        @endfor
                                                        @if($survey_choice == 'Stakeholder and Company')
                                                            <td class="border-none" style="width:20px;"></td>
                                                            @for ($i = 1; $i < 6; $i++)
                                                                @php $checked = '';
                                                                if(isset($input['esg']['company'][$esg->id]) && $input['esg']['company'][$esg->id] == $i){
                                                                    $checked = 'checked="checked"';
                                                                } @endphp
                                                                <td class="text-center borderT1">
                                                                    <input type="radio" name="esg[company][{{ $esg->id }}]" value="{{ $i }}" {{ $checked }}>
                                                                </td>
                                                            @endfor
                                                        @endif
                                                    </tr>
                                                    @php $i++; @endphp
                                                @endif
                                            @endforeach
                                        @endforeach
                                    </tbody>
                                </table>
                                <div class="row">
                                    <div class="col-md-12">
                                        <input type="submit" value="{{ trans('messages.Submit') }}" name="submit" class="btn btn-primary pull-right">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            @elsedesktop
            <div class="IpadPanel">
                <form method="post">
                    {{ csrf_field() }}
                    <div class="RangesTable">
                        <table class="table" width="100%">
                            <tr>
                                <th colspan="2">{{ trans('messages.ImportantToStakeholder') }}</th>
                            </tr>
                            @foreach($esg_categories as $esg_category)
                                @php $i=1; @endphp
                                @foreach($esgs as $esg)
                                    @if($esg_category->id == $esg->category_id)
                                        @if($i==1)
                                            <tr>
                                                <th class="border0" colspan="2">
                                                    @php 
                                                        $local_key = LaravelLocalization::getCurrentLocale().'_title';
                                                        echo $esg_category->$local_key;
                                                    @endphp
                                                </th>
                                            </tr>
                                        @endif
                                        <tr>
                                            <td class="border0" width="50%">
                                                <span  style="display: inline-block; vertical-align: middle;"  title="{{ $esg->information }}">
                                                    <img style="margin-right:3px;" class="align-middle" src="{{ url('images/moreinfo.png') }}" width="20" height="20" align="middle">{{ $esg->title }} 
                                                </span>
                                            </td>
                                            <td class="border0">
            									<div class="rangeBox">
                                                    <div id="rangeslider" class="rangeslider">
                                                        <input type="hidden" name="esg[stakeholder][{{ $esg->id }}]" value="1">
                                                    </div>
                                                    <div class="rangeText">
                                                        <span>1</span>
                                                        <span>2</span>
                                                        <span>3</span>
                                                        <span>4</span>
                                                        <span>5</span>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        @php $i++; @endphp
                                    @endif
                                @endforeach
                            @endforeach
                        </table>
                        @if($survey_choice == 'Stakeholder and Company')
                            <table class="table" width="100%">
                                <tr>
                                    <th colspan="2">{{ trans('messages.ImportantToCompany') }}</th>
                                </tr>
                                @foreach($esg_categories as $esg_category)
                                    @php $i=1; @endphp
                                    @foreach($esgs as $esg)
                                        @if($esg_category->id == $esg->category_id)
                                            @if($i==1)
                                                <tr>
                                                    <th class="border0" colspan="2">
                                                        @php 
                                                            $local_key = LaravelLocalization::getCurrentLocale().'_title';
                                                            print_r($esg_category->$local_key);
                                                        @endphp
                                                    </th>
                                                </tr>
                                            @endif
                                            <tr>
                                                <td class="border0" width="50%">
                                                    <span  style="display: inline-block; vertical-align: middle;"  title="{{ $esg->information }}">
                                                        <img style="margin-right:3px;" class="align-middle" src="{{ url('images/moreinfo.png') }}" width="20" height="20" align="middle">{{ $esg->title }} 
                                                    </span>
                                                </td>
                                                <td class="border0">
            										<div class="rangeBox">
                                                        <div id="rangeslider" class="rangeslider">
                                                            <input type="hidden" name="esg[company][{{ $esg->id }}]" value="1">
                                                        </div>
                                                        <div class="rangeText">
                                                            <span>1</span>
                                                            <span>2</span>
                                                            <span>3</span>
                                                            <span>4</span>
                                                            <span>5</span>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            @php $i++; @endphp
                                        @endif
                                    @endforeach
                                @endforeach
                            </table>
                        @endif
                        <div class="col-md-12">
                            <div style="text-align: center" class="form-group">
                              <input type="submit" value="{{ trans('messages.Submit') }}" name="submit" class="btn btn-primary">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            @enddesktop
		</div>
    </section>
</div>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script>
jQuery(function() {
    $( ".rangeslider" ).slider({
        min: 1,
        max: 5,
        value: 1,
        slide: function( event, ui ) {
            $(this).children('input').val(ui.value);
        }
    });

    /*jQuery("#language_menu a").click(function(e) {
          e.preventDefault();
    });*/
    $("#language_menu").css("display", "none");
});
</script>

@endsection
