<?php

/**
 * @namspace Erc\Cli
 * @name ClassReader
 * Summary: #$END$#
 *
 * Date: 2023-02-20
 * Time: 3:31 PM
 *
 * @author Michael Munger <mj@hph.io>
 * @copyright (c) 2023 High Powered Help, Inc. All Rights Reserved.
 */

namespace hphio\util\ClassReader;

use Exception;
use ReflectionClass;
use ReflectionException;

class ClassReader
{

    protected ?bool $hasConstructor = null;
    protected ?string $header = null;
    protected ?string $targetFile = null;
    protected ?string $namespace = null;
    protected ?string $classname = null;

    public function analyze(string $path) {
        $this->targetFile = $path;
        if(!file_exists($this->targetFile)) throw new Exception(sprintf("Target file does not exist: %s", $this->targetFile), 500);
        $this->parseClassname();
        $this->parseNamespace();
    }

    private function parseClassname()
    {
        $this->classname =  $this->readHeader('class');
    }

    private function readHeader(string $firstWord)
    {
        $buffer = file($this->targetFile);
        $targetLine = "";
        foreach($buffer as $line) {
            $line = trim($line);
            if(!str_starts_with($line, $firstWord)) continue;
            if(str_starts_with($line, $firstWord)) $targetLine = $line;
        }

        $targetLine = $this->cleanLine($targetLine, $firstWord);
        return $targetLine;
    }

    private function parseNamespace()
    {
        $this->namespace = $this->readHeader('namespace');
    }

    public function classname() {
        return $this->classname;
    }

    public function namespace() {
        return $this->namespace;
    }

    /**
     * @param string $targetLine
     * @param string $firstWord
     * @return string
     */
    public function cleanLine(string $targetLine, string $firstWord): string
    {
        $targetLine = $this->removeFirstWord($targetLine, $firstWord);
        $targetLine = $this->removeOtherWords($targetLine);
        $targetLine = $this->removeSemiColon($targetLine);
        return $targetLine;
    }

    /**
     * @param string $targetLine
     * @param string $firstWord
     * @return string
     */
    public function removeFirstWord(string $targetLine, string $firstWord): string
    {
        $targetLine = substr($targetLine, strlen($firstWord));
        $targetLine = trim($targetLine);
        return $targetLine;
    }

    /**
     * @param string $targetLine
     * @return string
     */
    public function removeOtherWords(string $targetLine): string
    {
        $index = strpos($targetLine, ' ');
        if ($index > 0) $targetLine = substr($targetLine, 0, $index);
        return $targetLine;
    }

    private function removeSemiColon(string $targetLine)
    {
        if(str_ends_with($targetLine, ';')) $targetLine = substr($targetLine,0, -1);
        return $targetLine;
    }

    /**
     * @throws ReflectionException
     */
    public function hasConstructor(): bool
    {
        $reflection = new ReflectionClass($this->namespace . '\\' .  $this->classname);
        return $reflection->hasMethod('__construct');
    }

    /**
     * @throws ReflectionException
     */
    public function requiresContainer(): bool {
        $reflection = new ReflectionClass($this->namespace . '\\' .  $this->classname);
        $constructor = $reflection->getConstructor();
        if(is_null($constructor)) return false;
        foreach($constructor->getParameters() as $parameter) {
            if($parameter->name == 'container') return true;
        }
        return false;
    }

    public function isClass(): bool
    {
        $this->parseClassname();
        return (strlen($this->classname)>0);
    }

    public function fullClassPath() : string {
        return $this->namespace . '\\' . $this->classname;
    }
}
