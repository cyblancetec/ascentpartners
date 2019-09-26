<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\DataTables;
use App\EmailTemplate;
use App\EmailTemplateTranslation;
use App\language;
use Common;
use Validator;

class EmailTemplateController extends Controller
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
    	//$emailTemplates = emailTemplate::paginate(1);
        //return view('admin.emailTemplate.index', compact('emailTemplates'));
        return view('admin.emailTemplate.index');
    }

    public function getEmailTemplates()
    {
        $emailTemplates = EmailTemplate::all();
        return DataTables::of($emailTemplates)
            ->editColumn('alias_name', function ($emailTemplate) {
                $templateName = Common::getEmailTemplateName($emailTemplate->alias_name);
                return $templateName;
            })
            ->addColumn('action', function($emailTemplate){				
				return '<a href="'.action('admin\EmailTemplateController@edit', $emailTemplate['id']).'"><i class="fa fa-edit"></i></a>';
					
                /*$msg = "'Do you want to delete this email template?'";
                return '<a href="'.action('admin\EmailTemplateController@edit', $emailTemplate['id']).'"><i class="fa fa-edit"></i></a>
                        <form onsubmit="return confirm('.$msg.');" style="display: inline-block;" action="'.action('admin\EmailTemplateController@destroy', $emailTemplate['id']).'" method="post">
                           '.csrf_field().'
                            <input name="_method" type="hidden" value="DELETE">
                            <button type="submit"><i class="fa fa-trash"></i></button>
                        </form>';*/
            })->make(true);
    }
		
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $templateNames = Common::emailTemplateNames();
        return view('admin.emailTemplate.create',array('templateNames' => $templateNames));
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
        $rules['alias_name']='required|string';        
        foreach ($languages as $language) {
            $fields['subject_'.$language->alias_name]=request()->get('subject_'.$language->alias_name);
            $rules['subject_'.$language->alias_name]='required|string';
            $messages['subject_'.$language->alias_name.'.required']='The '.$language->name.' subject field is required';
            $fields['content_'.$language->alias_name]=request()->get('content_'.$language->alias_name);
            $rules['content_'.$language->alias_name]='required|string';
            $messages['content_'.$language->alias_name.'.required']='The '.$language->name.' content field is required';            
        }

        $validator = Validator::make($fields, $rules, $messages);
        if ($validator->fails()) {
            return redirect('admin/email-templates/create')->withInput()->withErrors($validator);
        } else {
            $input = request()->all();
            //print_r($input);exit();
            $data = array('alias_name' => $input['alias_name']);
            $esg = EmailTemplate::create($data);
            foreach ($languages as $language) {
                $tdata = array(
                        'email_template_id' => $esg->id,
                        'locale' => $language->alias_name,
                        'subject' => $input['subject_'.$language->alias_name],
                        'content' => $input['content_'.$language->alias_name],                        
                    );
                EmailTemplateTranslation::create($tdata);
            }
            return redirect('admin/email-templates')->with('success','Email Template has been created.');
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
        $templateNames = Common::emailTemplateNames();
        $emailTemplate = EmailTemplate::findOrFail($id);
        return view('admin.emailTemplate.edit', compact('emailTemplate'),array('templateNames' => $templateNames));
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
        //$fields['alias_name']=request()->get('alias_name');
        //$rules['alias_name']='required|string';
        
        foreach ($languages as $language) {
            $fields['subject_'.$language->alias_name]=request()->get('subject_'.$language->alias_name);
            $rules['subject_'.$language->alias_name]='required|string';
            $messages['subject_'.$language->alias_name.'.required']='The '.$language->name.' subject field is required';
            $fields['content_'.$language->alias_name]=request()->get('content_'.$language->alias_name);
            $rules['content_'.$language->alias_name]='required|string';
            $messages['content_'.$language->alias_name.'.required']='The '.$language->name.' content field is required';            
        }

        $validator = Validator::make($fields, $rules, $messages);
        if ($validator->fails()) {
            return redirect('admin/email-templates/'.$id.'/edit')->withInput()->withErrors($validator);
        } else {
            $input = request()->all();
            //print_r($input);exit();
            //$data = array('alias_name' => $input['alias_name']);
            //EmailTemplate::find($id)->update($data);
           
            foreach ($languages as $language) {
                $tid = $input['tid_'.$language->alias_name];
                $tdata = array(
                        'email_template_id' => $id,
                        'locale' => $language->alias_name,
                        'subject' => $input['subject_'.$language->alias_name],
                        'content' => $input['content_'.$language->alias_name],                        
                    );
                EmailTemplateTranslation::find($tid)->update($tdata);
            }
            return redirect('admin/email-templates')->with('success','Email Template has been updated.');
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
        $emailTemplate = EmailTemplate::findOrFail($id);
        $emailTemplate->delete();
        EmailTemplateTranslation::where('email_template_id', $id)->delete();
        return redirect('admin/email-templates')->with('success','Email Template has been deleted.');
    }
}
