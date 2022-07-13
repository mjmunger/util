<?php

namespace hphio\util\ByteValueInterpreter;

class RawInteger extends ByteValueInterpreter
{

    public function getBytes($iniValue) : int
    {
        return intval($iniValue);
    }
}
