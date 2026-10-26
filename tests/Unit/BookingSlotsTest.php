<?php

namespace Tests\Unit;

use App\Support\BookingSlots;
use App\Support\OpeningHours;
use Carbon\CarbonImmutable;
use PHPUnit\Framework\TestCase;

class BookingSlotsTest extends TestCase
{
    private BookingSlots $slots;

    protected function setUp(): void
    {
        parent::setUp();

        // Mirrors config/shiftreality.php: Mon closed, Tue–Fri 10–19, Sat–Sun 09–20;
        // hourly slots, last entry an hour before closing, two weeks ahead
        $weekday = ['10:00', '19:00'];
        $weekend = ['09:00', '20:00'];

        $this->slots = new BookingSlots(
            new OpeningHours(
                [1 => null, 2 => $weekday, 3 => $weekday, 4 => $weekday, 5 => $weekday, 6 => $weekend, 7 => $weekend],
                'Africa/Harare',
            ),
            60,
            60,
            14,
        );
    }

    /** 2026-10-20 is a Tuesday. Times are Harare local time. */
    private function at(string $datetime): CarbonImmutable
    {
        return CarbonImmutable::parse($datetime, 'Africa/Harare');
    }

    public function test_covers_the_days_ahead_from_today(): void
    {
        $days = $this->slots->days($this->at('2026-10-20 08:00'));

        $this->assertCount(14, $days);
        $this->assertSame('2026-10-20', $days[0]['date']);
        $this->assertTrue($days[0]['today']);
        $this->assertFalse($days[1]['today']);
        $this->assertSame(2, $days[0]['iso']);   // Tuesday
    }

    public function test_hourly_slots_until_an_hour_before_closing(): void
    {
        $days = $this->slots->days($this->at('2026-10-20 08:00'));

        $this->assertSame(['10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00'], $days[0]['slots']);
        $this->assertSame('09:00', $days[4]['slots'][0]);   // Saturday opens earlier
        $this->assertSame('19:00', end($days[4]['slots']));
    }

    public function test_closed_monday_has_no_slots(): void
    {
        $days = $this->slots->days($this->at('2026-10-20 08:00'));

        $this->assertSame([], $days[6]['slots']);   // 2026-10-26
    }

    public function test_today_only_offers_slots_that_have_not_started(): void
    {
        $days = $this->slots->days($this->at('2026-10-20 14:30'));

        $this->assertSame(['15:00', '16:00', '17:00', '18:00'], $days[0]['slots']);
    }

    public function test_availability_checks_the_day_and_the_time(): void
    {
        $now = $this->at('2026-10-20 14:30');

        $this->assertTrue($this->slots->isAvailable('2026-10-21', '10:00', $now));
        $this->assertFalse($this->slots->isAvailable('2026-10-20', '11:00', $now));   // already started
        $this->assertFalse($this->slots->isAvailable('2026-10-26', '12:00', $now));   // Monday
        $this->assertFalse($this->slots->isAvailable('2026-10-21', '10:30', $now));   // not a slot
        $this->assertFalse($this->slots->isAvailable('2026-12-01', '10:00', $now));   // too far ahead
    }
}
