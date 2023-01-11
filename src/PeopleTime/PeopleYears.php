<?php
/**
 * @namspace hphio\util\PeopleTime
 * @name PeopleYears
 * Summary: #$END$#
 *
 * Date: 2023-01-11
 * Time: 4:28 PM
 *
 * @author Michael Munger <mj@hph.io>
 * @copyright (c) 2023 High Powered Help, Inc. All Rights Reserved.
 */

namespace hphio\util\PeopleTime;

use DateInterval;

class PeopleYears extends PeoplePeriod
{

    public function getPeopleTime(): string
    {
        return $this->formatInterval($this->interval->y, 'year');
    }
}
