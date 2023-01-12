<?php
/**
 * @namspace hphio\util\PeopleTime
 * @name PeopleTimePeriodFactory
 * Summary: #$END$#
 *
 * Date: 2023-01-11
 * Time: 4:11 PM
 *
 * @author Michael Munger <mj@hph.io>
 * @copyright (c) 2023 High Powered Help, Inc. All Rights Reserved.
 */

namespace hphio\util\PeopleTime;

use DateInterval;
use DateTime;

class PeopleTimePeriodFactory
{
    /**
     * This method relies on a cascading check of year, month, day, hour, minute, and second values.
     * The first non-zero value (checked from largest to smallest period of time) becomes the relative time.
     * @param DateTime $start
     * @param DateTime $end
     * @return ScopedPeriodInterface
     */
    public static function getScopedPeriod(DateTime $start, DateTime $end): ScopedPeriodInterface
    {
        $interval = $end->diff($start);

        if($interval->y > 0) return new PeopleYears($interval);
        if($interval->m > 0) return new PeopleMonths($interval);
        if($interval->d > 0) return new PeopleDays($interval);
        if($interval->h > 0) return new PeopleHours($interval);
        if($interval->i > 0) return new PeopleMinutes($interval);
        if($interval->s > 0) return new PeopleSeconds($interval);

    }
}
