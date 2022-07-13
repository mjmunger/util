<?php

namespace hphio\util\ByteValueInterpreter;

use League\Container\Container;

class ByteValueFactory
{

    public static function getByteInterpreter(Container $container, $value) {
        $value = trim($value);
        $last = strtolower($value[strlen($value)-1]);
        switch($last) {
            // The 'G' modifier is available
            case 'g':
            case 'm':
            case 'k':
                return $container->get(HumanReadable::class);
            default:
                return $container->get(RawInteger::class);
        }
    }

}
