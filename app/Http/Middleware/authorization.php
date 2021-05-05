<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Adldap\Laravel\Facades\Adldap;
use App\Models\User\User;
use Carbon\Carbon;

class authorization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Auth::check() && auth()->user()->block == 0){

            $username = auth()->user()->username;
            if(Adldap::search()->users()->find($username) == true){

                //var_dump($next($request));
                //exit();
                $userAuth = Auth::user();
                $request->session()->put('user', $userAuth);
                $_SESSION['user'] = serialize(Auth::user());
                return $next($request); 
            }else{
                $user = User::where('id', auth()->user()->id)->get();
                $days = Carbon::parse($user[0]->lastresettime)->addDays(90);
                $ParseDays = $days->toDateTimeString();
                $dateNow = Carbon::now()->toDateTimeString();
                
                if(auth()->user()->lastresettime == null || $dateNow >= $ParseDays){
                    return redirect()->route('reset.view');
                }
            }


            $userAuth = Auth::user();
            $request->session()->put('user', $userAuth);
            $_SESSION['user'] = serialize(Auth::user());
            return $next($request);

        }else{
        
            Auth::logout();   
            return redirect('/login');
        }
            
    }
}
