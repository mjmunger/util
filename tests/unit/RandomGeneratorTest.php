<?php

namespace hphio\util;


/**
 * Tests to make sure we are getting random things from the random generator.
 *
 * Date: 9/17/18
 * Time: 3:01 PM
 * @author Michael Munger <mj@hph.io>
 */

use PHPUnit\Framework\TestCase;
//use \Exception;

class RandomGeneratorTest extends TestCase
{
    public function testUUIDv4() {
        $UUID = new \hphio\util\RandomGenerator();
        $buffer = [];
        for($x = 0; $x < 10; $x++) {
            $buffer[] = $UUID->uuidv4();
        }

        //Each result should be 36 characters long.
        foreach($buffer as $uuid) {
            $pattern = '/[a-fA-F0-9]{8}-[a-fA-F0-9]{4}-[a-fA-F0-9]{4}-[a-fA-F0-9]{4}-[a-fA-F0-9]{12}/m';
            $this->assertSame(1,preg_match($pattern, $uuid ));
        }

        //Make sure there are no duplicates.
        while(count($buffer) > 0) {
            $uuid = array_pop($buffer);
            $this->assertFalse(in_array($uuid,$buffer));
        }


    }
}