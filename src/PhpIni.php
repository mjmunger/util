<?php

/**
 * Wrapper for ini_get().
 * https://www.php.net/manual/en/function.ini-get.php
 *
 */

namespace hphio\util;

class PhpIni
{
    /**
     * @param string $option
     * @return false|string
     * @codeCoverageIgnore
     */
    public function getOption(string $option) {
        return ini_get($option);
    }
}
