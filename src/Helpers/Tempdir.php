<?php

namespace hphio\util\Helpers;

class Tempdir
{
    public function create() : string {
        $dir = tempnam(sys_get_temp_dir(), 'dir_');
        unlink($dir);
        mkdir($dir, 0777, true);
        return $dir;
    }
}
