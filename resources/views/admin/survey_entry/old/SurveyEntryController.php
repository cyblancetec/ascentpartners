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

        $input = request()->all();
        $companies = Company::orderBy('name')->get();
        if(isset($input['industry']) && $input['industry']!=''){
            $company = DB::table('companies')
                        ->select('id')
                        ->where('industry_type',$input['industry']);
            $company_ids = $company->implode('id',',');
        }else{
            $company_ids = $companies->implode('id',',');
        }

        if(isset($input['company']) && $input['company']!=''){
            $company_ids = $input['company'];
        }
       
        $surveys = DB::table('surveys')
                    ->select('id','title')
                    ->whereIn('company_id',explode(',', $company_ids));
        if (request()->has('search')) {
            if(!empty($input)){
                if($input['fiscal_year']!='' && $input['fiscal_month']==''){
                    $surveys = $surveys->where('fiscal_entry','like', '%'.$input['fiscal_year'].'%');
                }else if($input['fiscal_month']!='' && $input['fiscal_year']==''){
                    $surveys = $surveys->where('fiscal_entry','like', '%'.$input['fiscal_month'].'%');
                }else if($input['fiscal_year']!='' && $input['fiscal_month']!=''){
                    $fiscal_entry = $input['fiscal_year'].' / '.$input['fiscal_month'];
                    $surveys = $surveys->where('fiscal_entry',$fiscal_entry);
                }
            }
        }

        $surveys = $surveys->get();

        $survey_ids = $surveys->implode('id',',');
        $result1 = array();
        $result2 = array();
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
                //$average = number_format($toatal_esg_value1 / $survey_entry_esgs1[0]->total_esg);
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
                //$average = number_format($toatal_esg_value2 / $survey_entry_esgs2[0]->total_esg);
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

        if (request()->has('campanyFltr')) {
            $input['company'] = request()->campanyFltr;
            $fiscal_entry = DB::table('surveys')
                            ->select('fiscal_entry')
                            ->where('company_id', request()->campanyFltr)
                            ->get();
        }else{
            $fiscal_entry = DB::table('surveys')->select('fiscal_entry')->get();
        }

        $years = array();
        $months = array();
        foreach ($fiscal_entry as $key => $value) {
            $year_month = explode(' / ', $value->fiscal_entry);
            $years[] = $year_month[0];
            $months[] = $year_month[1];
        }
        $years = array_unique($years);
        $months = array_unique($months);

        return view('admin.survey_entry.all-companies-survey-report', compact('input','companies','result1','result2','years','months'));
    }

    public function individualCompanySurveyReport(Request $request){

        $companies = Company::orderBy('name')->get();
        if ($request->has('search')) {
            $this->validate(request(), [
                'company' => 'required',
                'year' => 'required',
                'month' =>  'required',
            ]);

            $input = request()->all();
            $fiscal_entry = $input['year'].' / '.$input['month'];

            $survey = Survey::select('id','title')
                        ->where('company_id',$input['company'])
                        ->where('fiscal_entry',$fiscal_entry)
                        ->first();
            
            $result1 = array();
            $result2 = array();
            $result3 = array();
            $survey_detail = array();
            if(!empty($survey)){

                $esgs = DB::table('esgs')
                        ->select('esgs.*')
                        ->join('survey_esgs', 'esgs.id', '=', 'survey_esgs.esg_id')
                        ->where('survey_esgs.survey_id', $survey->id)
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
                        ->where('survey_entries.survey_id', $survey->id)
                        ->where('survey_entry_esgs.survey_type', 'stakeholder')
                        ->get();
                    
                    $one1 = 1 * $survey_entry_esgs1[0]->one;  
                    $two1 = 2 * $survey_entry_esgs1[0]->two;
                    $three1 = 3 * $survey_entry_esgs1[0]->three;
                    $four1 = 4 * $survey_entry_esgs1[0]->four;
                    $five1 = 5 * $survey_entry_esgs1[0]->five;
                    $toatal_esg_value1 = $one1 + $two1 + $three1 + $four1 + $five1;
                    if($survey_entry_esgs1[0]->total_esg!=0){
                        //$average = number_format($toatal_esg_value1 / $survey_entry_esgs1[0]->total_esg);
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
                        ->where('survey_entries.survey_id', $survey->id)
                        ->where('survey_entry_esgs.survey_type', 'company')
                        ->get();
                    
                    $one2 = 1 * $survey_entry_esgs2[0]->one;  
                    $two2 = 2 * $survey_entry_esgs2[0]->two;
                    $three2 = 3 * $survey_entry_esgs2[0]->three;
                    $four2 = 4 * $survey_entry_esgs2[0]->four;
                    $five2 = 5 * $survey_entry_esgs2[0]->five;
                    $toatal_esg_value2 = $one2 + $two2 + $three2 + $four2 + $five2;
                    if($survey_entry_esgs2[0]->total_esg!=0){
                        //$average = number_format($toatal_esg_value2 / $survey_entry_esgs2[0]->total_esg);
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
                $stakeholders = DB::table('stakeholders')
                        ->select('stakeholders.*', DB::raw("SUM(survey_stakeholders.sample_size) as sample_size"))
                        ->join('survey_stakeholders', 'stakeholders.id', '=', 'survey_stakeholders.stakeholder_id')
                        ->where('survey_stakeholders.survey_id', $survey->id)
                        ->groupBy('stakeholders.id');
                if(isset($input['survey_choice']) && $input['survey_choice']!=''){
                    $stakeholders = $stakeholders->where('stakeholders.survey_choice', $input['survey_choice']);
                }
                $stakeholders = $stakeholders->get();
                foreach ($stakeholders as $stakeholder){
                    $survey_entry_stakeholders = DB::table('survey_entries')
                            ->select(DB::raw("COUNT(survey_entry_stakeholders.id) as total_stakeholder"))
                            ->join('survey_entry_stakeholders', 'survey_entry_stakeholders.survey_entry_id', '=', 'survey_entries.id')
                            ->where('survey_entry_stakeholders.stakeholder_id', $stakeholder->id)
                            ->where('survey_entries.survey_id', $survey->id)
                            ->get();
                    $sesc = DB::table('survey_entries')
                            ->select(DB::raw("COUNT(survey_entry_stakeholders.id) as total_comment"))
                            ->join('survey_entry_stakeholders', 'survey_entry_stakeholders.survey_entry_id', '=', 'survey_entries.id')
                            ->where('survey_entry_stakeholders.stakeholder_id', $stakeholder->id)
                            ->where('survey_entries.survey_id', $survey->id)
                            ->where('survey_entry_stakeholders.stakeholder_comment', '<>', null)
                            ->first();
                    $average = number_format($survey_entry_stakeholders[0]->total_stakeholder * 100 / $stakeholder->sample_size, 2);
                    $ses = array_merge((array)$stakeholder, array('completed_survey' => $survey_entry_stakeholders[0]->total_stakeholder, 'average' => $average, 'total_comment' => $sesc->total_comment));
                    array_push($result3, $ses);
                    $total_completed_survey = $total_completed_survey + $survey_entry_stakeholders[0]->total_stakeholder;
                }
                $survey_detail = array_merge($survey_detail,array('id' => $survey->id, 'title' => $survey->title,'total_completed_survey' => $total_completed_survey));
            }
        }

        if ($request->has('campanyFltr')) {
            $input['company'] = request()->campanyFltr;
            $company_id = request()->campanyFltr;
        }else{
            $company_first = json_decode(array_first($companies));
            $company_id = $company_first->id;
        }

        $years = array();
        $months = array();
        $fiscal_entry = DB::table('surveys')
                            ->select('fiscal_entry')
                            ->where('company_id', $company_id)
                            ->get();
        foreach ($fiscal_entry as $key => $value) {
            $year_month = explode(' / ', $value->fiscal_entry);
            $years[] = $year_month[0];
            $months[] = $year_month[1];
        }
        $years = array_unique($years);
        $months = array_unique($months);

        return view('admin.survey_entry.individual-company-survey-report', compact('input','companies','survey_detail','result1','result2','result3','years','months'));
    }

    public function getStakeholderComments($id){

        $sql = 'SELECT survey_entries.email, survey_entry_stakeholders.stakeholder_comment
                FROM survey_entries, survey_entry_stakeholders
                WHERE survey_entries.id = survey_entry_stakeholders.survey_entry_id
                AND survey_entry_stakeholders.stakeholder_id='.$id.'
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
        if(!empty($results1)){
            foreach ($results1 as $key1 => $value1) {
                $data1 = array(
                                'Email' => $value1->email,
                                'Company' => $value1->name,
                                'Stakeholder' => $value1->stakeholder,
                            );
                $results2 = DB::table('survey_entry_esgs')
                    ->select('esgs.alias_name as esg','survey_entry_esgs.survey_type','survey_entry_esgs.esg_value')
                    ->join('esgs', 'survey_entry_esgs.esg_id', '=', 'esgs.id')
                    ->where('survey_entry_esgs.survey_entry_id', $value1->id)
                    ->get();
                if(!empty($results2)){
                    foreach ($results2 as $key2 => $value2) {
                        $data1 = array_add($data1, 'ESG - '.$value2->esg.' ('.$value2->survey_type.')', $value2->esg_value);
                    }
                    if($value1->survey_choice == 'Stakeholder only'){
                        foreach ($results2 as $key2 => $value2) {
                            $data1 = array_add($data1, 'ESG - '.$value2->esg.' (company)', '');
                        }
                    }
                }
                $data[] = $data1;
            }
        }
        //echo '<pre>';print_r($data);exit();

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
