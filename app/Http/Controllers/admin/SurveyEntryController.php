<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\DataTables;

use DB;
use App\Company;
use App\Survey;
use App\SurveyEntry;
use App\SurveyEntryEsg;
use App\SurveyEntryStakeholder;
use App\DataTables\SurveyReportDataTable;

class SurveyEntryController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:admin');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.survey_entry.index');
    }

    public function getSurveyEntries()
    {
        $survey_entries = SurveyEntry::all();
        return DataTables::of($survey_entries)
            ->editColumn('company_id', function ($survey_entry) {
                $company = Company::select('name')->find($survey_entry->company_id);
                return $company->name;
            })
            ->editColumn('survey_id', function ($survey_entry) {
                $survey = Survey::select('title')->find($survey_entry->survey_id);
                return $survey->title;
            })
            ->addColumn('stakeholder', function($survey_entry){
                $sql = 'SELECT stakeholders.alias_name,survey_entry_stakeholders.stakeholder_comment  
                        FROM survey_entry_stakeholders 
                        JOIN stakeholders ON survey_entry_stakeholders.stakeholder_id=stakeholders.id
                        WHERE survey_entry_id='.$survey_entry->id;
                $stakeholder = DB::select($sql);
                $str = $stakeholder[0]->alias_name;
                if($stakeholder[0]->stakeholder_comment!=''){
                    $str .= ' - '.$stakeholder[0]->stakeholder_comment;
                }
                return $str;
            })
            ->addColumn('esg', function($survey_entry){
                $sql = 'SELECT esgs.alias_name, survey_entry_esgs.esg_value, survey_entry_esgs.survey_type
                        FROM survey_entry_esgs
                        JOIN esgs ON survey_entry_esgs.esg_id=esgs.id
                        WHERE survey_entry_id='.$survey_entry->id;
                $esgs = DB::select($sql);
                $str = '<ul>';
                foreach ($esgs as $esg) {
                    $str .= '<li>'.$esg->alias_name.': '.$esg->esg_value.' for '.$esg->survey_type.'</li>';
                }
                $str .= '</ul>';
                
                return $str;
            })
            ->rawColumns(['esg'])
            ->make(true);
    }

    public function allCompaniesSurveyReport(){

        $industries = Company::orderBy('industry_type', 'asc')->groupBy('industry_type')->get();
        return view('admin.survey_entry.all-companies-survey', compact('industries'));
    }

    public function allCompaniesFilter(Request $request){

        $companies = array();
        $years = array();
        $months = array();
        $stakeholder_groups = array();
        $result1 = array();
        $result2 = array();
        $result = '';
        if($request->has('industry')){
            $companies = DB::table('companies')->select('companies.*')
                ->join('survey_entries', 'survey_entries.company_id', '=', 'companies.id')
                ->where('industry_type',request()->industry)
                ->groupBy('companies.id')
                ->get();
            $company_ids = $companies->implode('id',',');
            
            $surveys = DB::table('surveys')->select('surveys.id','surveys.fiscal_entry')
                ->join('survey_entries', 'surveys.id', '=', 'survey_entries.survey_id');
            if(request()->company!=''){
                $surveys = $surveys->where('surveys.company_id', request()->company);   
            }else{
                $surveys = $surveys->whereIn('surveys.company_id',explode(',', $company_ids));
            }
            $surveys = $surveys->get();
            
            $survey_ids = '';
            foreach ($surveys as $key => $value) {
                $year_month = explode(' / ', $value->fiscal_entry);
                $years[] = $year_month[0];
                if(request()->fiscal_year!=''){
                    if(request()->fiscal_year == $year_month[0]){
                        $months[] = $year_month[1];
                        if(request()->fiscal_month!=''){
                            if(request()->fiscal_month == $year_month[1]){
                                $survey_ids .= $value->id.',';
                            }
                        }else{
                            $survey_ids .= $value->id.',';
                        }
                    }
                }else{
                    $months[] = $year_month[1];
                    $survey_ids .= $value->id.',';
                }
            }
            $survey_ids = substr($survey_ids,0,-1);
            
            $stakeholders = DB::table('stakeholders')->select('stakeholders.id','stakeholders.survey_choice')
                ->join('survey_stakeholders','survey_stakeholders.stakeholder_id','=','stakeholders.id');
            $stakeholders = $stakeholders->whereIn('survey_id',explode(',', $survey_ids));
            $stakeholders = $stakeholders->get();
            $stakeholder_ids = $stakeholders->implode('id',',');

            if(!$stakeholders->isEmpty()){
                $stakeholder_groups = $stakeholders->implode('survey_choice',',');
                $stakeholder_groups = array_values(array_unique(explode(',', $stakeholder_groups)));
            }
            
            $isSurvey = DB::table('survey_entry_stakeholders');
            $isSurvey = $isSurvey->whereIn('stakeholder_id',explode(',', $stakeholder_ids));
            $isSurvey = $isSurvey->exists();
            
            $years = array_values(array_unique($years));
            $months = array_values(array_unique($months));

            if(request()->search!=''){

                $survey_choice = $request->survey_choice;
                $esgs = DB::table('esgs')
                    ->select('esgs.*')
                    ->join('survey_esgs', 'esgs.id', '=', 'survey_esgs.esg_id')
                    ->whereIn('survey_esgs.survey_id', explode(',', $survey_ids))
                    ->groupBy('esgs.id')
                    ->get();
                foreach ($esgs as $esg){
                    $survey_entry_esgs1 = DB::table('survey_entries')
                        ->select(
                            DB::raw("SUM(survey_entry_esgs.esg_value = 1) as one"),
                            DB::raw("SUM(survey_entry_esgs.esg_value = 2) as two"),
                            DB::raw("SUM(survey_entry_esgs.esg_value = 3) as three"),
                            DB::raw("SUM(survey_entry_esgs.esg_value = 4) as four"),
                            DB::raw("SUM(survey_entry_esgs.esg_value = 5) as five"),
                            DB::raw("COUNT(survey_entry_esgs.id) as total_esg")
                        )
                        ->join('survey_entry_esgs', 'survey_entry_esgs.survey_entry_id', '=', 'survey_entries.id')
                        ->where('survey_entry_esgs.esg_id', $esg->id)
                        ->whereIn('survey_entries.survey_id', explode(',', $survey_ids))
                        ->where('survey_entry_esgs.survey_type', 'stakeholder')
                        ->get();

                    $one1 = 1 * $survey_entry_esgs1[0]->one;  
                    $two1 = 2 * $survey_entry_esgs1[0]->two;
                    $three1 = 3 * $survey_entry_esgs1[0]->three;
                    $four1 = 4 * $survey_entry_esgs1[0]->four;
                    $five1 = 5 * $survey_entry_esgs1[0]->five;
                    $toatal_esg_value1 = $one1 + $two1 + $three1 + $four1 + $five1;
                    if($survey_entry_esgs1[0]->total_esg!=0){
                        $average = $toatal_esg_value1 / $survey_entry_esgs1[0]->total_esg;
                        if(is_int($average)){
                            $average = $average.'.0';
                        }else{
                            $average = round($average, 1);
                        }
                        $see = array_merge((array)$esg, array('average' => $average));
                        array_push($result1, $see);
                    }
        
                    $survey_entry_esgs2 = DB::table('survey_entries')
                        ->select(
                            DB::raw("SUM(survey_entry_esgs.esg_value = 1) as one"),
                            DB::raw("SUM(survey_entry_esgs.esg_value = 2) as two"),
                            DB::raw("SUM(survey_entry_esgs.esg_value = 3) as three"),
                            DB::raw("SUM(survey_entry_esgs.esg_value = 4) as four"),
                            DB::raw("SUM(survey_entry_esgs.esg_value = 5) as five"),
                            DB::raw("COUNT(survey_entry_esgs.id) as total_esg")
                        )
                        ->join('survey_entry_esgs', 'survey_entry_esgs.survey_entry_id', '=', 'survey_entries.id')
                        ->where('survey_entry_esgs.esg_id', $esg->id)
                        ->whereIn('survey_entries.survey_id', explode(',', $survey_ids))
                        ->where('survey_entry_esgs.survey_type', 'company')
                        ->get();
                    
                    $one2 = 1 * $survey_entry_esgs2[0]->one;  
                    $two2 = 2 * $survey_entry_esgs2[0]->two;
                    $three2 = 3 * $survey_entry_esgs2[0]->three;
                    $four2 = 4 * $survey_entry_esgs2[0]->four;
                    $five2 = 5 * $survey_entry_esgs2[0]->five;
                    $toatal_esg_value2 = $one2 + $two2 + $three2 + $four2 + $five2;
                    if($survey_entry_esgs2[0]->total_esg!=0){
                        $average = $toatal_esg_value2 / $survey_entry_esgs2[0]->total_esg;
                        if(is_int($average)){
                            $average = $average.'.0';
                        }else{
                            $average = round($average, 1);
                        }
                        $see = array_merge((array)$esg, array('average' => $average));
                        array_push($result2, $see);
                    }
                }
                
                $result1 = array_reverse(array_sort($result1, function ($value) {
                    return $value['average'];
                }));
                $result2 = array_reverse(array_sort($result2, function ($value) {
                    return $value['average'];
                }));
                $result = view('admin.survey_entry.all-companies-survey-report', compact('survey_choice','result1','result2'))->render();
            }
        }

        return response()->json(array('companies' => $companies, 'years' => $years, 'months' => $months, 'stakeholder_groups' => $stakeholder_groups, 'isSurvey' => $isSurvey, 'result' => $result));
    }

    public function individualCompanySurveyReport(){

        $companies = DB::table('companies')->select('companies.*')
                ->join('survey_entries', 'survey_entries.company_id', '=', 'companies.id')
                ->groupBy('companies.id')
                ->get();
        return view('admin.survey_entry.individual-company-survey', compact('companies'));
    }

    public function individualCompanyFilter(Request $request){

        $years = array();
        $months = array();
        $stakeholder_groups = array();
        $result1 = array();
        $result2 = array();
        $result3 = array();
        $survey_detail = array();
        $result = '';
        if($request->has('company')){
            
            $surveys = DB::table('surveys')->select('surveys.id','surveys.title','surveys.fiscal_entry')
                ->join('survey_entries', 'surveys.id', '=', 'survey_entries.survey_id')
                ->where('surveys.company_id', request()->company)
                ->get();
            
            $survey_ids = array();
            foreach ($surveys as $key => $value) {
                $year_month = explode(' / ', $value->fiscal_entry);
                $years[] = $year_month[0];
                if(request()->fiscal_year!=''){
                    if(request()->fiscal_year == $year_month[0]){
                        $months[] = $year_month[1];
                        if(request()->fiscal_month!=''){
                            if(request()->fiscal_month == $year_month[1]){
                                $survey_ids[] = $value->id;
                            }
                        }else{
                            $survey_ids[] = $value->id;
                        }
                    }
                }else{
                    $months[] = $year_month[1];
                    $survey_ids[] = $value->id;
                }
            }
            $survey_ids = array_values(array_unique($survey_ids));

            $stakeholders = DB::table('stakeholders')->select('stakeholders.id','stakeholders.alias_name','stakeholders.textbox_support_required','stakeholders.survey_choice')
                ->join('survey_stakeholders','survey_stakeholders.stakeholder_id','=','stakeholders.id')
                ->whereIn('survey_id', $survey_ids)
                ->groupBy('stakeholders.id')
                ->get();
            $stakeholder_ids = $stakeholders->implode('id',',');

            if(!$stakeholders->isEmpty()){
                $stakeholder_groups = $stakeholders->implode('survey_choice',',');
                $stakeholder_groups = array_values(array_unique(explode(',', $stakeholder_groups)));
            }
            
            $isSurvey = DB::table('survey_entry_stakeholders');
            $isSurvey = $isSurvey->whereIn('stakeholder_id',explode(',', $stakeholder_ids));
            $isSurvey = $isSurvey->exists();
            
            $years = array_values(array_unique($years));
            $months = array_values(array_unique($months));

            if(request()->search!=''){
                
                $survey_choice = $request->survey_choice;
                $esgs = DB::table('esgs')
                    ->select('esgs.*')
                    ->join('survey_esgs', 'esgs.id', '=', 'survey_esgs.esg_id')
                    ->whereIn('survey_esgs.survey_id', $survey_ids)
                    ->groupBy('esgs.id')
                    ->get();
                foreach ($esgs as $esg){
                    $survey_entry_esgs1 = DB::table('survey_entries')
                        ->select(
                            DB::raw("SUM(survey_entry_esgs.esg_value = 1) as one"),
                            DB::raw("SUM(survey_entry_esgs.esg_value = 2) as two"),
                            DB::raw("SUM(survey_entry_esgs.esg_value = 3) as three"),
                            DB::raw("SUM(survey_entry_esgs.esg_value = 4) as four"),
                            DB::raw("SUM(survey_entry_esgs.esg_value = 5) as five"),
                            DB::raw("COUNT(survey_entry_esgs.id) as total_esg")
                        )
                        ->join('survey_entry_esgs', 'survey_entry_esgs.survey_entry_id', '=', 'survey_entries.id')
                        ->where('survey_entry_esgs.esg_id', $esg->id)
                        ->whereIn('survey_entries.survey_id', $survey_ids)
                        ->where('survey_entry_esgs.survey_type', 'stakeholder')
                        ->get();

                    $one1 = 1 * $survey_entry_esgs1[0]->one;  
                    $two1 = 2 * $survey_entry_esgs1[0]->two;
                    $three1 = 3 * $survey_entry_esgs1[0]->three;
                    $four1 = 4 * $survey_entry_esgs1[0]->four;
                    $five1 = 5 * $survey_entry_esgs1[0]->five;
                    $toatal_esg_value1 = $one1 + $two1 + $three1 + $four1 + $five1;
                    if($survey_entry_esgs1[0]->total_esg!=0){
                        $average = $toatal_esg_value1 / $survey_entry_esgs1[0]->total_esg;
                        if(is_int($average)){
                            $average = $average.'.0';
                        }else{
                            $average = round($average, 1);
                        }
                        $see = array_merge((array)$esg, array('average' => $average));
                        array_push($result1, $see);
                    }
        
                    $survey_entry_esgs2 = DB::table('survey_entries')
                        ->select(
                            DB::raw("SUM(survey_entry_esgs.esg_value = 1) as one"),
                            DB::raw("SUM(survey_entry_esgs.esg_value = 2) as two"),
                            DB::raw("SUM(survey_entry_esgs.esg_value = 3) as three"),
                            DB::raw("SUM(survey_entry_esgs.esg_value = 4) as four"),
                            DB::raw("SUM(survey_entry_esgs.esg_value = 5) as five"),
                            DB::raw("COUNT(survey_entry_esgs.id) as total_esg")
                        )
                        ->join('survey_entry_esgs', 'survey_entry_esgs.survey_entry_id', '=', 'survey_entries.id')
                        ->where('survey_entry_esgs.esg_id', $esg->id)
                        ->whereIn('survey_entries.survey_id', $survey_ids)
                        ->where('survey_entry_esgs.survey_type', 'company')
                        ->get();
                    
                    $one2 = 1 * $survey_entry_esgs2[0]->one;  
                    $two2 = 2 * $survey_entry_esgs2[0]->two;
                    $three2 = 3 * $survey_entry_esgs2[0]->three;
                    $four2 = 4 * $survey_entry_esgs2[0]->four;
                    $five2 = 5 * $survey_entry_esgs2[0]->five;
                    $toatal_esg_value2 = $one2 + $two2 + $three2 + $four2 + $five2;
                    if($survey_entry_esgs2[0]->total_esg!=0){
                        $average = $toatal_esg_value2 / $survey_entry_esgs2[0]->total_esg;
                        if(is_int($average)){
                            $average = $average.'.0';
                        }else{
                            $average = round($average, 1);
                        }
                        $see = array_merge((array)$esg, array('average' => $average));
                        array_push($result2, $see);
                    }
                    
                }

                $result1 = array_reverse(array_sort($result1, function ($value) {
                    return $value['average'];
                }));
                $result2 = array_reverse(array_sort($result2, function ($value) {
                    return $value['average'];
                }));
                
                $total_completed_survey = 0;
                foreach ($stakeholders as $stakeholder){
                    $survey_entry_stakeholders = DB::table('survey_entries')
                            ->select(DB::raw("COUNT(survey_entry_stakeholders.id) as total_stakeholder"),'survey_entries.survey_id')
                            ->join('survey_entry_stakeholders', 'survey_entry_stakeholders.survey_entry_id', '=', 'survey_entries.id')
                            ->where('survey_entry_stakeholders.stakeholder_id', $stakeholder->id)
                            ->whereIn('survey_entries.survey_id', $survey_ids)
                            ->get();

                    $sesc = DB::table('survey_entries')
                            ->select(DB::raw("COUNT(survey_entry_stakeholders.id) as total_comment"))
                            ->join('survey_entry_stakeholders', 'survey_entry_stakeholders.survey_entry_id', '=', 'survey_entries.id')
                            ->where('survey_entry_stakeholders.stakeholder_id', $stakeholder->id)
                            ->where('survey_entry_stakeholders.stakeholder_comment', '<>', "")
                            ->whereIn('survey_entries.survey_id', $survey_ids)
                            ->first();

                    $survey_stakeholders = DB::table('survey_stakeholders')
                            ->select(DB::raw("SUM(sample_size) as sample_size"))
                            ->where('stakeholder_id', $stakeholder->id)
                            ->whereIn('survey_id', $survey_ids)
                            ->first();
                    
                    if($survey_stakeholders->sample_size=='0'){
                        $average = 'NA';
                    }else{
                        $average = $survey_entry_stakeholders[0]->total_stakeholder * 100 / $survey_stakeholders->sample_size;
                        if($average >= 100 ) {
                            $average = '100.00';
                        }else{
                            $average = number_format($survey_entry_stakeholders[0]->total_stakeholder * 100 / $survey_stakeholders->sample_size, 2);    
                        }
                    }                    
                    
                    $ses = array(
                        'id' => $stakeholder->id,
                        'alias_name' => $stakeholder->alias_name,
                        'textbox_support_required' => $stakeholder->textbox_support_required,
                        'sample_size' => $survey_stakeholders->sample_size,
                        'completed_survey' => $survey_entry_stakeholders[0]->total_stakeholder,
                        'average' => $average, 
                        'survey_id' => $survey_entry_stakeholders[0]->survey_id,
                        'total_comment' => $sesc->total_comment,
                    );
                    array_push($result3, $ses);
                    $total_completed_survey = $total_completed_survey + $survey_entry_stakeholders[0]->total_stakeholder;
                }
                
                $survey_id = '';
                $survey_title = '';
                if(count($survey_ids) == 1){
                    
                    $filtered = $surveys->filter(function ($item) use ($survey_ids) {
                        if($item->id == $survey_ids[0]){
                            return (array)$item;
                        }
                    })->values();

                    $survey_id = $survey_ids[0];
                    $survey_title = $filtered[0]->title;
                }
                
                $survey_detail = array_merge($survey_detail,array('id' => $survey_id, 'title' => $survey_title,'total_completed_survey' => $total_completed_survey));
                $result = view('admin.survey_entry.individual-company-survey-report', compact('survey_choice','result1','result2','result3','survey_detail'))->render();
            }
        }

        return response()->json(array('years' => $years, 'months' => $months, 'stakeholder_groups' => $stakeholder_groups, 'isSurvey' => $isSurvey, 'result' => $result));
    }
    
    public function getStakeholderComments($id,$survey_id){

        $sql = 'SELECT survey_entries.email, survey_entry_stakeholders.stakeholder_comment
                FROM survey_entries, survey_entry_stakeholders
                WHERE survey_entries.id = survey_entry_stakeholders.survey_entry_id
                AND survey_entry_stakeholders.stakeholder_id='.$id.'
                AND survey_entries.survey_id='.$survey_id.'
                AND survey_entry_stakeholders.stakeholder_comment <> ""';
        $stakeholder_comments = DB::select($sql);
        return view('admin.survey_entry.stakeholder_comments', compact('stakeholder_comments'));
        /*return response()->json([
            'stakeholder_comments' => view('admin.survey_entry.stakeholder_comments')->with('stakeholder_comments',$stakeholder_comments)->render()
        ]);*/
    }

    public function exportFile($id,$type){
        
        $data = array();
        $results1 = DB::table('companies')
                ->select('survey_entries.email','companies.name','stakeholders.alias_name as stakeholder','survey_entry_stakeholders.stakeholder_comment','survey_entries.id','stakeholders.survey_choice')
                ->join('surveys', 'companies.id', '=', 'surveys.company_id')
                ->join('survey_entries', 'surveys.id', '=', 'survey_entries.survey_id')
                ->join('survey_entry_stakeholders', 'survey_entries.id', '=', 'survey_entry_stakeholders.survey_entry_id')
                ->join('stakeholders', 'survey_entry_stakeholders.stakeholder_id', '=', 'stakeholders.id')
                ->where('surveys.id', $id)
                ->get();
        
        $results2 = DB::table('survey_esgs')
                ->select('esgs.alias_name as esg', 'esgs.id as esg_id')
                ->join('esgs', 'survey_esgs.esg_id', '=', 'esgs.id')
                ->where('survey_esgs.survey_id', $id)
                ->get();
        

        if(!empty($results1)){
            foreach ($results1 as $key1 => $value1) {
                $data1 = array(
                                'Email' => $value1->email,
                                'Company' => $value1->name,
                                'Stakeholder' => $value1->stakeholder,
                            );
                if(!empty($results2)){
                    foreach ($results2 as $key2 => $value2) {

                        $results3 = DB::table('survey_entry_esgs')
                            ->select('survey_entry_esgs.esg_value')
                            ->where('survey_entry_esgs.esg_id', $value2->esg_id)
                            ->where('survey_entry_esgs.survey_entry_id', $value1->id)
                            ->where('survey_entry_esgs.survey_type', 'stakeholder')
                            ->first();
                        $esg_value = (!empty($results3) ? $results3->esg_value : '');
                        $data1 = array_add($data1, 'ESG - '.$value2->esg.' (stakeholder)', $esg_value);
                    }
                    foreach ($results2 as $key2 => $value2) {

                        $results3 = DB::table('survey_entry_esgs')
                            ->select('survey_entry_esgs.esg_value')
                            ->where('survey_entry_esgs.esg_id', $value2->esg_id)
                            ->where('survey_entry_esgs.survey_entry_id', $value1->id)
                            ->where('survey_entry_esgs.survey_type', 'company')
                            ->first();
                        $esg_value = (!empty($results3) ? $results3->esg_value : '');
                        $data1 = array_add($data1, 'ESG - '.$value2->esg.' (company)', $esg_value);
                    }
                }
                $data[] = $data1;                
            }            
        }
        //echo '<pre>'; print_r($data);exit();
        
        return \Excel::create('survey-report-'.date('Y-m-d'), function($excel) use ($data) {
            $excel->sheet('survey-report-'.date('Y-m-d'), function($sheet) use ($data)
            {
                $sheet->fromArray($data);
            });
        })->download($type);
    }

    public function surveyReport(SurveyReportDataTable $dataTable){

        return $dataTable->render('admin.survey_entry.survey-report');
    }

}
