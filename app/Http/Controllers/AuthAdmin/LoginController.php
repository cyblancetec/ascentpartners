<?php

namespace App\Http\Controllers\AuthAdmin;

use App\Http\Controllers\Controller;
//use Illuminate\Foundation\Auth\RedirectsUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Admin;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    //use RedirectsUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/admin';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {

        $this->middleware('guest:admin')->except(['logout']);
    }

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\Http\Response 
     */
    public function showLoginForm(Request $request) {

        return view( 'authAdmin.login' );
    }

    /**
     * Handle a login request to the application.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse\Illuminate\Http\Response\Illuminate\Http\JsonResponse
     */
    public function login(Request $request) {

        $this->validate(request(), [
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);

        $credential = [
            'email' => $request->email,
            'password' => $request->password
        ];
        $remember_me = $request->has('remember') ? true : false; 

        // Attemp to log the user in
        if(Auth::guard('admin')->attempt($credential, $remember_me)) {

            // If login seccessful, then redirect to their intnded location
            return redirect()->intended(route('admin.home'));
        }

        // If Unsuccessful, then redirect back to the login with the form data
        //return redirect()->back()->withInput($request->only('email', 'remember'));
        //return redirect()->back()->withErrors(array('email'=>'These credentials do not match our records.'))->withInput($request->only('email', 'remember'));
        if ( ! Admin::where('email', $request->email)->first() ) {
            return redirect()->back()
                ->withInput($request->only('email', 'remember'))
                ->withErrors([
                    'email' => 'This email address is not registered, please contact Ascent Partners ESG Team for support.',
                ]);
        }
    
        if ( ! Admin::where('email', $request->email)->where('password', bcrypt($request->password))->first() ) {
            return redirect()->back()
                ->withInput($request->only('email', 'remember'))
                ->withErrors([
                    'password' => 'Wrong password',
                ]);
        }
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout()
    {
        Auth::guard('admin')->logout();
        return redirect('/admin/login');
    }
}
