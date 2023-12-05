<?php

namespace hphio\util\TestScope;

use hphio\util\ClassReader\ClassReader;
use hphio\util\Helpers\ShellExec;
use League\Container\Container;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionClass;
use SimpleXMLElement;

class ChangedFiles
{
    protected ?Container $container = null;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \ReflectionException
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function getChangedNamespaces(string $targetBranch): array
    {
        $changes = $this->diffFileswith($targetBranch);
        return $this->getNamespaces($changes);

    }

    protected function diffFilesWith(string $targetBranch)
    {
        $shellEx = $this->container->get(ShellExec::class);
        $cmd = "git diff HEAD {$targetBranch} --name-only";
        return $shellEx->getStdOut();
    }

    /**
     * @param $changes
     *
     * @return array
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    protected function getNamespaces($changes): array
    {
        $attribNameSpaces = [];
        $changedFiles = explode(PHP_EOL, $changes);
        foreach ($changedFiles as $file) {
            if (!file_exists($file)) {
                continue;
            }
            $path = getcwd() . '/' . $file;
            var_dump($file, file_exists($file), $path);
            $reader = $this->container->get(ClassReader::class);
            $reader->analyze($path);
            $class = new ReflectionClass($reader->fullClassPath());
            $attribNameSpaces[] = $class->getNamespaceName();
        }

        return $attribNameSpaces;
    }

    protected function buildXml(string $sourceXML, array $namespaces): SimpleXMLElement
    {
        $xml = simplexml_load_file($sourceXML);

        if (isset($xml->testsuites)) unset($xml->testsuites);
        $suites = $xml->addChild('testsuites');

        foreach ($namespaces as $namespace) {
            $directory = str_replace('\\', '/', $namespace);
            $suite = $suites->addChild('testsuite', $directory);
            $suite->addAttribute('name', str_replace('\\','-', $namespace));
            $dir = $suite->addChild('directory', $directory);
            $dir->addAttribute('suffix', 'Test.php');
        }
        return $xml;
    }

    public function generateXML(string $sourceXML, string $targetBranch, string $outputPath): void
    {
        $namespaces = $this->getChangedNamespaces($targetBranch);
        $xml = $this->buildXml($sourceXML, $namespaces);
        $dom = dom_import_simplexml($xml)->ownerDocument;
        $dom->formatOutput = true;
        $dom->save($outputPath);
    }
}
