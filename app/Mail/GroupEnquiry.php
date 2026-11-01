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
 * A group enquiry from /groups, sent to the bookings inbox. Written for the
 * team, like BookingRequest: the group at a glance, then one tap to reply
 * on WhatsApp, call or email with a price.
 */
class GroupEnquiry extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  array{occasion: string, size: int, date: ?string, day: ?string, name: string, organisation: ?string, phone: string, email: ?string, note: ?string, received: string}  $enquiry
     */
    public function __construct(public array $enquiry) {}

    public function envelope(): Envelope
    {
        $e   = $this->enquiry;
        $who = filled($e['organisation'] ?? null) ? "{$e['name']} ({$e['organisation']})" : $e['name'];

        return new Envelope(
            subject: "Group enquiry: {$e['occasion']}, {$e['size']} people, {$who}",
            replyTo: filled($e['email'] ?? null) ? [new Address($e['email'], $e['name'])] : [],
        );
    }

    public function content(): Content
    {
        $e     = $this->enquiry;
        $first = Str::before(trim($e['name']), ' ');
        $tel   = preg_replace('/[^\d+]/', '', $e['phone']);
        $wa    = preg_replace('/^0/', '263', ltrim($tel, '+'));
        $hello = "Hi {$first}, this is ".config('shiftreality.name').". Thanks for your group enquiry ({$e['occasion']}, {$e['size']} people). ";

        return new Content(
            view: 'mail.group-enquiry',
            text: 'mail.group-enquiry-text',
            with: [
                'first'      => $first,
                'telLink'    => 'tel:'.$tel,
                'waLink'     => 'https://wa.me/'.$wa.'?text='.rawurlencode($hello),
                'mailtoLink' => filled($e['email'] ?? null)
                    ? 'mailto:'.$e['email'].'?subject='.rawurlencode('Your Reality Shift group visit')
                    : null,
            ],
        );
    }
}
