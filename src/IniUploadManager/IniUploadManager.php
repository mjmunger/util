<?php

namespace hphio\util\IniUploadManager;

use hphio\util\ByteValueInterpreter\ByteValueFactory;
use hphio\util\PhpIni;
use League\Container\Container;

class IniUploadManager
{

    protected ?Container $container = null;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Get the maximum upload size
     * @return int
     */
    public function getUploadLimit() :int {
        return min($this->getMaxUpload(), $this->getMaxPost());
    }

    /**
     * Get the maximum POST size for uploads
     * @return int
     */
    public function getMaxPost() : int {
        $value = $this->container->get(PhpIni::class)->getOption('post_max_size');
        return $this->getBytes($value);
    }

    /**
     * Return the max upload size, which is the less of getPostLimit() and getUploadLimit()
     * @return int
     */
    public function getMaxUpload() : int {
        $value = $this->container->get(PhpIni::class)->getOption('upload_max_filesize');
        return $this->getBytes($value);
    }

    /**
     * Convert human readable values to bytes.
     */

    private function getBytes($value)
    {
        $interpreter = ByteValueFactory::getByteInterpreter($this->container, $value);
        return $interpreter->getBytes($value);
    }

    /**
     * Determine if a given size will exceed the upload value (violates post_max_size or upload_max_filesize).
     * @param $value
     * @return void
     */
    public function isTooBig(int $value) : bool {
        return ($value > $this->getUploadLimit());

    }

}
