<?php

namespace App\Http\Controllers\Manager;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Cess\Cess;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use App\Models\User\User;
use Illuminate\Support\Facades\Crypt;

class ManagerController extends Controller
{

    public function index(Request $request)
    {
        $url = $request->url;
        $url  = htmlspecialchars($url);
        if (isset($request->key)) {

            $url = $url . '&key=' . $request->key;
        }
        $urlComplete = env('APP_URL_ENV') . $url;

        if ($url != '') {
            $url= $urlComplete;
            return view('manager', compact('url'));
        } else {
            abort(404);
        }
    }

    public function getUserLogin()
    {
        //$infoUser = \Auth::id();
        //$infoUser = auth()->id();
        $infoUser = auth()->user();
        //$infoUser = Session::all();
        return response()->json($infoUser);
    }
}
