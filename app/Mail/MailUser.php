<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MailUser extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */

    public $user;
    public $operate;

    public function __construct($user, $operate)
    {
        $this->user = $user;
        $this->operate = $operate;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = 'You are ' . $this->operate;
        return new Envelope(
            subject: $subject
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.loginUser',
            with: [
                'user' => $this->user,
                'operate' => $this->operate
            ]
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
