<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ManagerUser;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use App\Notifications\createUsersProgram;
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
    protected $description = 'Crea los usuarios nuevos en Mi portal del colaborador ';

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
        
        
        $user =  new ManagerUser();
        $user->CeateUser();
        $user->UpdateJefeSinEstructura();
        $user->updatelda();
        $user->disableusers();
        
        $user = User::where('id', DB::raw('44891'))->first();
        Notification::send($user, new createUsersProgram(''));
        //echo 'Finalizo el Proceso';
           
        


    }
}
