<?php

namespace hphio\util\ByteValueInterpreter;

class HumanReadable extends ByteValueInterpreter
{
    public function getBytes($iniValue) : int {

        $last = strtolower($iniValue[strlen($iniValue)-1]);
        $value = intval(rtrim($iniValue, $last));

        switch($last) {
            // The 'G' modifier is available
            case 'g':
                $value *= 1024;
            case 'm':
                $value *= 1024;
            case 'k':
                $value *= 1024;
        }

        return $value;
    }
}
