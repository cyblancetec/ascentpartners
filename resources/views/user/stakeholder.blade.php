@extends('layouts.app')

@section('content')
<div class="page-inner">	
    <div class="lg-title">
        <div class="container">
            <h3>{!! trans('messages.StakeholderPageTitle') !!}</h3>
        </div>
    </div>
    <section class="section UserPage">
		<div class="container"> 
			<div class="panel panel-default">
            <div class="panel-body">
                @if ($errors->count() > 0)
                    <div class="alert alert-danger">
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @php                    
                    $content=trans('messages.StakeholderPageContent');
                    if(strpos($content,'[Company]') !== false){
                        $content=str_replace("[Company]",$company,$content);
                    }
                @endphp
                <b> {!! $content !!} </b>
                <hr style="border-width: 4px;margin-top: 10px;border-color: #008ab0;">
                <form method="post">
                    {{ csrf_field() }}
                    <div class="stakeholder_group">
                        @foreach($stakeholders as $stakeholder)
                            <input type="hidden" name="survey_choice_{{ $stakeholder->id }}" value="{{ $stakeholder->survey_choice }}">
                            <span>
                                <input type="radio" id="stakeholder_{{ $stakeholder->id }}" name="stakeholder" value="{{ $stakeholder->id }}" {{ old('stakeholder') ? 'checked' : '' }}> <label for="stakeholder_{{ $stakeholder->id }}">{{ $stakeholder->title }}</label>
                            </span>
                            @if($stakeholder->textbox_support_required=='yes')
                            <div class="stakeholder_group2">
                                <div class="form-group">
                                    <textarea class="form-control" name="stakeholder_comment_{{ $stakeholder->id }}">{{ old('stakeholder_comment_'.$stakeholder->id) }}</textarea>
                                </div>
                            </div>
                            @endif
                        @endforeach
                        <br>
                    </div>
                    <div class="text-center">
                        <input type="hidden" name="engagement" value="engagement">
                        <a href="{{ url('/stakeholder-welcome/'.$hash) }}" style="float: left" class="btn btn-primary" >{{ trans('messages.Back') }}</a>
                        <input type="submit" class="btn btn-primary" name="submit" value="{{ trans('messages.Ok') }}" style="float: right" />
                    </div>
                </form>
                <br>
          </div>
        </div>
	   </div>
    </section>
</div>
<script type="text/javascript">
    jQuery( document ).ready(function() {
		$("#language_menu").css("display", "none");
        /*jQuery("#language_menu a").click(function(e) {
              e.preventDefault();
        });*/
    });
</script>
@endsection
