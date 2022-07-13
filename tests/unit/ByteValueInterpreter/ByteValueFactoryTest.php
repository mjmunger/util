<?php

namespace ByteValueInterpreter;

use hphio\util\ByteValueInterpreter\ByteValueFactory;
use hphio\util\ByteValueInterpreter\HumanReadable;
use hphio\util\ByteValueInterpreter\RawInteger;
use League\Container\Container;
use PHPUnit\Framework\TestCase;

class ByteValueFactoryTest extends TestCase
{

    /**
     * @return void
     * @dataProvider providerTestGetByteInterpreter
     */
    public function testGetByteInterpreter($value, $expectedClass)
    {
        $container = new Container();
        $container->add(RawInteger::class);
        $container->add(HumanReadable::class);

        $obj = ByteValueFactory::getByteInterpreter($container, $value);

        $this->assertInstanceOf($expectedClass, $obj);
    }

    public function providerTestGetByteInterpreter()
    {
        return [
            ['8G', HumanReadable::class],
            ['8g', HumanReadable::class],
            ['8M', HumanReadable::class],
            ['8m', HumanReadable::class],
            ['8K', HumanReadable::class],
            ['8k', HumanReadable::class],
            ['8', RawInteger::class],
        ];
    }
}
