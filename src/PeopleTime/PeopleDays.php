<?php

/**
 * @namspace hphio\util\PeopleTime
 * @name PeopleDays
 * Summary: Represents a day.
 *
 * Date: 2023-01-11
 * Time: 4:35 PM
 *
 * @author Michael Munger <mj@hph.io>
 * @copyright (c) 2023 High Powered Help, Inc. All Rights Reserved.
 */

namespace hphio\util\PeopleTime;

class PeopleDays extends PeoplePeriod
{
    public function getPeopleTime(): string
    {
        return $this->formatInterval($this->interval->d, 'day');
    }
}

