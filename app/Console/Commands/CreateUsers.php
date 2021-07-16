<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\CreateUsersSica;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ReporteSoporte;
use App\Models\User\User;
class CreateUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crea los usuarios nuevos en SICA ';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        
        
        $user =  new CreateUsersSica();
        $Total = $user->getUserTotal();
        
      
      if ($Total == 0) {

        Notification::route('mail',['david.guanga@carvajal.com'])->notify(new ReporteSoporte( $Total ,'OK'));

       }else{

        Notification::route('mail',['david.guanga@carvajal.com'])->notify(new ReporteSoporte( $Total ,'ERROR'));
      }
        
           
        


    }
}
