<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MailPassword extends Mailable{

    protected $data;

    public function __construct($data){
        
        $this->data = (object) $data;

    }

    public function build(){

        return $this->markdown('mails.recover_password')->with([
            'email' => $this->data->email,
            'password' => $this->data->password
        ]);

    }

}