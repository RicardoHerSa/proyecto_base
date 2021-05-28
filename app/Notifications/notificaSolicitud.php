<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

use Illuminate\Support\Facades\DB;

class notificaSolicitud extends Notification implements ShouldQueue
{
    use Queueable;

    protected $idSolicitud;
    protected $solicitante;
    protected $labor;
    protected $tipoValidacion;
    protected $causa;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($idSolicitud,$solicitante,$labor,$tipoValidacion, $causa)
    {
        $this->idSolicitud = $idSolicitud;
        $this->solicitante = $solicitante;
        $this->labor = $labor;
        $this->tipoValidacion = $tipoValidacion;
        $this->causa = $causa;
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
         if($this->tipoValidacion == "A"){
            return (new MailMessage)
            ->subject('Solicitud #'.$this->idSolicitud.' Aprobada.')
            ->greeting('Hola')
            ->line('Le informamos que la solicitud de ingreso #'.$this->idSolicitud.' ha sido aprobada.')
            ->line('Detalles de la solicitud: ')
            ->line('Solicitante: '.$this->solicitante)
            ->line('Labor a realizar: '.$this->labor)
            ->salutation('Cordialmente:');
         }else{
            return (new MailMessage)
            ->subject('Solicitud #'.$this->idSolicitud.' Rechazada.')
            ->greeting('Hola')
            ->line('Le informamos que la solicitud de ingreso #'.$this->idSolicitud.' ha sido rechazada.')
            ->line('Detalles de la solicitud: ')
            ->line('Solicitante: '.$this->solicitante)
            ->line('Labor a realizar: '.$this->labor)
            ->line('')
            ->line('Causa: '.$this->causa)
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
