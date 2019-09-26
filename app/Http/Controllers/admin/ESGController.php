<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\DataTables;
use App\Esg;
use App\EsgTranslation;
use App\EsgCategory;
use App\language;
use Validator;
use App\SurveyEsg;

class ESGController extends Controller
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
    	//$esgs = Esg::paginate(1);
        //return view('admin.esg.index', compact('esgs'));
        return view('admin.esg.index');
    }

    public function getEsg()
    {
        $esgs = Esg::all();
        return DataTables::of($esgs)
            ->editColumn('category_id', function ($esg) {
                $category = EsgCategory::select('en_title')->find($esg->category_id);
                return $category->en_title;
            })
            ->addColumn('action', function($esg){
                if (SurveyEsg::where('esg_id', '=', $esg->id)->exists()) {
                    $msg = "'Delete operation failed since it has associated record(s).'";
                    return '<a href="'.action('admin\ESGController@edit', $esg['id']).'"><i class="fa fa-edit"></i></a>
                            <button onclick="return alert('.$msg.');" type="button"><i class="fa fa-trash"></i></button>';
                }else{
                    $msg = "'Do you want to delete this ESG?'";
                    return '<a href="'.action('admin\ESGController@edit', $esg['id']).'"><i class="fa fa-edit"></i></a>
                            <form onsubmit="return confirm('.$msg.');" style="display: inline-block;" action="'.action('admin\ESGController@destroy', $esg['id']).'" method="post">
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
        $esg_categories = EsgCategory::all()->sortBy('en_title');
        return view('admin.esg.create', compact('esg_categories'));
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
        $rules['alias_name']='required|string|unique:esgs,alias_name';
        $messages['alias_name.required']='The ESG Aspect field is required.';
        $fields['category_id']=request()->get('category_id');
        $rules['category_id']='required|string';
        $messages['category_id.required']='The Category field is required.';
        foreach ($languages as $language) {
            $alias_name = str_replace('-','_', strtolower($language->alias_name));
            $fields['title_'.$language->alias_name]=request()->get('title_'.$language->alias_name);
            $rules['title_'.$language->alias_name]='required|string|unique_esg_title_'.$alias_name;
            $messages['title_'.$language->alias_name.'.required']='The '.$language->name.' title field is required.';
            $fields['information_'.$language->alias_name]=request()->get('information_'.$language->alias_name);
            $rules['information_'.$language->alias_name]='required|string';
            $messages['information_'.$language->alias_name.'.required']='The '.$language->name.' information field is required.';
            $messages['title_'.$language->alias_name.'.unique_esg_title_'.$alias_name]='The '.$language->name.' title has already been taken.';
        }

        $validator = Validator::make($fields, $rules, $messages);
        if ($validator->fails()) {
            return redirect('admin/esg/create')->withInput()->withErrors($validator);
        } else {
            $input = request()->all();
            //print_r($input);exit();
            $data = array('alias_name' => $input['alias_name'], 'category_id' => $input['category_id']);
            $esg = Esg::create($data);
            foreach ($languages as $language) {
                $tdata = array(
                        'esg_id' => $esg->id,
                        'locale' => $language->alias_name,
                        'title' => $input['title_'.$language->alias_name],
                        'information' => $input['information_'.$language->alias_name],
                    );
                EsgTranslation::create($tdata);
            }
            return redirect('admin/esg')->with('success','ESG has been inserted.');
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
        $esg = Esg::findOrFail($id);
        $esg_categories = EsgCategory::all()->sortBy('en_title');
        return view('admin.esg.edit', compact('esg','esg_categories'));
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
        $rules['alias_name']='required|string|unique:esgs,alias_name,'.$id;
        $messages['alias_name.required']='The ESG Aspect field is required.';
        $fields['category_id']=request()->get('category_id');
        $rules['category_id']='required|string';
        $messages['category_id.required']='The Category field is required.';
        foreach ($languages as $language) {
            $alias_name = str_replace('-','_', strtolower($language->alias_name));
            $fields['tid_'.$language->alias_name]=request()->get('tid_'.$language->alias_name);
            $fields['title_'.$language->alias_name]=request()->get('title_'.$language->alias_name);
            $rules['title_'.$language->alias_name]='required|string|unique_esg_title_'.$alias_name;
            $messages['title_'.$language->alias_name.'.required']='The '.$language->name.' title field is required.';
            $fields['information_'.$language->alias_name]=request()->get('information_'.$language->alias_name);
            $rules['information_'.$language->alias_name]='required|string';
            $messages['information_'.$language->alias_name.'.required']='The '.$language->name.' information field is required.';
            $messages['title_'.$language->alias_name.'.unique_esg_title_'.$alias_name]='The '.$language->name.' title has already been taken.';
        }

        $validator = Validator::make($fields, $rules, $messages);
        if ($validator->fails()) {
            return redirect('admin/esg/'.$id.'/edit')->withInput()->withErrors($validator);
        } else {
            $input = request()->all();
            //print_r($input);exit();
            $data = array('alias_name' => $input['alias_name'], 'category_id' => $input['category_id']);
            Esg::find($id)->update($data);
            foreach ($languages as $language) {
                $tid = $input['tid_'.$language->alias_name];
                $tdata = array(
                        'esg_id' => $id,
                        'locale' => $language->alias_name,
                        'title' => $input['title_'.$language->alias_name],
                        'information' => $input['information_'.$language->alias_name],
                    );
                EsgTranslation::find($tid)->update($tdata);
            }
            return redirect('admin/esg')->with('success','ESG has been updated.');
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
        $user = Esg::findOrFail($id);
        $user->delete();
        EsgTranslation::where('esg_id', $id)->delete();
        return redirect('admin/esg')->with('success','ESG has been deleted.');
    }
}
