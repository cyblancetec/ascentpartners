<?php

namespace App\Helpers;
use Config;
use DB;
use Illuminate\Support\Facades\Auth;
use App\SurveyEntry;
use App\SendSurvey;

class SurveyEntryHelper{
	
	public static function surveyEntryExists($session){

		$email = $session->get('stakeholder_email');
        $survey_id = $session->get('stakeholder_survey_id');
		$query = "SELECT email FROM survey_entries WHERE email='".$email."' AND survey_id=".$survey_id;
        $survey = DB::select($query);
        if(!empty($survey) && $survey[0]->email == $email){
        	return true;
     	}else{
     		return false;
     	}
    }

    public static function surveyEmailExists($email,$survey){

        $SurveyEntry = SurveyEntry::where('email', $email)
                        ->where('company_id', Auth::user()->company_id)
                        ->where('survey_id',$survey)
                        ->exists();

        $SendSurvey = SendSurvey::where('email', $email)
                        ->where('company_id', Auth::user()->company_id)
                        ->where('survey_id',$survey)
                        ->exists();

        if(!empty($SurveyEntry) || !empty($SendSurvey)){
            return false;
        }else{
            return true;
        }
    }
}
