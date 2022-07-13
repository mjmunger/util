<?php

namespace ByteValueInterpreter;

use hphio\util\ByteValueInterpreter\HumanReadable;
use PHPUnit\Framework\TestCase;

class HumanReadableTest extends TestCase
{

    /**
     * @return void
     * @dataProvider providerTestGetBytes
     */
    public function testGetBytes($iniValue, $expectedValue)
    {
        $obj = new HumanReadable();
        $this->assertEquals($expectedValue, $obj->getBytes($iniValue));

    }

    public function providerTestGetBytes()
    {
        return [
            ['8M', 8388608],
            ['8m', 8388608],
            ['8k', 8192],
            ['8K', 8192],
            ['8G', 8589934592],
            ['8g', 8589934592],
        ];
    }
}
