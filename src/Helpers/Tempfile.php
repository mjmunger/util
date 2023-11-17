<?php

namespace hphio\util\Helpers;

/**
 * @codeCoverageIgnore
 */
class Tempfile
{
    /**
     * Wrapper for temporary file creation that creates a file in the system temp directory.
     *
     * Usage:
     *   $path * $container->get(Tempfile)->create();
     *   unlink($path);
     *
     * @return string
     */
    public function create(): string
    {
        return tempnam(sys_get_temp_dir(), 'tmp_');
    }
}
