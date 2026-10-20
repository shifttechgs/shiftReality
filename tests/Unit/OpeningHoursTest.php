<?php

namespace Tests\Unit;

use App\Support\OpeningHours;
use Carbon\CarbonImmutable;
use PHPUnit\Framework\TestCase;

class OpeningHoursTest extends TestCase
{
    private OpeningHours $hours;

    protected function setUp(): void
    {
        parent::setUp();

        // Mirrors config/shiftreality.php: Mon closed, Tue–Fri 10–19, Sat–Sun 09–20
        $weekday = ['10:00', '19:00'];
        $weekend = ['09:00', '20:00'];

        $this->hours = new OpeningHours(
            [1 => null, 2 => $weekday, 3 => $weekday, 4 => $weekday, 5 => $weekday, 6 => $weekend, 7 => $weekend],
            'Africa/Harare',
        );
    }

    /** 2026-10-20 is a Tuesday. Times are Harare local time. */
    private function at(string $datetime): CarbonImmutable
    {
        return CarbonImmutable::parse($datetime, 'Africa/Harare');
    }

    public function test_open_during_hours(): void
    {
        $status = $this->hours->status($this->at('2026-10-20 12:00'));

        $this->assertTrue($status['open']);
        $this->assertSame('Closes at 19:00', $status['detail']);
    }

    public function test_before_opening_says_today(): void
    {
        $status = $this->hours->status($this->at('2026-10-20 09:30'));

        $this->assertFalse($status['open']);
        $this->assertSame('Opens today at 10:00', $status['detail']);
    }

    public function test_closing_time_is_closed_and_points_to_tomorrow(): void
    {
        $status = $this->hours->status($this->at('2026-10-23 19:00')); // Friday

        $this->assertFalse($status['open']);
        $this->assertSame('Opens tomorrow at 09:00', $status['detail']);
    }

    public function test_skips_the_closed_monday(): void
    {
        $status = $this->hours->status($this->at('2026-10-25 21:00')); // Sunday night

        $this->assertSame('Opens Tuesday at 10:00', $status['detail']);
    }

    public function test_evaluates_in_venue_timezone_not_server_timezone(): void
    {
        // 08:30 UTC on a Tuesday is 10:30 in Harare (UTC+2) — open
        $status = $this->hours->status(CarbonImmutable::parse('2026-10-20 08:30', 'UTC'));

        $this->assertTrue($status['open']);
    }

    public function test_rows_group_matching_days_and_flag_today(): void
    {
        $rows = $this->hours->rows($this->at('2026-10-24 12:00')); // Saturday

        $this->assertSame(['Monday', 'Tuesday – Friday', 'Saturday – Sunday'], array_column($rows, 'days'));
        $this->assertSame(['Closed', '10:00 – 19:00', '09:00 – 20:00'], array_column($rows, 'time'));
        $this->assertSame([false, false, true], array_column($rows, 'today'));
    }

    public function test_schema_has_one_spec_per_distinct_hours(): void
    {
        $schema = $this->hours->schema();

        $this->assertCount(2, $schema);
        $this->assertCount(4, $schema[0]['dayOfWeek']);
        $this->assertSame('10:00', $schema[0]['opens']);
        $this->assertSame(['https://schema.org/Saturday', 'https://schema.org/Sunday'], $schema[1]['dayOfWeek']);
    }
}
