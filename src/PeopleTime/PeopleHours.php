<?php
/**
 * @namspace hphio\util\PeopleTime
 * @name PeopleHours
 * Summary: Represents an hour.
 *
 * Date: 2023-01-11
 * Time: 4:36 PM
 *
 * @author Michael Munger <mj@hph.io>
 * @copyright (c) 2023 High Powered Help, Inc. All Rights Reserved.
 */

namespace hphio\util\PeopleTime;

class PeopleHours extends PeoplePeriod
{
    public function getPeopleTime(): string
    {
        return $this->formatInterval($this->interval->h, 'hour');
    }
}
