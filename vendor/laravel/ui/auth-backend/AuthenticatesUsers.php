<?php

namespace Illuminate\Foundation\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Adldap\Laravel\Facades\Adldap;
use App\Models\User\User;
use Illuminate\Support\Facades\Hash;

trait AuthenticatesUsers
{
    use RedirectsUsers, ThrottlesLogins;

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }
    


    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request)
    {
        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if (
            method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)
        ) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    /**
     * Validate the user login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validateLogin(Request $request)
    {
        $request->validate([
            $this->username() => 'required|string',
            'password' => 'required|string',
        ]);
    }

    /**
     * Attempt to log the user into the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function attemptLogin(Request $request)
    {
        return $this->guard()->attempt(
            $this->credentials($request),
            $request->filled('remember')
        );
    }

    /**
     * Get the needed authorization credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(Request $request){
        $info = array_merge($request->only($this->username(), 'password'), ['block' => 0]);
        $username = $info[$this->username()];
        $password = $info['password'];

       

            /*e-fergua Se modifica el metodo de busqueda 
                puesto que la cosulta busca similitudes y puede traer usuario errados 
                causando el no logueo a la aplicacion
                $user = Adldap::search()->users()->find($username);*/

            /* Busca el usuario exacto es sensible a mayuscula y minuscula
            por politica coorporativa todos los usuarios 
            son en minuscula   
             */
          $user = Adldap::search()->where('samaccountname', '=', $username)->get();
           
           
             if(isset($user[0]['mail'][0])){
              
               if (Adldap::auth()->attempt($user[0]['mail'][0], $password, $bindAsUser = true)) {
                $infouser = User::where($this->username(), $username)->first();
                
                if(isset($infouser)){
                    if (Hash::check($password, $infouser->password)){
                        return $info;
                    }else{
                        $pass = Hash::make($password);
                        $infouser->update([
                            'password' => $pass
                        ]);
                        return $info;
                    }
                    
                }else{
                    $info = [
                        "username" => "nn",
                        "password" => "password",
                        "block" => 1,
                        ];
                    return $info;
                   
                }
            } else{
                $info = [
                "username" => "nn",
                "password" => "password",
                "block" => 1,
                ];
                return $info;
            }
        }else {
            $infouser = User::where($this->username(), $username)->first();
            if(isset($infouser)){
                return $info;
            }else{
                //el usuario local no existe
                return $info;
            }
        }
        
    }
    /**
     * Send the response after the user was authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    protected function sendLoginResponse(Request $request)
    {
        $request->session()->regenerate();

        $this->clearLoginAttempts($request);

        return $this->authenticated($request, $this->guard()->user())
            ?: redirect()->intended($this->redirectPath());
    }

    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        //
    }

    /**
     * Get the failed login response instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        throw ValidationException::withMessages([
            $this->username() => [trans('auth.failed')],
        ]);
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        return 'email';
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        session_start();
        unset($_SESSION["user_laravel"]);
        unset($_SESSION["user"]);
        $this->guard()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        /** Permite cerrar la session en Yii e-fergua */
       // header("Location: ".env('APP_URL_ENV')."Notification/end"); // (*) Eliminar para proyecto diferente a portal
        //exit(); // (*) Eliminar para proyecto diferente a portal
        return $this->loggedOut($request) ?: redirect('/');
    }

    /**
     * The user has logged out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    protected function loggedOut(Request $request)
    {
        //
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard();
    }
}
