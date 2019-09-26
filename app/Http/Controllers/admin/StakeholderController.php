<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\DataTables;
use App\Stakeholder;
use App\StakeholderTranslation;
use App\language;
use Validator;
use App\SurveyStakeholder;

class StakeholderController extends Controller
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
    	//$stakeholders = Stakeholder::paginate(1);
        //return view('admin.stakeholder.index', compact('stakeholders'));
        return view('admin.stakeholder.index');
    }

    public function getStakeholdes()
    {
        $stakeholders = Stakeholder::all();
        return DataTables::of($stakeholders)
            ->addColumn('action', function($stakeholder){
                if (SurveyStakeholder::where('stakeholder_id', '=', $stakeholder->id)->exists()) {
                    $msg = "'Delete operation failed since it has associated record(s).'";
                    return '<a href="'.action('admin\StakeholderController@edit', $stakeholder['id']).'"><i class="fa fa-edit"></i></a>
                            <button onclick="return alert('.$msg.');" type="button"><i class="fa fa-trash"></i></button>';
                }else{
                    $msg = "'Do you want to delete this stakeholder?'";
                    return '<a href="'.action('admin\StakeholderController@edit', $stakeholder['id']).'"><i class="fa fa-edit"></i></a>
                            <form onsubmit="return confirm('.$msg.');" style="display: inline-block;" action="'.action('admin\StakeholderController@destroy', $stakeholder['id']).'" method="post">
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
        return view('admin.stakeholder.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $languages = Language::all();
        $messages = array();
        $fields['alias_name']=request()->get('alias_name');
        $rules['alias_name']='required|string|unique:stakeholders,alias_name';
        $messages['alias_name.required']='The stakeholder field is required.';
        $fields['textbox_support_required']=request()->get('textbox_support_required');
        $rules['textbox_support_required']='required|string';
        foreach ($languages as $language) {
            $alias_name = str_replace('-','_', strtolower($language->alias_name));
            $fields['title_'.$language->alias_name]=request()->get('title_'.$language->alias_name);
            $rules['title_'.$language->alias_name]='required|string|unique_stakeholder_title_'.$alias_name;
            $messages['title_'.$language->alias_name.'.required']='The '.$language->name.' title field is required.';
            $messages['title_'.$language->alias_name.'.unique_stakeholder_title_'.$alias_name]='The '.$language->name.' title has already been taken.';
        }

        $validator = Validator::make($fields, $rules, $messages);
        if ($validator->fails()) {
            return redirect('admin/stakeholders/create')->withInput()->withErrors($validator);
        } else {
            $input = request()->all();
            //print_r($input);exit();
            $data = array(
                    'alias_name' => $input['alias_name'],
                    'textbox_support_required' => $input['textbox_support_required'],
                    'survey_choice' => $input['survey_choice'],
                );
            $stakeholder = Stakeholder::create($data);
            foreach ($languages as $language) {
                $tdata = array(
                        'stakeholder_id' => $stakeholder->id,
                        'locale' => $language->alias_name,
                        'title' => $input['title_'.$language->alias_name],
                    );
                StakeholderTranslation::create($tdata);
            }
            return redirect('admin/stakeholders')->with('success','Stakeholder has been inserted.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $stakeholder = Stakeholder::findOrFail($id);
        return view('admin.stakeholder.edit', compact('stakeholder'));
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
        $languages = Language::all();
        $messages = array();
        $fields['alias_name']=request()->get('alias_name');
        $rules['alias_name']='required|string|unique:stakeholders,alias_name,'.$id;
        $messages['alias_name.required']='The stakeholder field is required.';
        $fields['textbox_support_required']=request()->get('textbox_support_required');
        $rules['textbox_support_required']='required|string';
        foreach ($languages as $language) {
            $alias_name = str_replace('-','_', strtolower($language->alias_name));
            $fields['tid_'.$language->alias_name]=request()->get('tid_'.$language->alias_name);
            $fields['title_'.$language->alias_name]=request()->get('title_'.$language->alias_name);
            $rules['title_'.$language->alias_name]='required|string|unique_stakeholder_title_'.$alias_name;
            $messages['title_'.$language->alias_name.'.required']='The '.$language->name.' title field is required';
            $messages['title_'.$language->alias_name.'.unique_stakeholder_title_'.$alias_name]='The '.$language->name.' title has already been taken.';
        }

        $validator = Validator::make($fields, $rules, $messages);
        if ($validator->fails()) {
            return redirect('admin/stakeholders/'.$id.'/edit')->withInput()->withErrors($validator);
        } else {
            $input = request()->all();
            //print_r($input);exit();
            $data = array(
                    'alias_name' => $input['alias_name'],
                    'textbox_support_required' => $input['textbox_support_required'],
                    'survey_choice' => $input['survey_choice'],
                );
            Stakeholder::find($id)->update($data);
            foreach ($languages as $language) {
                $tid = $input['tid_'.$language->alias_name];
                $tdata = array(
                        'stakeholder_id' => $id,
                        'locale' => $language->alias_name,
                        'title' => $input['title_'.$language->alias_name],
                    );
                StakeholderTranslation::find($tid)->update($tdata);
            }
            return redirect('admin/stakeholders')->with('success','Stakeholder has been updated.');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = Stakeholder::findOrFail($id);
        $user->delete();
        StakeholderTranslation::where('stakeholder_id', $id)->delete();
        return redirect('admin/stakeholders')->with('success','Stakeholder has been deleted.');
    }
}
