<?php

namespace App\Http\Controllers\user;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\Rule;
use App\SurveyEntry;
use App\SurveyEntryEsg;
use App\SurveyEntryStakeholder;
use App\SendSurvey;
use App\Survey;
use App\SurveyEsg;
use App\SurveyStakeholder;
use App\User;
use App\Company;
use App\Language;
use App\EmailTemplate;
use App\EmailTemplateTranslation;
use App\EsgCategory;
use SurveyEntryHelper;
use Mail;
use LaravelLocalization;
use DB;

class FrontendController extends Controller
{
	/**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    public function index()
    {
        return view('user.home');
    }

    public function sendSurvey(Request $request)
    {
        $cId = Auth::user()->company_id;
        $input = request()->all();
        $success = false;
        $error = false;
        if(isset($input['proceed'])) {
                
            $this->validate(request(), [
                'email' => 'required',
                'survey' => 'required',
            ]);
            $emails = explode(',', $input['email']);

            $locale = LaravelLocalization::getCurrentLocale();
            $emailTemplate = EmailTemplate::where('alias_name', 'survey_invitation')->select('id')->first();
            $emailTemplateData = EmailTemplateTranslation::where('email_template_id', $emailTemplate->id)
                            ->where('locale', $locale)                            
                            ->select('subject','content')->first();

            $company = Company::select('name','fiscal_year')->find($cId);
            
            $subject = $emailTemplateData->subject;
            $subject = str_replace('[FISCAL_YEAR]', date('Y',strtotime($company->fiscal_year)), $subject);
            $subject = str_replace('[COMPANY]', $company->name, $subject);

            $content = $emailTemplateData->content;
            $content = str_replace('[FISCAL_YEAR]', date('Y',strtotime($company->fiscal_year)), $content);
            $content = str_replace('[COMPANY]', $company->name, $content);

            $boundary = str_replace(" ", "", date('l jS \of F Y h i s A'));
            $headers = "From: AscentPartners <info@ascentpartners.com>" . "\r\n";
            //$headers = "From: Cyblance <info@cyblance.com>" . "\r\n";
            $headers .= "Reply-To: info@ascentpartners.com" . "\r\n";
            $headers .= "Return-Path: info@ascentpartners.com" . "\r\n";
            $headers .= "X-Sender: Cyblance <info@ascentpartners.com>" . "\r\n";
            $headers .= "X-Mailer: PHP/" . phpversion();
            $headers .= "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-Type: multipart/alternative; boundary = \"" . $boundary . "\"". "\r\n";
            $headers .= '--' . $boundary . "\r\n";
            $headers .= 'Content-Type: text/html; charset=UTF-8' . "\r\n";

            $msgSuccess = '<ul>';
            $msgError = '<ul>';
            foreach ($emails as $email) {
                $separate_emails = explode(';', $email);  
                $already_send_mail='';              
                foreach ($separate_emails as $semail){
                    $isSurveyEmailExists = SurveyEntryHelper::surveyEmailExists($semail, $input['survey']);
                    if($isSurveyEmailExists){

                        $saveData = array('company_id'=>$cId, 'user_id'=>Auth::user()->id, 'survey_id'=>$input['survey'], 'email'=>$semail);
                        $hash = Crypt::encryptString(json_encode(array('company_id'=>$cId, 'survey_id'=>$input['survey'], 'email'=>$semail)));
                        SendSurvey::create($saveData);
                        
                        $servey_url = LaravelLocalization::localizeURL('/stakeholder-welcome/'.$hash);
                        $content = str_replace('[SURVEY_URL]', $servey_url, $content);
                        
                        mail($semail,$subject,$content,$headers,'-finfo@ascentpartners.com');
                        $success = true;
                        //$msgSuccess .= '<li>'.trans('messages.survey_sent_to').'</li>';
                    }else{
                        $error = true;
                        //$msgError .= '<li>'.trans('messages.already_survey_sent_to').' "'.$semail.'".</li>';
                        $already_send_mail.=$semail.';';
                    }
                }
            }
            $msgError .= '</ul>';
            $msgSuccess .= '</ul>';

        }        
        if($success == true){
           $msgSuccess = '<li>'.trans('messages.survey_sent_to').'</li>';
           $msgError = ''; 
        }
        if($success == false){
           $msgSuccess = '';
            if($error == true){           
               $sendedmail=mb_substr($already_send_mail, 0,-1);
               $msgError = '<li>'.trans('messages.already_survey_sent_to').'.</li>';  
            }
        }
        
        if($error == false){
           $msgError = ''; 
        }
        //$surveys = Survey::select('id','title')->where('expiry_date', '>=', date('Y-m-d'))->orderBy('title')->get();
        $surveys = Survey::select('id','title')->where([
                    ['expiry_date', '>=', date('Y-m-d')],
                    ['company_id', '=', $cId],
                ])->orderBy('title')->get();
        //print_r($msgSuccess);exit;
        return view('user.send-survey', array('surveys'=>$surveys, 'success'=>$msgSuccess, 'error'=>$msgError));

        /*$input = request()->all();
        if(isset($input['proceed'])) {
            echo '<pre>';print_r($input);exit();    
            $cId = Auth::user()->company_id;
            $this->validate(request(), [
                'email' => 'required|email|uniqueSurveyEmail',
                'survey' => 'required',
            ],[
                'email.unique_survey_email' => trans('validation.unique'),
            ]);

            $saveData = array('company_id'=>$cId, 'user_id'=>Auth::user()->id, 'survey_id'=>$input['survey'], 'email'=>$input['email']);
            $hash = Crypt::encryptString(json_encode(array('company_id'=>$cId, 'survey_id'=>$input['survey'], 'email'=>$input['email'])));
            SendSurvey::create($saveData);
            $locale = LaravelLocalization::getCurrentLocale();
            $emailTemplate = EmailTemplate::where('alias_name', 'survey_invitation')->select('id')->first();
            $emailTemplateData = EmailTemplateTranslation::where('email_template_id', $emailTemplate->id)
                            ->where('locale', $locale)
                            ->where('status', '1')
                            ->select('subject','content')->first();

            $company = Company::select('name','fiscal_year')->find($cId);
            $content = $emailTemplateData->content;
            $content = str_replace('[FISCAL_YEAR]', $company->fiscal_year, $content);
            $content = str_replace('[COMPANY]', $company->name, $content);
            //$servey_url = url('/stakeholder-welcome/'.$hash);
            $servey_url = LaravelLocalization::localizeURL('/stakeholder-welcome/'.$hash);
            $content = str_replace('[SURVEY_URL]', $servey_url, $content);
            
            $boundary = str_replace(" ", "", date('l jS \of F Y h i s A'));
            $headers =  "From: AscentPartners <info@ascentpartners.com>" . "\r\n";
            $headers .= "X-Mailer: PHP/" . phpversion();
            $headers .= "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-Type: multipart/alternative; boundary = \"" . $boundary . "\"". "\r\n";
            $headers .= '--' . $boundary . "\r\n";
            $headers .= 'Content-Type: text/html; charset=ISO-8859-1' . "\r\n";

            mail($input['email'],$emailTemplateData->subject,$content,$headers);

            $request->session()->flash('success', trans('messages.survey_sent'));
        }
        $surveys = Survey::select('id','title')->where('expiry_date', '>=', date('Y-m-d'))->orderBy('title')->get();
        return view('user.send-survey', array('surveys'=>$surveys));*/
    }

    public function takeSurvey(Request $request)
    {
        $input = request()->all();
        if(isset($input['proceed'])) {
            $this->validate(request(), [
                /*'email' => [
                        'required',
                        'email',
                        Rule::unique('survey_entries')->where(function ($query) use($input) {
                            $query->where('company_id', Auth::user()->company_id);
                            $query->where('survey_id',$input['survey']);
                            return $query;
                        })
                ],*/
                'email' => 'required|email|uniqueSurveyEmail',
                'survey' => 'required',
            ],[
                'email.unique_survey_email' => trans('validation.unique'),
            ]);
            $hash = Crypt::encryptString(json_encode(array('company_id'=>Auth::user()->company_id, 'survey_id'=>$input['survey'], 'email'=>$input['email'])));
            //echo $hash; exit(); 
            return redirect('stakeholder-welcome/'.$hash);
        }
        //$surveys = Survey::select('id','title')->where('expiry_date', '>=', date('Y-m-d'))->orderBy('title')->get();
        $surveys = Survey::select('id','title')->where([
                    ['expiry_date', '>=', date('Y-m-d')],
                    ['company_id', '=', Auth::user()->company_id],
                ])->orderBy('title')->get();
        return view('user.take-survey', array('surveys'=>$surveys));
    }

    public function stakeholderWelcome(Request $request, $hash)
    {
        $surveyUniqueData = json_decode(Crypt::decryptString($hash));
        $request->session()->put('stakeholder_company_id', $surveyUniqueData->company_id);
        $request->session()->put('stakeholder_survey_id', $surveyUniqueData->survey_id);
        $request->session()->put('stakeholder_email', $surveyUniqueData->email);
        $isSurveyExists = SurveyEntryHelper::surveyEntryExists($request->session());
        if($isSurveyExists){
            return redirect('completed-survey');
        }
		$company = Company::select('name')->find($surveyUniqueData->company_id);
		$survey = Survey::select('fiscal_entry')->find($surveyUniqueData->survey_id);
		$fiscal_entry = explode(' / ', $survey->fiscal_entry);
		//echo "<pre>";print_r($fiscal_entry[0]);
		//echo "<pre>";print_r($company->name);
        return view('user.stakeholder-welcome', array('hash'=>$hash,'company'=>$company->name,'fiscal_year'=>$fiscal_entry[0]));
    }

    public function stakeholder(Request $request)
    {
        if (!$request->session()->has('stakeholder_email')) {
            if (Auth::check()) {
                $email = Auth::user()->email;
                $request->session()->put('stakeholder_email', $email);
            }else{
                return redirect('take-survey');
            }
        }
        $isSurveyExists = SurveyEntryHelper::surveyEntryExists($request->session());
        if($isSurveyExists){
            return redirect('completed-survey');
        }
        $company_id = $request->session()->get('stakeholder_company_id');
        $survey_id = $request->session()->get('stakeholder_survey_id');
        $email = $request->session()->get('stakeholder_email');
        $hash = Crypt::encryptString(json_encode(array('company_id'=>$company_id, 'survey_id'=>$survey_id, 'email'=>$email)));

        $company = Company::select('name')->find($company_id);

        //$survey = Survey::select('stakeholder_ids')->find($survey_id);
        $locale = LaravelLocalization::getCurrentLocale();
        $stakeholders = DB::table('stakeholders')
            ->join('survey_stakeholders', 'stakeholders.id', '=', 'survey_stakeholders.stakeholder_id')
            ->join('stakeholder_translations', 'stakeholders.id', '=', 'stakeholder_translations.stakeholder_id')
            ->select('stakeholders.id','stakeholder_translations.title', 'stakeholders.textbox_support_required', 'stakeholders.survey_choice')
            ->where('stakeholder_translations.locale', $locale)
            ->where('survey_stakeholders.survey_id', $survey_id)
            ->orderBy('survey_stakeholders.id', 'asc')
            //->whereIn('stakeholders.id', explode(',', $survey->stakeholder_ids))
            //->orderByRaw('FIELD(stakeholders.id, '.$survey->stakeholder_ids.')')
            ->get();

        if ($request->has('submit')) {
            $input = request()->all();
            if(request()->has('stakeholder') && request()->has('stakeholder_comment_'.$input['stakeholder']) && $input['stakeholder_comment_'.$input['stakeholder']]==''){               
                $this->validate(request(), [
                    'stakeholder_comment_'.request()->get('stakeholder') => 'required',
                ],[
                    'stakeholder_comment_'.request()->get('stakeholder').'.required' => trans('messages.stakeholderCommentRequired'),
                ]);
            }else{
                $this->validate(request(), [
                    'stakeholder' => 'required',
                ],[
                    'stakeholder.required' => trans('messages.stakeholderRequired'),
                ]);
            }
            $request->session()->put('stakeholder', $input['stakeholder']);
            $request->session()->put('survey_choice', $input['survey_choice_'.$input['stakeholder']]);
            if(isset($input['stakeholder_comment_'.$input['stakeholder']])){
                $request->session()->put('stakeholder_comment', $input['stakeholder_comment_'.$input['stakeholder']]);
            }
            return redirect('stakeholder-engagement');
        }

        return view('user.stakeholder', array('stakeholders' => $stakeholders, 'hash'=>$hash,'company'=>$company->name));
    }

    public function stakeholderInstanceSummary(Request $request){

        $input = request()->all();		
        $WHERE = '';
        $survey_id='';
        if (isset($input['survey'])) {
            $WHERE .= ' AND id='.$input['survey'];
            $survey_id = $input['survey'];
			
			$sql = 'SELECT *, ( SELECT COUNT(`survey_entry_stakeholders`.`id`) FROM `survey_entries` LEFT JOIN `survey_entry_stakeholders` ON `survey_entries`.`id` = `survey_entry_stakeholders`.`survey_entry_id` WHERE `survey_entries`.`survey_id` = `surveys`.`id`) as total_survey FROM surveys WHERE company_id = '.Auth::user()->company_id.$WHERE.' LIMIT 1';
			$get_survey_summary = DB::select($sql);
			$survey_summary = $get_survey_summary[0];
        }else{
			$survey_summary = '';
			$survey_id = '';
		}		
        $surveys = Survey::select('id','title')->where('company_id', Auth::user()->company_id)->orderBy('title')->get();
        
        return view('user.stakeholder-instance-summary', array('surveys' => $surveys, 'survey_summary' => $survey_summary, 'survey_id' => $survey_id));
    }

    public function getStakeholderComments($id,$survey_id){

        $sql = 'SELECT survey_entries.email, survey_entry_stakeholders.stakeholder_comment
                FROM survey_entries, survey_entry_stakeholders
                WHERE survey_entries.id = survey_entry_stakeholders.survey_entry_id
                AND survey_entry_stakeholders.stakeholder_id='.$id.'
				AND survey_entries.survey_id='.$survey_id.'
                AND survey_entry_stakeholders.stakeholder_comment <> ""';
        $stakeholder_comments = DB::select($sql);
        return view('user.stakeholder_comments', compact('stakeholder_comments'));
    }

    public function stakeholderEngagement(Request $request)
    {
        if (!$request->session()->has('stakeholder')) {
            return redirect('stakeholder');
        }
        $isSurveyExists = SurveyEntryHelper::surveyEntryExists($request->session());
        if($isSurveyExists){
            return redirect('completed-survey');
        }

        //$survey = Survey::select('esg_ids')->find($request->session()->get('stakeholder_survey_id'));
        $locale = LaravelLocalization::getCurrentLocale();
        $esgs = DB::table('esgs')
            ->join('survey_esgs', 'esgs.id', '=', 'survey_esgs.esg_id')
            ->join('esg_translations', 'esgs.id', '=', 'esg_translations.esg_id')
            ->select('esgs.id', 'esgs.category_id', 'esg_translations.title', 'esg_translations.information')
            ->where('esg_translations.locale', $locale)
            ->where('survey_esgs.survey_id', $request->session()->get('stakeholder_survey_id'))
            ->orderBy('survey_esgs.id', 'asc')
            //->whereIn('esgs.id', explode(',', $survey->esg_ids))
            //->orderByRaw('FIELD(esgs.id, '.$survey->esg_ids.')')
            ->get();
        $esg_categories = EsgCategory::all();
        $input = '';
        if ($request->has('submit')) {
            $input = request()->all();

            $cnt_s = 0;
            $cnt_c = 0;
            if($request->has('esg')){
                if($request->has('esg.stakeholder')){
                    $cnt_s = count($input['esg']['stakeholder']);    
                }
                if($request->has('esg.company')){
                    $cnt_c = count($input['esg']['company']);
                }
            }
            $input_cnt = $cnt_s +  $cnt_c;
            $esg_lenght = count($esgs);
            if($request->session()->get('survey_choice')=='Stakeholder and Company'){
                $esg_lenght = count($esgs) * 2;
            }
            if($input_cnt != $esg_lenght){
                $request->session()->flash('error', trans('messages.esgRequired'));
            }else{
                $data1 = array();
                $data2 = array();
                $data3 = array();
                $data1['company_id'] = $request->session()->get('stakeholder_company_id');
                $data1['survey_id'] = $request->session()->get('stakeholder_survey_id');
                $data1['email'] = $request->session()->get('stakeholder_email');
                $data2['stakeholder_id'] = $request->session()->get('stakeholder');
                if ($request->session()->has('stakeholder_comment')) {
                    $data2['stakeholder_comment'] = $request->session()->get('stakeholder_comment');
                }
                $survey = SurveyEntry::create($data1);
                $data2['survey_entry_id'] = $survey->id;
                SurveyEntryStakeholder::create($data2);
                $data3['survey_entry_id'] = $survey->id;
                foreach ($input['esg'] as $key => $value) {
                    if($key == 'stakeholder'){
                        $data3['survey_type'] = 'stakeholder';
                    } else if($key == 'company'){
                        $data3['survey_type'] = 'company';
                    }
                    foreach ($value as $esg_id => $esg_value) {   
                        $data3['esg_id'] = $esg_id;
                        $data3['esg_value'] = $esg_value;
                        SurveyEntryEsg::create($data3);
                    }
                }
                /*$request->session()->forget('stakeholder_company_id');
                $request->session()->forget('stakeholder_survey_id');
                $request->session()->forget('stakeholder_email');
                $request->session()->forget('stakeholder_comment');*/
                return redirect('thank-you');
            }
        }

        return view('user.stakeholder-engagement', array('esgs' => $esgs, 'survey_choice' => $request->session()->get('survey_choice'), 'esg_categories' => $esg_categories, 'input' => $input));
    }

    public function editProfile(Request $request, $id)
    {
        //print_r($request->all());exit();
        if ($request->has('submit')) {
            $this->validate(request(), [
                'first_name' => 'required|string',
                'email' => 'required|email|unique:users,email,'.$id,
                'title' =>  'required|string',
                'department' =>  'required|string',
                //'company_id' =>  'required',
                'phone.*' => 'nullable|numeric',
            ], [
                //'company_id.required' => 'The company field is required.',
                'phone.*.numeric' => 'The phone must be a number.',
            ]);
            
            $input = request()->all();
            $input['phone'] = json_encode(array_values(array_filter($_POST['phone'])));
            //print_r($input);//exit();
            User::find($id)->update($input);
            $request->session()->flash('success', trans('messages.edit_profile_updated'));
        }
        
        $user = User::findOrFail($id);
        return view('user.edit-profile', compact('user'));
    }
}
