<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use Yajra\DataTables\DataTables;
use App\Survey;
use App\Stakeholder;
use App\Esg;
use App\EsgCategory;
use App\SurveyStakeholder;
use App\SurveyEsg;
use App\Company;
use App\SurveyEntry;

class SurveyController extends Controller
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
        //$surveys = Survey::paginate(1);
        //return view('admin.survey.index', compact('surveys'));
        return view('admin.survey.index');
    }

    public function getSurveys()
    {
        $surveys = Survey::all();
        return DataTables::of($surveys)
            ->editColumn('company_id', function ($survey) {
                $company = Company::select('name')->find($survey->company_id);
                return $company->name;
            })
            ->addColumn('action', function($survey){
                if (SurveyEntry::where('survey_id', '=', $survey->id)->exists()) {
                    $msg = "'Delete operation failed since it has associated record(s).'";
                    return '<a href="'.action('admin\SurveyController@edit', $survey['id']).'"><i class="fa fa-edit"></i></a>
                            <button onclick="return alert('.$msg.');" type="button"><i class="fa fa-trash"></i></button>';
                }else{
                    $msg = "'Do you want to delete this survey?'";
                    return '<a href="'.action('admin\SurveyController@edit', $survey['id']).'"><i class="fa fa-edit"></i></a>
                            <form onsubmit="return confirm('.$msg.');" style="display: inline-block;" action="'.action('admin\SurveyController@destroy', $survey['id']).'" method="post">
                               '.csrf_field().'
                                <input name="_method" type="hidden" value="DELETE">
                                <button type="submit"><i class="fa fa-trash"></i></button>
                            </form>';
                }
            })->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $stakeholders = Stakeholder::all();
        $esgs = Esg::all();
        $esg_categories = EsgCategory::all();
        return view('admin.survey.create', array('stakeholders' => $stakeholders, 'esgs' => $esgs, 'esg_categories' => $esg_categories));
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = request()->all();

        $this->validate(request(), [
            'title' => 'required|string|unique:surveys,title|uniqueSurvey',
            'fiscal_year' => 'required|numeric',
            'fiscal_month' => 'required|string',
            'company_id' => 'required',
            'stakeholder_ids' => 'required',
            'esg_ids' => 'required',
            'expiry_date' => 'required',
        ],[
            'stakeholder_ids.required' => 'The stakeholders field is required.',
            'esg_ids.required' => 'The esg\'s field is required.',
            'company_id.required' => 'The related client (company) field is required.',
            'fiscal_year.required' => 'The fiscal entry year field is required.',
            'fiscal_month.required' => 'The fiscal entry month field is required.',
            'unique_survey' => 'The fiscal entry must be different for the related client or selcted differtent client.',
        ]);
        
        $data1 = array(
            'company_id' => $input['company_id'],
            'title' => $input['title'],
            'fiscal_entry' => $input['fiscal_year'].' / '.$input['fiscal_month'],
            'expiry_date' =>date('Y-m-d', strtotime($input['expiry_date']))
        );
        
        $survey = Survey::create($data1);
        $data2 = array();
        $stakeholder_ids = explode(',', $input['stakeholder_ids']);
        foreach ($stakeholder_ids as $stakeholder_id) {
            $sample_size = $input['stakeholder_size_'.$stakeholder_id];
            if($sample_size=='' || $sample_size <= 0){
                $sample_size = 0;
            }
            $data2 = array(
                'survey_id' => $survey->id,
                'stakeholder_id' => $stakeholder_id,
                'sample_size' => $sample_size,
            );
            SurveyStakeholder::create($data2);
        }
        $data3 = array();
        $esg_ids = explode(',', $input['esg_ids']);
        foreach ($esg_ids as $esg_id) {
            $data3 = array(
                'survey_id' => $survey->id,
                'esg_id' => $esg_id
            );
            SurveyEsg::create($data3);
        }
        
        return redirect('admin/surveys')->with('success','Survey has been created.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $stakeholders = Stakeholder::all();
        $esgs = Esg::all();
        $esg_categories = EsgCategory::all();
        $survey = Survey::findOrFail($id);
        return view('admin.survey.edit', compact('survey'), array('stakeholders' => $stakeholders, 'esgs' => $esgs, 'esg_categories' => $esg_categories) );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate(request(), [
            'title' => 'required|string|unique:surveys,title,'.$id.'|uniqueSurvey',
            'fiscal_year' => 'required|numeric',
            'fiscal_month' => 'required|string',
            'company_id' => 'required',
            'stakeholder_ids' => 'required',
            'esg_ids' => 'required',
            'expiry_date' => 'required',
        ],[
            'stakeholder_ids.required' => 'The stakeholders field is required',
            'esg_ids.required' => 'The esg\'s field is required',
            'company_id.required' => 'The related client (company) field is required.',
            'fiscal_year.required' => 'The fiscal entry year field is required.',
            'fiscal_month.required' => 'The fiscal entry month field is required.',
            'unique_survey' => 'The fiscal entry must be different for the related client or selcted differtent client.',
        ]);
        
        $input = request()->all();
        $data1 = array(
            'company_id' => $input['company_id'],
            'title' => $input['title'],
            'fiscal_entry' => $input['fiscal_year'].' / '.$input['fiscal_month'],
            'expiry_date' =>date('Y-m-d', strtotime($input['expiry_date']))
        );
        Survey::find($id)->update($data1);
        SurveyStakeholder::where('survey_id', $id)->delete();
        SurveyEsg::where('survey_id', $id)->delete();
        $data2 = array();
        $stakeholder_ids = explode(',', $input['stakeholder_ids']);
        foreach ($stakeholder_ids as $stakeholder_id) {
            $sample_size = $input['stakeholder_size_'.$stakeholder_id];
            if($sample_size=='' || $sample_size <= 0){
                $sample_size = 0;
            }
            $data2 = array(
                'survey_id' => $id,
                'stakeholder_id' => $stakeholder_id,
                'sample_size' => $sample_size
            );
            SurveyStakeholder::create($data2);
        }
        $data3 = array();
        $esg_ids = explode(',', $input['esg_ids']);
        foreach ($esg_ids as $esg_id) {
            $data3 = array(
                'survey_id' => $id,
                'esg_id' => $esg_id
            );
            SurveyEsg::create($data3);
        }
        return redirect('admin/surveys')->with('success','Survey has been updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $survey = Survey::findOrFail($id);
        $survey->delete();
        SurveyStakeholder::where('survey_id', $id)->delete();
        SurveyEsg::where('survey_id', $id)->delete();
        return redirect('admin/surveys')->with('success','Survey has been deleted.');
    }
}
