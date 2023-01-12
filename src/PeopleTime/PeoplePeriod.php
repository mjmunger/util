<?php
/**
 * @namspace hphio\util\PeopleTime
 * @name PeoplePeriod
 * Summary: The PeoplePeriod class holds the interval we are working with, and implements ScopedPeriodInterface
 * to ensure the output of all PeoplePeriods conforms to the expected output.
 *
 * Date: 2023-01-11
 * Time: 4:29 PM
 *
 * @author Michael Munger <mj@hph.io>
 * @copyright (c) 2023 High Powered Help, Inc. All Rights Reserved.
 */

namespace hphio\util\PeopleTime;

use DateInterval;

abstract class PeoplePeriod implements ScopedPeriodInterface
{
    protected ?DateInterval $interval = null;

    /**
     * @param DateInterval $interval
     */
    public function __construct(DateInterval $interval)
    {
        $this->interval = $interval;
    }

    /**
     * Handles singular vs plural for the period name, and formats the desired output string.
     * @param int $interval
     * @param string $suffix
     * @return string
     */
    protected function formatInterval(int $interval, string $suffix): string
    {
        if($interval > 1) $suffix .= 's';
        return sprintf("%s %s", $interval, $suffix);
    }
}
