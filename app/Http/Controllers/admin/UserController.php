<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\DataTables;
use App\User;
use App\Company;
use App\Language;
use App\EmailTemplate;
use App\EmailTemplateTranslation;
use Mail;
use App\SendSurvey;

class UserController extends Controller
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
    	//$users = User::paginate(1);
        //return view('admin.user.index', compact('users'));
        return view('admin.user.index');
    }

    public function getUsers()
    {
        $users = User::all();
        return DataTables::of($users)
            ->editColumn('company_id', function ($user) {
                $company = Company::select('name')->find($user->company_id);
                return $company->name;
            })
            ->editColumn('phone', function ($user) {
                $phones = json_decode($user->phone);
                if(is_array($phones)){
                    $str_phone = '<ul>';
                    foreach($phones as $phone){
                        $str_phone .= '<li>'.$phone.'</li>';
                    }
                    $str_phone .= '</ul>';
                    $phones = $str_phone;
                }
                return $phones;
            })
            ->editColumn('user_name', function($user){
                return $user->first_name.' '.$user->last_name;
            })
            ->addColumn('action', function($user){
                if (SendSurvey::where('user_id', '=', $user->id)->exists()) {
                    $msg = "'Delete operation failed since it has associated record(s).'";
                    return '<a href="'.action('admin\UserController@edit', $user['id']).'"><i class="fa fa-edit"></i></a>
                            <button onclick="return alert('.$msg.');" type="button"><i class="fa fa-trash"></i></button>';
                }else{
                    $msg = "'Do you want to delete this user?'";
                    return '<a href="'.action('admin\UserController@edit', $user['id']).'"><i class="fa fa-edit"></i></a>
                            <form onsubmit="return confirm('.$msg.');" style="display: inline-block;" action="'.action('admin\UserController@destroy', $user['id']).'" method="post">
                               '.csrf_field().'
                                <input name="_method" type="hidden" value="DELETE">
                                <button type="submit"><i class="fa fa-trash"></i></button>
                            </form>';
                }
            })
            ->rawColumns(['phone', 'action'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.user.create');
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
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed|min:6',
            //'phone.*' => 'regex:^(\+\d{1,2}\s)?\(?\d{3}\)?[\s.-]\d{3}[\s.-]\d{4}$^',
            'phone.*' => 'nullable|numeric',
            'title' =>  'required|string',
            'department' =>  'required|string',
            'company_id' =>  'required',
        ], [
            'company_id.required' => 'The company field is required.',
            'phone.*.numeric' => 'The phone must be a number.',
        ]);
        
        $input = request()->all();
        $emailTemplate = EmailTemplate::where('alias_name', 'user_registration')->select('id')->first();
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

        $input['password'] = bcrypt($input['password']);
        unset($input['password_confirmation']);
        $input['phone'] = json_encode(array_values(array_filter($_POST['phone'])));
        
        $user = User::create($input);
        $company = Company::findOrFail($user->company_id);

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

        mail($input['email'],$subject,$content,$headers,'-finfo@ascentpartners.com');

        /*$data = array(
            'email' => $input['email'], 
            'subject' => $emailTemplateData->subject,
            'content' => $content
        );

        Mail::send('email.welcome-user', $data, function($message) use ($data) {
            $message->to($data['email'])->subject($data['subject']);
            $message->from('info@cyblance.com', 'Cyblance');
        });*/

        return redirect('admin/users')->with('success','User has been created.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('admin.user.edit', compact('user'));
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
            'email' => 'required|email|unique:users,email,'.$id,
			'password' => 'nullable|min:6',
            //'phone.*' => 'nullable|regex:^(\+\d{1,2}\s)?\(?\d{3}\)?[\s.-]\d{3}[\s.-]\d{4}$^',
            'phone.*' => 'nullable|numeric',
            'title' =>  'required|string',
            'department' =>  'required|string',
            'company_id' =>  'required',
        ], [
            'company_id.required' => 'The company field is required.',
            'phone.*.numeric' => 'The phone must be a number.',
        ]);
        
        $input = request()->all();
		if(trim($input['password'])!=''){
            $input['password'] = bcrypt($input['password']);
        }else{
            unset($input['password']);
        }
        $input['phone'] = json_encode(array_values(array_filter($_POST['phone'])));
        //print_r($input);exit();
        User::find($id)->update($input);
        return redirect('admin/users')->with('success','User has been updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return redirect('admin/users')->with('success','User has been deleted.');
    }
}
