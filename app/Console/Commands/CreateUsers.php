<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\CreateUsersSica;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ReporteSoporte;
use Illuminate\Support\Facades\Hash;
use App\Models\User\User;
use Illuminate\Support\Facades\Log;

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
      $user->run();

    }
}
