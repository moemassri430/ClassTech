<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Rules\Captcha;
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

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function login(Request $request){

        $input = $request->all();
        $this->validate($request,[
            'email'=>'required|email',
            'password'=>'required',
            'g-recaptcha-response'=>new Captcha(),
        ]);

        if(auth()->attempt(['email'=>$input["email"],'password'=>$input['password']]))
        {
            if(auth()->user()->role=='admin')
            {
                return redirect()->route('home');
            }
            else if(auth()->user()->role=='student')
            {
                return redirect()->route('home.student');
            }
            else
            {
                return redirect()->route('home.teacher');
            }

        }
        else{
            return redirect()
            ->route('login')
            ->with("error","Incorrect email or password");
        }
    }
}
