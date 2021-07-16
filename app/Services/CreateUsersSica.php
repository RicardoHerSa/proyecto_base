<?php namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

Class CreateUsersSica {

    public $User_Total;

    public  function   __construct()
    {
        
        $this->User_Total = DB::table('public.cvj_co_interfaz_sica_prd')->count();
    }
    
   /**
    * Undocumented function
    *
    * @return string
    * @author David Guanga <david.guanga@carvajal.com>
    */
    public  function getUserTotal(){

        return  $this->User_Total;
    }



}