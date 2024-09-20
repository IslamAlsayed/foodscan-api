<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MailOrder extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */

    public $order;

    public function __construct($order)
    {
        $this->order = $order;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $status = $this->order->order_status;
        $subject = 'Order ' . ($status === "in_progress" ? "in progress" : $status);

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $statusMessage =
            $this->order->order_status === 'in_progress' ?
            'Your order is currently in progress. We are working hard to get it completed as soon as possible. Please review the updated details below.'
            : ($this->order->order_status === 'cancelled' ?
                'We regret to inform you that your order has been cancelled. Please review the details below and contact us if you have any questions or concerns.'
                : ($this->order->order_status === 'done' ?
                    'Your order is now ready for pickup! 🎁 Please visit us to pick it up. We appreciate your patience!'
                    : 'Your order has been updated. Please review the updated details below.'));

        return new Content(
            view: 'emails.order',
            with: [
                'order' => $this->order,
                'statusMessage' => $statusMessage,
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
