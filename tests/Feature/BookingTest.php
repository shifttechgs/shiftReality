<?php

namespace Tests\Feature;

use Carbon\CarbonImmutable;
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

    public function test_booking_page_lists_days_and_times(): void
    {
        $this->get('/book')
            ->assertOk()
            ->assertSee('value="2026-10-20"', false)
            ->assertSee('value="10:00"', false)
            ->assertSee('Send on WhatsApp');
    }

    public function test_valid_request_opens_whatsapp_with_the_booking(): void
    {
        $response = $this->post('/book', [
            'date' => '2026-10-21', 'time' => '11:00', 'people' => 3, 'name' => 'Rudo',
        ]);

        $url = $response->headers->get('Location');
        $this->assertStringStartsWith('https://wa.me/'.config('shiftreality.contact.whatsapp').'?text=', $url);

        $text = rawurldecode(explode('?text=', $url, 2)[1]);
        $this->assertStringContainsString('Name: Rudo', $text);
        $this->assertStringContainsString('Day: Wednesday 21 October', $text);
        $this->assertStringContainsString('Time: 11:00', $text);
        $this->assertStringContainsString('People: 3', $text);
        $this->assertStringContainsString('Total: $45', $text);
    }

    public function test_closed_day_is_sent_back(): void
    {
        $this->from('/book')->post('/book', [
            'date' => '2026-10-26', 'time' => '12:00', 'people' => 2, 'name' => 'Rudo',
        ])->assertRedirect('/book')->assertSessionHasErrors('time');
    }

    public function test_more_people_than_the_cap_is_refused(): void
    {
        $this->from('/book')->post('/book', [
            'date' => '2026-10-21', 'time' => '11:00',
            'people' => config('shiftreality.booking.max_people') + 1, 'name' => 'Rudo',
        ])->assertSessionHasErrors('people');
    }

    public function test_name_is_required(): void
    {
        $this->from('/book')->post('/book', [
            'date' => '2026-10-21', 'time' => '11:00', 'people' => 2,
        ])->assertSessionHasErrors('name');
    }
}
