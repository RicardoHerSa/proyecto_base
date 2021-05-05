<?php

namespace App\Http\Controllers\home;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use  App\Services\Utilidad ;

class homeController extends Controller
{
    
    public function index()
    {   
       // $url = env('APP_URL_HOME') ;
        //return view('home',compact('url'));
        $pais = "pe";
    
        return view('home.index', compact('pais'));
    }

    public function addinfo()
    {   
        $pais = "pe";
       // $url = env('APP_URL_HOME') ;
        //return view('home',compact('url'));

        return view('home.infoadd', compact('pais'));
    }
    //metodos hs company technology

    public function incorporarMenu()
    {   
    $pais = "pe";
    
        return view('home.navegacion', compact('pais'));
    }

    public function addaccion()
    {   
    $pais = "pe";
        return view('home.accionistas', compact('pais'));
    }

    public function addjunta()
    {   
     $pais = "pe";
        return view('home.junta', compact('pais'));
    }

    public function addrepresentante()
    {   
     $pais = "pe";
        return view('home.representante', compact('pais'));
    }

    
    
    public function addCalidad()
    {   
       // $url = env('APP_URL_HOME') ;
        //return view('home',compact('url'));
        $pais = "pe";
        return view('home.infoCalidad', compact('pais'));
    }

    
    public function addGeneral()
    {   
        $pais = "pe";
       // $url = env('APP_URL_HOME') ;
        //return view('home',compact('url'));

        return view('home.infoGeneral', compact('pais'));
    }

    public function addGeneral2()
    {   
        $pais = "pe";
       // $url = env('APP_URL_HOME') ;
        //return view('home',compact('url'));

        return view('home.infoGeneralSig', compact('pais'));
    }

    
   //   public function aenv('APP_URL_HOME') {
    //      //return view('home',compact('url'));
    //      return view('plantillas.loadFile.carga_documento_natural');
    //  }
}





