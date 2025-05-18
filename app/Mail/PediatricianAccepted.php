<?php

// app/Mail/PediatricianAccepted.php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PediatricianAccepted extends Mailable
{
    use Queueable, SerializesModels;

    public $pediatrician;

    public function __construct($pediatrician)
    {
        $this->pediatrician = $pediatrician;
    }

    public function build()
    {
        return $this->subject('Votre compte a été validé')
                    ->view('Mail.Accpt');
    }
}

