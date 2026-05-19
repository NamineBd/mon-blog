<?php

namespace App\Mail;

use App\Models\Subscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ConfirmSubscriptionMail extends Mailable
{
    use Queueable, SerializesModels;

    public $subscriber;
    public $confirmationUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(Subscriber $subscriber)
    {
        $this->subscriber = $subscriber;
        // Génération de l'URL de confirmation (à adapter à ton frontend)
        $this->confirmationUrl = config('app.frontend_url') . '/newsletter/confirm/' . $subscriber->id;
        // Alternative : route API directe (si frontend et backend partagent le même domaine)
        // $this->confirmationUrl = url('/api/newsletter/confirm/' . $subscriber->id);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Confirmez votre abonnement à la newsletter',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.confirm-subscription', // vue que nous allons créer
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}