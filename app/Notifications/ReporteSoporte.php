<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Notifiable;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;

class ReporteSoporte extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
   
    public $i_detalleidSolicitud ;
    public $i_tipo ;

    public function __construct($T_nuevo,$T_inactivo,$tipo)
    {
        $this->i_T_nuevo = $T_nuevo;
        $this->i_T_inactivo = $T_inactivo;
        $this->i_tipo = $tipo;
        
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
    {
       
       if($this->i_tipo == 'OK'){
        return (new MailMessage)
                    ->subject('Creación de Usuarios Sica')
                    ->greeting('Buen Día')
                    ->line('El Sistema Integral Control de Acceso (SICA) le informa ' )
                    ->line('Total de  Nuevos Usuarios :' . $this->i_T_nuevo )
                    ->line('Total de Usuarios Inactivados :' . $this->i_T_inactivo )
                    ->line( 'Fecha: ' .now() )
                    ->line(new HtmlString('<br>' ))
                    ->line(new HtmlString('<br>' ))
                    ->salutation('Cordialmente:');
       } else {
       
        $url = 'https://172.19.143.6:9455/services/SicaZzInterfaceHrsysMsService?tryit';
        return (new MailMessage)
                ->subject('ERROR - Creación de Usuarios Sica')
                ->greeting('Buen Día')
                ->line('El Sistema Integral Control de Acceso (SICA) le informa ' )
                ->line('La Interfaz  de cargue de datos de Hrsys a Sica' )
                ->line('NO  se ejecuto hoy ' . now() )
                ->action('EJECUTAR SERVICIO', url($url))
                ->line(new HtmlString('<br>' ))
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
