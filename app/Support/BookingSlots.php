<?php

namespace App\Support;

use Carbon\CarbonImmutable;

/**
 * Bookable entry slots, derived from the opening hours: every $slotMinutes from
 * opening until $lastEntryMinutes before closing, for the next $daysAhead days.
 * Today only offers slots that haven't started yet. All in the venue's timezone.
 */
class BookingSlots
{
    public function __construct(
        private OpeningHours $hours,
        private int $slotMinutes,
        private int $lastEntryMinutes,
        private int $daysAhead,
    ) {}

    public static function fromConfig(): self
    {
        return new self(
            OpeningHours::fromConfig(),
            (int) config('shiftreality.booking.slot_minutes'),
            (int) config('shiftreality.booking.last_entry_minutes'),
            (int) config('shiftreality.booking.days_ahead'),
        );
    }

    /**
     * One entry per day from today, closed days included (shown, not pickable).
     *
     * @return list<array{date: string, iso: int, today: bool, day: string, month: string, label: string, slots: list<string>}>
     */
    public function days(?CarbonImmutable $now = null): array
    {
        $now  = $this->local($now);
        $days = [];

        for ($offset = 0; $offset < $this->daysAhead; $offset++) {
            $date = $now->startOfDay()->addDays($offset);

            $days[] = [
                'date'    => $date->format('Y-m-d'),
                'iso'     => $date->dayOfWeekIso,   // lets the picker line days up under Mon … Sun
                'today'   => $offset === 0,
                'day'     => $date->format('j'),
                'month'   => $date->format('F'),
                'label'   => $date->format('l j F'),
                'slots'   => $this->slotsOn($date, $now),
            ];
        }

        return $days;
    }

    /** Whether "Y-m-d" + "H:i" is a slot someone can book right now. */
    public function isAvailable(string $date, string $time, ?CarbonImmutable $now = null): bool
    {
        foreach ($this->days($now) as $day) {
            if ($day['date'] === $date) {
                return in_array($time, $day['slots'], true);
            }
        }

        return false;
    }

    /** @return list<string> */
    private function slotsOn(CarbonImmutable $date, CarbonImmutable $now): array
    {
        if (! $hours = $this->hours->hoursFor($date->dayOfWeekIso)) {
            return [];
        }

        $slot   = $date->setTimeFromTimeString($hours[0]);
        $last   = $date->setTimeFromTimeString($hours[1])->subMinutes($this->lastEntryMinutes);
        $slots  = [];

        for (; $slot <= $last; $slot = $slot->addMinutes($this->slotMinutes)) {
            if ($slot > $now) {
                $slots[] = $slot->format('H:i');
            }
        }

        return $slots;
    }

    private function local(?CarbonImmutable $now): CarbonImmutable
    {
        return ($now ?? CarbonImmutable::now())->setTimezone($this->hours->timezone());
    }
}
