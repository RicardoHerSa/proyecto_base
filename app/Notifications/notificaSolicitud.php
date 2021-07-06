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
    protected $sede;
    protected $tipoVi;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($idSolicitud,$solicitante,$labor,$tipoValidacion, $causa, $sede, $tipoVi)
    {
        $this->idSolicitud = $idSolicitud;
        $this->solicitante = $solicitante;
        $this->labor = $labor;
        $this->tipoValidacion = $tipoValidacion;
        $this->causa = $causa;
        $this->sede = $sede;
        $this->tipoVi = $tipoVi;
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
        $consultanombreSede = DB::table('ohxqc_ubicaciones as ubi')
        ->select('ubi.descripcion')
        ->where('ubi.id_ubicacion', $this->sede)
        ->get();

        $nombreSede = $consultanombreSede[0]->descripcion;


        $nivelSolicitud = DB::table('ohxqc_solicitud_por_aprobar')
        ->select('niveles')
        ->where('id_solicitud', $this->idSolicitud)
        ->where('tipo_visitante', $this->tipoVi)
        ->where('sede_id', $this->sede)
        ->get();

        $flujoHasta = $nivelSolicitud[0]->niveles;

         if($this->tipoValidacion == "A"){

            return (new MailMessage)
            ->subject('Solicitud #'.$this->idSolicitud.' Aprobada - Sede: '.$nombreSede.'.')
            ->greeting('Apreciado Solicitante')
            ->line('El Sistema Integral Control de Acceso (SICA) le informa que la solicitud #'.$this->idSolicitud.' para la sede '.$nombreSede.' ha sido aprobada por el área de seguridad en el trabajo / área de Seguridad Corporativa.')
            ->line('Detalles de la solicitud: ')
            ->line('Solicitante: '.$this->solicitante)
            ->line('Labor a realizar: '.$this->labor)
            ->line('Flujo validado: '.$flujoHasta.'/'.$flujoHasta)
            ->line('Validada por: '.auth()->user()->name.' en el flujo #'.$flujoHasta)
            ->salutation('Cordialmente:');
         }else{
            return (new MailMessage)
            ->subject('Solicitud #'.$this->idSolicitud.' Rechazada - Sede: '.$nombreSede.'.')
            ->greeting('Apreciado Solicitante')
            ->line('El Sistema Integral Control de Acceso (SICA) le informa que la solicitud #'.$this->idSolicitud.' para la sede '.$nombreSede.' ha sido rechazada por el área de seguridad en el trabajo / área de Seguridad Corporativa.')
            ->line('Detalles de la solicitud: ')
            ->line('Solicitante: '.$this->solicitante)
            ->line('Labor a realizar: '.$this->labor)
            ->line('Flujo validado: '.$flujoHasta.'/'.$flujoHasta)
            ->line('Validada por: '.auth()->user()->name.' en el flujo #'.$flujoHasta)
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
