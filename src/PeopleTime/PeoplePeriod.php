<?php
/**
 * @namspace hphio\util\PeopleTime
 * @name PeoplePeriod
 * Summary: #$END$#
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

    protected function formatInterval(int $interval, string $suffix) {
        if($interval > 1) $suffix .= 's';
        return sprintf("%s %s", $interval, $suffix);
    }
}
