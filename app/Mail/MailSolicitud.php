<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MailSolicitud extends Mailable{

    protected $data;

    public function __construct($data){
        
        $this->data = $data;

    }

    public function build(){

        $confirm= '   <a href="http://localhost:8080/#/solicitudes" class="button button-green"> Procesar</a>';

        return $this->markdown('mails.solicitud')->with([
            'usuario' => $this->data,
            'confirm' => $confirm,
        ]);

    }

}