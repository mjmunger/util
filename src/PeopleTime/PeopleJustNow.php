<?php
/**
 * @namspace      hphio\util\PeopleTime
 * @name PeopleJustNow
 * Summary: #$END$#
 *
 * Date: 2023-08-16
 * Time: 3:18 PM
 *
 * @author        Michael Munger <mj@hph.io>
 * @copyright (c) 2023 High Powered Help, Inc. All Rights Reserved.
 */

namespace hphio\util\PeopleTime;

class PeopleJustNow extends PeoplePeriod
{

    public function getPeopleTime(): string
    {
        return "Just now";
    }
}
