<?php

namespace Tests\Feature;

use App\Mail\BookingRequest;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class BookingTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Tuesday 20 October 2026, 08:00 in Harare — before opening
        CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-10-20 08:00', 'Africa/Harare'));
    }

    protected function tearDown(): void
    {
        CarbonImmutable::setTestNow();

        parent::tearDown();
    }

    private function valid(array $overrides = []): array
    {
        return array_merge([
            'date' => '2026-10-21', 'time' => '11:00', 'people' => 3,
            'name' => 'Rudo Moyo', 'phone' => '077 123 4567',
        ], $overrides);
    }

    public function test_booking_page_lists_days_and_times(): void
    {
        $this->get('/book')
            ->assertOk()
            ->assertSee('value="2026-10-20"', false)
            ->assertSee('value="10:00"', false)
            ->assertSee('name="phone"', false)
            ->assertSee('Send booking request');
    }

    public function test_valid_request_is_emailed_to_the_bookings_inbox(): void
    {
        Mail::fake();

        $this->post('/book', $this->valid(['email' => 'rudo@example.com', 'note' => 'A birthday']))
            ->assertRedirect('/book')
            ->assertSessionHas('booking_sent', fn ($b) => (bool) preg_match('/^RS-[A-HJ-NP-Z2-9]{5}$/', $b['ref']));

        Mail::assertSent(BookingRequest::class, function (BookingRequest $mail) {
            $b = $mail->booking;

            return $mail->hasTo('bookings@realityshift.co.zw')
                && $mail->hasReplyTo('rudo@example.com')
                && $b['name'] === 'Rudo Moyo'
                && $b['day'] === 'Wednesday 21 October'
                && $b['time'] === '11:00'
                && $b['people'] === 3
                && $b['total'] === '$45'
                && $b['phone'] === '077 123 4567'
                && $b['note'] === 'A birthday';
        });
    }

    public function test_every_bookings_address_gets_the_request(): void
    {
        Mail::fake();
        config(['shiftreality.booking.email' => ['bookings@realityshift.co.zw', 'tester@example.com']]);

        $this->post('/book', $this->valid())->assertRedirect('/book');

        Mail::assertSent(BookingRequest::class, fn (BookingRequest $mail) => $mail->hasTo('bookings@realityshift.co.zw') && $mail->hasTo('tester@example.com'));
    }

    public function test_email_renders_the_booking(): void
    {
        $mail = new BookingRequest([
            'ref' => 'RS-K7M2Q', 'received' => 'Tue 20 Oct at 08:00',
            'name' => 'Rudo Moyo', 'phone' => '077 123 4567', 'email' => null, 'note' => null,
            'day' => 'Wednesday 21 October', 'time' => '11:00', 'people' => 3, 'total' => '$45',
        ]);

        $mail->assertHasSubject('Booking request: Rudo Moyo, Wednesday 21 October at 11:00, 3 guests (RS-K7M2Q)');
        $mail->assertSeeInHtml('Rudo Moyo');
        $mail->assertSeeInHtml('Confirm on WhatsApp');
        // WhatsApp opens on their number with the confirmation written out
        $mail->assertSeeInHtml('https://wa.me/263771234567?text=Hi%20Rudo%2C%20this%20is%20Reality%20Shift.', false);
        $mail->assertSeeInText('Total:  $45');
        $mail->assertSeeInText('Reference RS-K7M2Q', false);
    }

    public function test_confirmation_shows_after_sending(): void
    {
        Mail::fake();

        $this->followingRedirects()->post('/book', $this->valid())
            ->assertOk()
            ->assertSee('Request sent')
            ->assertSee('Thanks, Rudo.', false)
            ->assertDontSee('Send booking request');
    }

    public function test_a_failed_send_comes_back_with_the_answers(): void
    {
        Mail::shouldReceive('to')->andThrow(new \RuntimeException('SMTP down'));

        $this->from('/book')->post('/book', $this->valid())
            ->assertRedirect('/book')
            ->assertSessionHasErrors('send')
            ->assertSessionHasInput('phone', '077 123 4567');
    }

    public function test_home_card_link_opens_on_the_contact_step(): void
    {
        $this->get('/book?date=2026-10-21&time=11:00&people=4&name=Rudo')
            ->assertOk()
            ->assertSee('data-start-step="3"', false)
            ->assertSee('value="Rudo"', false);
    }

    public function test_closed_day_is_sent_back(): void
    {
        config(['shiftreality.hours.1' => null]);   // Monday closed

        $this->from('/book')->post('/book', $this->valid(['date' => '2026-10-26', 'time' => '12:00']))
            ->assertRedirect('/book')->assertSessionHasErrors('time');
    }

    public function test_more_people_than_the_cap_is_refused(): void
    {
        $this->from('/book')->post('/book', $this->valid([
            'people' => config('shiftreality.booking.max_people') + 1,
        ]))->assertSessionHasErrors('people');
    }

    public function test_name_and_phone_are_required(): void
    {
        $this->from('/book')->post('/book', $this->valid(['name' => '', 'phone' => '']))
            ->assertSessionHasErrors(['name', 'phone']);
    }

    public function test_a_bad_phone_or_email_is_refused(): void
    {
        $this->from('/book')->post('/book', $this->valid(['phone' => 'call me', 'email' => 'not-an-email']))
            ->assertSessionHasErrors(['phone', 'email']);
    }
}
