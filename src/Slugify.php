<?php

namespace hphio\util;

class Slugify
{
    public static function getSlug(string $string) {
        return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $string), '-'));
    }
}