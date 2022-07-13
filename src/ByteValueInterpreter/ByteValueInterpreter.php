<?php

namespace hphio\util\ByteValueInterpreter;

abstract class ByteValueInterpreter
{
    abstract public function getBytes($iniValue) : int ;
}
