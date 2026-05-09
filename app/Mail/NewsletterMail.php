<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewsletterMail extends Mailable
{
    use Queueable, SerializesModels;

    public $assunto;
    public $mensagem;
    public $produtos;

    public function __construct($assunto, $mensagem, $produtos)
    {
        $this->assunto = $assunto;
        $this->mensagem = $mensagem;
        $this->produtos = $produtos;
    }

    public function build()
    {
        return $this->subject($this->assunto)
            ->view('email.newsletter-template'); // Criaremos este arquivo abaixo
    }
}
