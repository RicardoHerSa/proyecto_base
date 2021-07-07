<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;

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
        
        $nombreSede = $sedeN[0]->descripcion;

        //tabla de detalles
        $tabla = DB::table('ohxqc_documentos_solicitud')->select('identificacion','nombre')->where('solicitud_id',$this->idSolicitud)->get();
        $registros = '';
        $registros.=  '<table class="table">
        <thead>
            <tr>
                <th scope="col">Identificación</th>
                <th scope="col">Nombre</th>
            </tr>
        </thead>';
        $registros.= '<tbody>';
            foreach($tabla as $tb){
                $registros.='<tr> 
                                    <th>'.$tb->identificacion.'</th>
                                    <th>'.$tb->nombre.'</th>
                            </tr>';
            }
        $registros.= '</tbody>
        </table>';

        return (new MailMessage)
                    ->subject('Solicitud #'.$this->idSolicitud.' Aprobada - Sede: '.$nombreSede.'.')
                    ->greeting('Apreciado Portero')
                    ->line('El Sistema Integral Control de Acceso (SICA) le informa que se ha aprobado la solicitud de ingreso #'.$this->idSolicitud.' para la sede: '.$nombreSede)
                    ->line('Detalles de la solicitud: ')
                    ->line('Solicitante: '.$this->solicitante)
                    ->line('Labor a realizar: '.$this->labor)
                    ->line(new HtmlString(
                        $registros
                    ))
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
