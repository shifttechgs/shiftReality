<?php

namespace App\Support;

use Carbon\CarbonImmutable;

/**
 * The venue's weekly opening hours, evaluated in its own timezone:
 * live open/closed status, grouped rows for display, and schema.org specs.
 *
 * $week is keyed by ISO weekday (1 = Monday … 7 = Sunday); each value is
 * ['HH:MM', 'HH:MM'] or null when closed. Hours never cross midnight.
 */
class OpeningHours
{
    private const DAYS = [
        1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday',
        5 => 'Friday', 6 => 'Saturday', 7 => 'Sunday',
    ];

    /** @param array<int, array{0: string, 1: string}|null> $week */
    public function __construct(
        private array $week,
        private string $timezone,
    ) {}

    public static function fromConfig(): self
    {
        return new self(config('shiftreality.hours'), config('shiftreality.timezone'));
    }

    /** @return array{open: bool, label: string, detail: string} */
    public function status(?CarbonImmutable $at = null): array
    {
        $at    = $this->local($at);
        $today = $this->hoursFor($at->dayOfWeekIso);
        $time  = $at->format('H:i');

        if ($today && $time >= $today[0] && $time < $today[1]) {
            return ['open' => true, 'label' => 'Open now', 'detail' => "Closes at {$today[1]}"];
        }

        // Next opening: later today, or the next open day this week
        for ($offset = 0; $offset <= 7; $offset++) {
            $day   = $at->addDays($offset);
            $hours = $this->hoursFor($day->dayOfWeekIso);

            if (! $hours || ($offset === 0 && $time >= $hours[0])) {
                continue;
            }

            $when = match ($offset) {
                0       => 'today',
                1       => 'tomorrow',
                default => self::DAYS[$day->dayOfWeekIso],
            };

            return ['open' => false, 'label' => 'Closed now', 'detail' => "Opens {$when} at {$hours[0]}"];
        }

        return ['open' => false, 'label' => 'Closed', 'detail' => 'Check back soon'];
    }

    /**
     * Consecutive days with identical hours collapse into one row.
     *
     * @return list<array{days: string, time: string, today: bool, closed: bool}>
     */
    public function rows(?CarbonImmutable $at = null): array
    {
        $todayIso = $this->local($at)->dayOfWeekIso;
        $groups   = [];

        foreach (array_keys(self::DAYS) as $iso) {
            $hours = $this->hoursFor($iso);
            $last  = array_key_last($groups);

            if ($last !== null && $groups[$last]['hours'] === $hours) {
                $groups[$last]['to'] = $iso;
                continue;
            }

            $groups[] = ['from' => $iso, 'to' => $iso, 'hours' => $hours];
        }

        return array_map(fn (array $g) => [
            'days'   => $g['from'] === $g['to']
                ? self::DAYS[$g['from']]
                : self::DAYS[$g['from']].' – '.self::DAYS[$g['to']],
            'time'   => $g['hours'] ? "{$g['hours'][0]} – {$g['hours'][1]}" : 'Closed',
            'today'  => $todayIso >= $g['from'] && $todayIso <= $g['to'],
            'closed' => $g['hours'] === null,
        ], $groups);
    }

    /** schema.org OpeningHoursSpecification entries, one per distinct set of hours. */
    public function schema(): array
    {
        $specs = [];

        foreach (self::DAYS as $iso => $name) {
            if (! $hours = $this->hoursFor($iso)) {
                continue;
            }

            $key = implode('-', $hours);
            $specs[$key] ??= [
                '@type'     => 'OpeningHoursSpecification',
                'dayOfWeek' => [],
                'opens'     => $hours[0],
                'closes'    => $hours[1],
            ];
            $specs[$key]['dayOfWeek'][] = "https://schema.org/{$name}";
        }

        return array_values($specs);
    }

    private function hoursFor(int $iso): ?array
    {
        return $this->week[$iso] ?? null;
    }

    private function local(?CarbonImmutable $at): CarbonImmutable
    {
        return ($at ?? CarbonImmutable::now())->setTimezone($this->timezone);
    }
}
