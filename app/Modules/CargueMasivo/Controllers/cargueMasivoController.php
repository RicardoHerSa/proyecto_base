<?php

namespace App\Modules\CargueMasivo\Controllers;

use App\Http\Controllers\Controller;
use App\Services\adobe;
use App\Services\validacionCampos ;
use Illuminate\Support\Facades\Storage;
use App\Services\FlujoApr;
use DB;
use App\Notifications\Correcion;
use Illuminate\Support\Facades\Notification;
use Swift_SwiftException;
use Illuminate\Http\Request;


class cargueMasivoController extends Controller
{
    
    public function index()
    {   
        return view('CargueMasivo::cargueMasivo');
    }

    public function cargarColaboradores(Request $request)
    {
        if($request->hasFile('archivo')){
            echo "si";
        }
        var_dump($request->input("archivo"));
    }

   
    
   
}





