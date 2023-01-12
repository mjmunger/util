<?php
/**
 * @namspace hphio\util\PeopleTime
 * @name ScopedPeriodInterface
 * Summary: Ensures that all period classes return a relative people time.
 *
 * Date: 2023-01-11
 * Time: 4:18 PM
 *
 * @author Michael Munger <mj@hph.io>
 * @copyright (c) 2023 High Powered Help, Inc. All Rights Reserved.
 */

namespace hphio\util\PeopleTime;

interface ScopedPeriodInterface
{
    public function getPeopleTime(): string;
}
