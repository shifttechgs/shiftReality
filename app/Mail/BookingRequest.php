<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

/**
 * A visitor's booking request from /book, sent to the bookings inbox
 * (config shiftreality.booking.email). Written for the team: the visit at a
 * glance, then one tap to confirm on WhatsApp (pre-written), call or reply.
 */
class BookingRequest extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  array{ref: string, name: string, phone: string, email: ?string, note: ?string, day: string, time: string, people: int, total: string, received: string}  $booking
     */
    public function __construct(public array $booking) {}

    public function envelope(): Envelope
    {
        $b = $this->booking;

        return new Envelope(
            subject: "Booking request: {$b['name']}, {$b['day']} at {$b['time']}, {$b['people']} ".Str::plural('guest', $b['people'])." ({$b['ref']})",
            replyTo: filled($b['email']) ? [new Address($b['email'], $b['name'])] : [],
        );
    }

    public function content(): Content
    {
        $b     = $this->booking;
        $first = Str::before(trim($b['name']), ' ');
        $tel   = preg_replace('/[^\d+]/', '', $b['phone']);
        // 077… → 26377… for wa.me; +263… and 263… stay as they are
        $wa    = preg_replace('/^0/', '263', ltrim($tel, '+'));

        $confirm = "Hi {$first}, this is ".config('shiftreality.name').'. Your visit on '
            ."{$b['day']} at {$b['time']} for {$b['people']} ".Str::plural('guest', $b['people']).' is confirmed. '
            .'Find us at '.config('shiftreality.location.address').', '.config('shiftreality.location.city').'. '
            ."Your reference is {$b['ref']}. See you then!";

        return new Content(
            view: 'mail.booking-request',
            text: 'mail.booking-request-text',
            with: [
                'first'       => $first,
                'telLink'     => 'tel:'.$tel,
                'waLink'      => 'https://wa.me/'.$wa.'?text='.rawurlencode($confirm),
                'mailtoLink'  => filled($b['email'])
                    ? 'mailto:'.$b['email'].'?subject='.rawurlencode('Your Reality Shift booking ('.$b['ref'].')')
                    : null,
                'guestsLabel' => Str::plural('guest', $b['people']),
            ],
        );
    }
}
