<?php
/**
 * @namspace ${NAMESPACE}
 * @name PeopleTimeTest
 * Summary: #$END$#
 *
 * Date: 2023-01-11
 * Time: 1:02 PM
 *
 * @author Michael Munger <mj@hph.io>
 * @copyright (c) 2023 High Powered Help, Inc. All Rights Reserved.
 */

namespace unit\PeopleTime;

use DateInterval;
use DateTime;
use Exception;
use hphio\util\PeopleTime\PeopleTime;
use PHPUnit\Framework\TestCase;

class PeopleTimeTest extends TestCase
{
    /**
     * @param $interval
     * @param $expectedString
     * @return void
     * @dataProvider providerTestPeopleTime
     * @throws \Exception
     */
    public function testPeopleTime($startTime, $endTime, $expectedString): void
    {
        $this->assertSame($expectedString, PeopleTime::calculate($endTime, $startTime));
    }

    public function providerTestPeopleTime(): array
    {
        return [
            $this->yearsExample(),
            $this->oneYearExample(),
            $this->monthsExample(),
            $this->oneMonth(),
            $this->days29InJan(),
            $this->days29InFeb(),
            $this->daysExample(),
            $this->oneDayExample(),
            $this->hoursExample(),
            $this->oneHourExample(),
            $this->minutesExample(),
            $this->oneMinuteExample(),
            $this->secondsExample(),
            $this->oneSecondExample(),
            $this->nowTime()
        ];
    }

    private function yearsExample(): array
    {
        $startTime = 1672578977;
        $endTime = 2018748556;
        $expectedString = '10 years';
        return [$startTime, $endTime, $expectedString];
    }

    private function oneYearExample(): array
    {
        $startTime = 1672578977;
        $endTime = 1706883438;
        $expectedString = '1 year';
        return [$startTime, $endTime, $expectedString];
    }

    private function monthsExample(): array
    {
        $startTime = 1672578977;
        $endTime = 1703302156;
        $expectedString = '11 months';
        return [$startTime, $endTime, $expectedString];
    }

    private function daysExample(): array
    {
        $startTime = 1675209600;
        $endTime = 1676470379;
        $expectedString = '14 days';
        return [$startTime, $endTime, $expectedString];
    }

    private function hoursExample(): array
    {
        $startTime = 1675209600;
        $endTime = 1675260779;
        $expectedString = '14 hours';

        return [$startTime, $endTime, $expectedString];
    }

    private function minutesExample(): array
    {
        $startTime = 1675209600;
        $endTime = 1675210379;
        $expectedString = '12 minutes';

        return [$startTime, $endTime, $expectedString];
    }

    private function secondsExample(): array
    {
        $startTime = 1675209600;
        $endTime = 1675209659;
        $expectedString = '59 seconds';

        return [$startTime, $endTime, $expectedString];
    }

    private function days29InJan(): array
    {
        $startTime = 1672531200;
        $endTime = 1675087979;
        $expectedString = '29 days';
        return [$startTime, $endTime, $expectedString];
    }

    private function days29InFeb(): array
    {
        $startTime = 1675209600;
        $endTime = 1677766379;
        $expectedString = '1 month';
        return [$startTime, $endTime, $expectedString];
    }

    private function oneMonth(): array
    {
        $startTime = 1675209600;
        $endTime = 1677679979;
        $expectedString = '1 month';
        return [$startTime, $endTime, $expectedString];
    }

    private function oneDayExample()
    {
        $startTime = 1672578977;
        $endTime = 1672669038;
        $expectedString = '1 day';
        return [$startTime, $endTime, $expectedString];
    }

    private function oneHourExample(): array
    {
        $startTime = 1675209600;
        $endTime = 1677679979;
        $expectedString = '1 month';
        return [$startTime, $endTime, $expectedString];
    }

    private function oneMinuteExample(): array
    {
        $startTime = 1675209600;
        $endTime = 1675209719;
        $expectedString = '1 minute';
        return [$startTime, $endTime, $expectedString];
    }

    private function oneSecondExample(): array
    {

        $startTime = 1675209600;
        $endTime = 1675209601;
        $expectedString = '1 second';
        return [$startTime, $endTime, $expectedString];
    }

    /**
     * @return array
     * Note: we only test for 1 minute because a race condition can happen if we test for "same second".
     */
    private function nowTime()
    {
        $start = new DateTime();
        $end = clone $start;
        $end->add(new DateInterval('PT1M'));

        $startTime = $start->getTimestamp();
        $endTime = $end->getTimestamp();
        $expectedString = '1 minute';
        return [$startTime, $endTime, $expectedString];
    }

    /**
     * @param $startTime
     * @param $endTime
     * @param $expectedException
     * @return void
     * @dataProvider providerTestExceptions
     * @throws Exception
     */
    public function testExceptions($startTime, $endTime, Exception $expectedException): void
    {

        $this->expectExceptionMessage($expectedException->getMessage());
        $this->expectExceptionCode($expectedException->getCode());
        PeopleTime::calculate($endTime, $startTime);
    }

    public function providerTestExceptions(): array
    {
        return [
            $this->endBeforeStart()
        ];
    }

    private function endBeforeStart(): array
    {
        $startTime = 1672578977;
        $endTime = 1672578976;
        $expectedException = new Exception("End timestamp cannot be before the starting timestamp.", 501);
        return [$startTime, $endTime, $expectedException];
    }
}
