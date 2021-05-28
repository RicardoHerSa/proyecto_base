<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Adldap\Laravel\Facades\Adldap;
use Illuminate\Http\Request;
use Session;
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
    //protected $redirectTo = "";//RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function username()
    {
        return 'username';
    }

    //Creación de metodos para redirigir a la ruta que intentaba ver el usuario antes de loguearse
    public function showLoginForm(Request $request)
    {
        if ($request->has('redirect_to')) {
            Session::put('redirect_to', $request->input('redirect_to'));
        }
        
        return view('auth.login');
    }

    public function redirectTo()
    {
       return Session::get('redirect_to');
        /*if (session()->has('redirect_to')){
            return session()->pull('redirect_to');

        }else{

            return $this->redirectTo;
        }*/

    }

}
