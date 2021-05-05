<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

use Illuminate\Support\Facades\DB;

class createUsersProgram extends Notification implements ShouldQueue
{
    use Queueable;

    protected $token;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {   //Ruta de estructura de mail resources/views/vendor/notifications/email.blade.php
        //$url = route('reset.token'.$this->token);
        // $url = url('/api/resetemail/find/'.$this->token);

    

        $users = DB::table('jess_users')->where('block','0')->count();
        $users_new = DB::table('jess_users')->whereBetween('created_at',[DB::raw("current_date"),DB::raw("current_date")])->count();
         

        $mensaje = '';
        $TOTAL_INFO_LAB =  DB::table('cess_employee_inf')->count();
        $TOTAL_INFO_PER =  DB::table('cess_person_inf')->count();
        
        $TOTAL_INFO_LAB_CVJ =  DB::table('cess_employee_inf')->where('cess_id_org' , 'CO_CVJ')->count();
        $TOTAL_INFO_PER_CVJ =  DB::table('cess_person_inf')->where('cess_id_org' , 'CO_CVJ')->count();

        $TOTAL_INFO_LAB_SGC =  DB::table('cess_employee_inf')->where('cess_id_org' , 'MX_SGC')->count();
        $TOTAL_INFO_PER_SGC =  DB::table('cess_person_inf')->where('cess_id_org' , 'MX_SGC')->count();

        $TOTAL_INFO_LAB_EC =  DB::table('cess_employee_inf')->where('cess_id_org' , 'EC_CVJ')->count();
        $TOTAL_INFO_PER_EC =  DB::table('cess_person_inf')->where('cess_id_org' , 'EC_CVJ')->count();

       


        if ( $TOTAL_INFO_LAB == $TOTAL_INFO_PER ) {
            return (new MailMessage)
                ->subject('Notificación Proceso de Creación de Usuario')
                ->greeting('Buen Día ')
                ->line('Se ha ejecuto correctamente la creacion de usuario Fecha : ' .date("Y-m-d- H:m:s"))
                ->line('Total de nuevo usuarios  :'.$users_new) 
                ->line('Total de usuarios Activos :' .  $users )
                
                ->salutation('Cordialmente:');
        }else{


            return (new MailMessage)
            ->subject('ERROR - Proceso de Creación de Usuario')
            ->greeting('Buen Día ')
            ->line('ERROR : EL PROCESO NO SE EJECUTO')

            ->line('TOTAL TODOS LOS GRUPOS EMPRESARIALES')     
            ->line('Total de registros Informacion Laboral : ' .   $TOTAL_INFO_LAB)  
            ->line('Total de registros Informacion Personal : ' .  $TOTAL_INFO_PER ) 

            ->line('CO_CVJ COLOMBIA') 

            ->line('Total de registros Informacion Laboral : ' .   $TOTAL_INFO_LAB_CVJ ) 
            ->line('Total de registros Informacion Personal : ' .  $TOTAL_INFO_PER_CVJ )
        
            ->line('MX_SGC MEXICO') 
            ->line('Total de registros Informacion Laboral : ' .  $TOTAL_INFO_LAB_SGC ) 
            ->line('Total de registros Informacion Personal : ' . $TOTAL_INFO_PER_SGC) 
        
            ->line('EC_CVJ ECUADOR')
            ->line('Total de registros Informacion Laboral : ' .   $TOTAL_INFO_LAB_EC ) 
            ->line('Total de registros Informacion Personal : ' .  $TOTAL_INFO_PER_EC)
            ->line('Nota :')
            ->line( 'El numero de registro de informacion laboral y personas deben ser igual  En caso
            contrario se debe ejecutar el cargue de ellos.')        
            ->salutation('Cordialmente:');



        }

    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
