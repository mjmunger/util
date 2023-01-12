<?php
/**
 * @namspace hphio\util\PeopleTime
 * @name PeopleSeconds
 * Summary: Represents a second.
 *
 * Date: 2023-01-11
 * Time: 4:37 PM
 *
 * @author Michael Munger <mj@hph.io>
 * @copyright (c) 2023 High Powered Help, Inc. All Rights Reserved.
 */

namespace hphio\util\PeopleTime;

class PeopleSeconds extends PeoplePeriod
{
    public function getPeopleTime(): string
    {
        return $this->formatInterval($this->interval->s, 'second');
    }
}
