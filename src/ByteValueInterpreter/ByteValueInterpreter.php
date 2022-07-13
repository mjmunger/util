<?php

namespace hphio\util\ByteValueInterpreter;

abstract class ByteValueInterpreter
{
    /**
     * @param $iniValue
     * @return int
     * @codeCoverageIgnore
     */
    abstract public function getBytes($iniValue) : int ;
}
