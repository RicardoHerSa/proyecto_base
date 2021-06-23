<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\DB;

class porterias extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    protected $idSolicitud="";
    protected $solicitante="";
    protected $sede="";
    protected $labor="";
    public function __construct($idSolicitud,$solicitante,$sede,$labor)
    {
        $this->idSolicitud = $idSolicitud;
        $this->solicitante = $solicitante;
        $this->sede = $sede;
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
    {
        $sedeN =  DB::table('ohxqc_ubicaciones as ubi')
        ->select('ubi.descripcion')
        ->where('ubi.id_ubicacion', $this->sede)
        ->get();
        foreach($sedeN as $name){
            $nombreSede = $name->descripcion;
        }
        return (new MailMessage)
                    ->subject('Solicitud #'.$this->idSolicitud.' Aprobada - Sede: '.$nombreSede.'.')
                    ->greeting('Hola')
                    ->line('Se ha aprobado la solicitud de ingreso #'.$this->idSolicitud.' para la sede: '.$nombreSede)
                    ->line('Detalles de la solicitud: ')
                    ->line('Solicitante: '.$this->solicitante)
                    ->line('Labor a realizar: '.$this->labor)
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
