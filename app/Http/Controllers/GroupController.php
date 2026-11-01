<?php

namespace App\Http\Controllers;

use App\Mail\GroupEnquiry;
use App\Support\OpeningHours;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Throwable;

/**
 * Group visits (/groups): the pitch, then an enquiry form that emails the
 * bookings inbox (config shiftreality.booking.email), as /book does. The team
 * replies with a price; nothing is booked or paid online.
 */
class GroupController extends Controller
{
    public function show()
    {
        return view('pages.groups', [
            'shifts'       => config('shiftreality.shifts'),
            'visit'        => config('shiftreality.visit'),
            'groups'       => config('shiftreality.groups'),
            'contact'      => config('shiftreality.contact'),
            'occasions'    => $this->occasions(),
            'minGroup'     => config('shiftreality.booking.max_people') + 1,
            'openingHours' => OpeningHours::fromConfig(),
            'sent'         => session('group_sent'),
        ]);
    }

    public function send(Request $request)
    {
        $today = CarbonImmutable::now(config('shiftreality.timezone'))->toDateString();

        $data = $request->validate([
            'occasion'     => ['required', Rule::in($this->occasions())],
            'size'         => ['required', 'integer', 'min:2', 'max:1000'],
            'date'         => ['nullable', 'date_format:Y-m-d', 'after_or_equal:'.$today],
            'name'         => ['required', 'string', 'max:80'],
            'organisation' => ['nullable', 'string', 'max:120'],
            'phone'        => ['required', 'string', 'max:20', 'regex:/^\+?[\d\s()-]{7,20}$/'],
            'email'        => ['nullable', 'email', 'max:120'],
            'note'         => ['nullable', 'string', 'max:600'],
        ], [
            'occasion.required'    => 'Tell us what the visit is for.',
            'size.required'        => 'Tell us roughly how many are coming.',
            'size.min'             => 'A group is at least 2 people.',
            'date.after_or_equal'  => 'Pick a date from today on.',
            'name.required'        => 'Tell us who to ask for.',
            'phone.required'       => 'Add a phone number so we can send you a price.',
            'phone.regex'          => 'That phone number doesn\'t look right. Use digits, like 077 123 4567.',
            'email.email'          => 'That email address doesn\'t look right.',
        ]);

        $enquiry = array_merge(['organisation' => null, 'email' => null, 'note' => null, 'date' => null], $data, [
            'phone'    => trim($data['phone']),
            'size'     => (int) $data['size'],
            'day'      => filled($data['date'] ?? null) ? CarbonImmutable::parse($data['date'])->format('l j F Y') : null,
            'received' => CarbonImmutable::now(config('shiftreality.timezone'))->format('D j M \a\t H:i'),
        ]);

        try {
            Mail::to(config('shiftreality.booking.email'))->send(new GroupEnquiry($enquiry));
        } catch (Throwable $e) {
            report($e);

            return back()->withInput()->withErrors([
                'send' => 'We couldn\'t send your enquiry just now. Please try again, or call us on '.config('shiftreality.contact.phone').'.',
            ]);
        }

        return redirect()->to(route('groups').'#enquire')->with('group_sent', $enquiry);
    }

    /** What a group visit can be for: the occasions in config, then anything else */
    private function occasions(): array
    {
        return [...collect(config('shiftreality.groups.occasions'))->pluck('title')->all(), 'Something else'];
    }
}
