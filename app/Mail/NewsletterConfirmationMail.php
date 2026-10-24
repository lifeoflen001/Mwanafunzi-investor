<?php

namespace App\Mail;

use App\Models\NewsletterSubscriber;
use App\Support\NewsletterSettings;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class NewsletterConfirmationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public NewsletterSubscriber $subscriber, public string $token) {}

    public function envelope(): Envelope
    {
        $settings = NewsletterSettings::all();
        return new Envelope(from: new Address($settings['sender_email'], $settings['sender_name']), replyTo: [new Address($settings['reply_to_email'])], subject: 'Confirm your Mwanafunzi Investor subscription');
    }

    public function content(): Content
    {
        $unsubscribeToken = $this->subscriber->ensureUnsubscribeToken();

        return new Content(view: 'emails.newsletter.confirmation', with: [
            'confirmUrl' => URL::temporarySignedRoute('newsletter.confirm', now()->addDays(7), ['token' => $this->token]),
            'unsubscribeUrl' => URL::signedRoute('newsletter.unsubscribe', ['token' => $unsubscribeToken]),
        ]);
    }
}
