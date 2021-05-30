<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

use Illuminate\Support\Facades\DB;

class enviarSolicitud extends Notification implements ShouldQueue
{
    use Queueable;

    protected $token;
    protected $idSolicitud;
    protected $solicitante;
    protected $labor;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($token,$idSolicitud,$solicitante,$labor)
    {
        $this->token = $token;
        $this->idSolicitud = $idSolicitud;
        $this->solicitante = $solicitante;
        $this->labor = $labor;
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
         $url = url($this->token);

        return (new MailMessage)
        ->subject('Solicitud para aprobar nuevo visitante')
        ->greeting('Hola')
        ->line('Según el flujo al que perteneces, has recibido este correo para poder validar la solicitud número: '.$this->idSolicitud)
        ->line('Solicitante: '.$this->solicitante)
        ->line('Labor a realizar: '.$this->labor)
        ->line('Para ver mas detalles, pulsa a continuación:')
        ->action('Validar solicitud ', url($url))
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