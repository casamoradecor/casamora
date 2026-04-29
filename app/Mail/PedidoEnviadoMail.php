<?php

namespace App\Mail;

use App\Models\Pedido;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PedidoEnviadoMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Instância do pedido que será disponibilizada para a View.
     * * @var \App\Models\Pedido
     */
    public $pedido;

    /**
     * Create a new message instance.
     * * @param \App\Models\Pedido $pedido
     */
    public function __construct(Pedido $pedido)
    {
        $this->pedido = $pedido;
    }

/**
 * Define o envelope do e-mail, incluindo o assunto personalizado.
 */
public function envelope(): Envelope
{
    return new Envelope(
        subject: 'Seu pedido da Casa MORÁ está a caminho!',
        );
    }

/**
 * Define o conteúdo do e-mail usando o template Markdown.
 */
public function content(): Content
{
    return new Content(
        markdown: 'emails.pedido-enviado',
        );
    }

/**
 * Get the attachments for the message.
 *
 * @return array<int, \Illuminate\Mail\Mailables\Attachment>
 */
public function attachments(): array
{
    return [];
}
}
