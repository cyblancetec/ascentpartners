<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\DataTables;
use App\Admin;
use App\EmailTemplate;
use App\EmailTemplateTranslation;
use App\Language;
use Mail;

class AdminController extends Controller
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
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboard()
    {
        return view('admin.home');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    	//$admins = Admin::paginate(1);
        //return view('admin.admin.index', compact('admins'));
        return view('admin.admin.index');
    }

    public function getAdmins()
    {
        $admins = Admin::all();
        return DataTables::of($admins)
            ->addColumn('action', function($admin){
                $msg = "'Do you want to delete this admin?'";
                return '<a href="'.action('admin\AdminController@edit', $admin['id']).'"><i class="fa fa-edit"></i></a>
                        <form onsubmit="return confirm('.$msg.');" style="display: inline-block;" action="'.action('admin\AdminController@destroy', $admin['id']).'" method="post">
                           '.csrf_field().'
                            <input name="_method" type="hidden" value="DELETE">
                            <button type="submit"><i class="fa fa-trash"></i></button>
                        </form>';
            })->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.admin.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate(request(), [
            'first_name' => 'required|string',
			'last_name' => 'required|string',
            'email' => 'required|email|unique:admins',
            'password' => 'required|confirmed|min:6',
            'title' => 'required|string',
            'phone' => 'nullable|numeric',
        ], [
            'password.confirmed' => 'Passwords are not identical.',            
        ]);
        
        $input = request()->all();
        $emailTemplate = EmailTemplate::where('alias_name', 'admin_registration')->select('id')->first();
        $emailTemplateData = EmailTemplateTranslation::where('email_template_id', $emailTemplate->id)
                        ->where('locale', $input['language_preference'])                        
                        ->select('subject','content')->first();

        $subject = $emailTemplateData->subject;
        $subject = str_replace('[FIRST_NAME]', $input['first_name'], $subject);
        $subject = str_replace('[USER_EMAIL]', $input['email'], $subject);
        $subject = str_replace('[USER_PASSWORD]', $input['password'], $subject);

        $content = $emailTemplateData->content;
        $content = str_replace('[FIRST_NAME]', $input['first_name'], $content);
        $content = str_replace('[USER_EMAIL]', $input['email'], $content);
        $content = str_replace('[USER_PASSWORD]', $input['password'], $content);

        /*$data = array(
                'email' => $input['email'], 
                'subject' => $subject,
                'content' => $content
                );*/

        $input['password'] = bcrypt($input['password']);
        unset($input['password_confirmation']);
        Admin::create($input);
        
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

        mail($input['email'],$subject,$content,$headers,'-finfo@ascentpartners.com');

        /*Mail::send('email.welcome-admin', $data, function($message) use ($data) {
            $message->to($data['email'])->subject($data['subject']);
            $message->from('info@cyblance.com', 'Cyblance');
        });*/

        return redirect('admin/admins')->with('success','Admin has been created.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $admin = Admin::findOrFail($id);
        return view('admin.admin.edit', compact('admin'));
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
            'first_name' => 'required|string',
			'last_name' => 'required|string',
            'email' => 'required|email|unique:admins,email,'.$id,
            'password' => 'nullable|min:6',
            'title' => 'required|string',
            'phone' => 'nullable|numeric',
        ]);
        
        $input = request()->all();
        //echo '<pre>';print_r($input);exit();
        if(trim($input['password'])!=''){
            $input['password'] = bcrypt($input['password']);
        }else{
            unset($input['password']);
        }
        Admin::find($id)->update($input);
        return redirect('admin/admins')->with('success','Admin has been updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $admin = Admin::findOrFail($id);
        $admin->delete();
        return redirect('admin/admins')->with('success','Admin has been deleted.');
    }
}
