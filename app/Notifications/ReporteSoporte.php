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
       
        return (new MailMessage)
                    ->subject('Titulo de asunto')
                    ->greeting('Saludo')
                    ->line('En cada linea se escribe un texto' )
                    ->line('Cada linea será como una etiqueta de aprrafo' )
                    ->line(new HtmlString('<br>' ))
                    ->line(new HtmlString('<br>' ))
                    ->salutation('Cordialmente:');

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
