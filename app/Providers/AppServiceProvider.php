<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\UrlGenerator;
use App\Survey;
use App\Language;
use App\StakeholderTranslation;
use App\EsgTranslation;
use App\SurveyEntry;
use App\SendSurvey;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(UrlGenerator $url)
    {
        if(env('REDIRECT_HTTPS'))
        {
            $url->forceScheme('https');
        }

        Validator::extend('uniqueSurvey', function($attribute, $value, $parameters, $validator){

            $input = $validator->getData();
            $surveys = Survey::where('company_id', $input['company_id'])
                           ->where('fiscal_entry',$input['fiscal_year'].' / '.$input['fiscal_month']);
            if(isset($input['id'])){
                $surveys = $surveys->where('id', '<>', $input['id']);    
            }
            $surveys = $surveys->exists();
            if(!empty($surveys)){
                return false;
            }else{
                return true;
            }
        });

        $languages = Language::all();
        foreach ($languages as $language) {
            $alias_name = str_replace('-','_', strtolower($language->alias_name));
            Validator::extend('unique_stakeholder_title_'.$alias_name, function($attribute, $value, $parameters, $validator) use ($language) {
                $input = $validator->getData();
                $result = StakeholderTranslation::where('title',$input['title_'.$language->alias_name])
                        ->where('locale',$language->alias_name);
                if(isset($input['tid_'.$language->alias_name])){
                    $result = $result->where('id', '<>', $input['tid_'.$language->alias_name]);    
                }      
                $result = $result->exists();
                if($result){
                    return false;
                }else{
                    return true;
                }
            });

            Validator::extend('unique_esg_title_'.$alias_name, function($attribute, $value, $parameters, $validator) use ($language) {
                $input = $validator->getData();
                $result = EsgTranslation::where('title',$input['title_'.$language->alias_name])
                        ->where('locale',$language->alias_name);
                if(isset($input['tid_'.$language->alias_name])){
                    $result = $result->where('id', '<>', $input['tid_'.$language->alias_name]);    
                }      
                $result = $result->exists();
                if($result){
                    return false;
                }else{
                    return true;
                }
            });
        }

        Validator::extend('uniqueSurveyEmail', function($attribute, $value, $parameters, $validator){

            $input = $validator->getData();

            $SurveyEntry = SurveyEntry::where('email', $input['email'])
                            ->where('company_id', Auth::user()->company_id)
                            ->where('survey_id',$input['survey'])
                            ->exists();

            $SendSurvey = SendSurvey::where('email', $input['email'])
                            ->where('company_id', Auth::user()->company_id)
                            ->where('survey_id',$input['survey'])
                            ->exists();

            if(!empty($SurveyEntry) || !empty($SendSurvey)){
                return false;
            }else{
                return true;
            }
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
