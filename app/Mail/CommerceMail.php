<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class CommerceMail extends Mailable implements ShouldQueue
{
    use Queueable;
    public function __construct(public string $mailSubject, public string $heading, public string $body, public ?string $actionUrl = null, public ?string $actionLabel = null) {}
    public function envelope(): Envelope { return new Envelope(subject: $this->mailSubject); }
    public function content(): Content { return new Content(view: 'emails.commerce'); }
}
