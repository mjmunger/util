<?php
/**
 * @namspace hphio\util\PeopleTime
 * @name PeopleMonths
 * Summary: Represents a month.
 *
 * Date: 2023-01-11
 * Time: 4:32 PM
 *
 * @author Michael Munger <mj@hph.io>
 * @copyright (c) 2023 High Powered Help, Inc. All Rights Reserved.
 */

namespace hphio\util\PeopleTime;

class PeopleMonths extends PeoplePeriod
{

    public function getPeopleTime(): string
    {
        return $this->formatInterval($this->interval->m, 'month');
    }
}
