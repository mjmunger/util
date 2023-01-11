<?php
/**
 * @namspace hphio\util
 * @name \hphio\util\PeopleTime\PeopleTime
 * Summary: #$END$#
 *
 * Date: 2023-01-11
 * Time: 1:02 PM
 *
 * @author Michael Munger <mj@hph.io>
 * @copyright (c) 2023 High Powered Help, Inc. All Rights Reserved.
 */

namespace hphio\util\PeopleTime;

use DateTime;

class PeopleTime
{

    public static function calculate($endTime, $startTime = false): string
    {
        if(!$startTime) $startTime = (new DateTime())->getTimestamp();
        $difference = $endTime - $startTime;
        if($difference < 0) throw new \Exception("End timestamp cannot be before the starting timestamp.", 501);

        $start = new DateTime('@'.$startTime);
        $end = new DateTime('@'.$endTime);

        $period = PeopleTimePeriodFactory::getScopedPeriod($start, $end);
        return $period->getPeopleTime();
    }
}
