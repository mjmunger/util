<?php

/**
 * @namspace hphio\util
 * @name PeopleTime
 * Summary: Class used to hold the calculate method, which gets the human-readable relative time from two timestamps.
 *
 * Date: 2023-01-11
 * Time: 1:02 PM
 *
 * @author Michael Munger <mj@hph.io>
 * @copyright (c) 2023 High Powered Help, Inc. All Rights Reserved.
 */

namespace hphio\util\PeopleTime;

use DateTime;
use Exception;

class PeopleTime
{

    /**
     * Calculates the human-readable relative time between two timestamps.
     * @param $endTime
     * @param $startTime
     * @return string
     * @throws Exception
     * @todo Modify this to support "in 5 days" for future time, and "5 days ago" for past time.
     */
    public static function calculate($endTime, $startTime = false): string
    {
        if(!$startTime) $startTime = (new DateTime())->getTimestamp();
        $difference = $endTime - $startTime;
        if($difference < 0) throw new Exception("End timestamp cannot be before the starting timestamp.", 501);

        $start = new DateTime('@'.$startTime);
        $end = new DateTime('@'.$endTime);

        $period = PeopleTimePeriodFactory::getScopedPeriod($start, $end);
        return $period->getPeopleTime();
    }
}
