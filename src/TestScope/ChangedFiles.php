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
    protected ?ShellExec $shell = null;

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

    public function getRawDiff(string $targetBranch): string|null
    {
        return $this->diffFileswith($targetBranch);
    }

    protected function diffFilesWith(string $targetBranch)
    {
        $this->shell = $this->container->get(ShellExec::class);
        $cmd = "git diff HEAD {$targetBranch} --name-only";
        $this->shell->exec($cmd);
        return $this->shell->getStdOut();
    }

    /**
     * @param $changes
     *
     * @return array
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \ReflectionException
     * @throws \hphio\util\TestScope\NoChangedFilesException
     */
    protected function getNamespaces($changes): array
    {
        $attribNameSpaces = [];
        if (is_null($changes)) return throw new NoChangedFilesException("No changed files found.");
        $changedFiles = explode(PHP_EOL, $changes);
        foreach ($changedFiles as $file) {
            if (!$this->isSourceFile($file)) {
                continue;
            }
            $testPath = $this->findTestDirectory($file);

            $attribNameSpaces[] = $testPath;

//            if(!file_exists($testPath)) throw new \Exception("Could not find file $testPath");
//            $reader = $this->container->get(ClassReader::class);
//            $reader->analyze($testPath);
//            $class = new ReflectionClass($reader->fullClassPath());
        }

        return array_unique($attribNameSpaces);
    }

    protected function buildXml(string $sourceXML, array $namespaces): SimpleXMLElement
    {
        $xml = simplexml_load_file($sourceXML);

        if (isset($xml->testsuites)) unset($xml->testsuites);
        $suites = $xml->addChild('testsuites');

        foreach ($namespaces as $namespace) {
            $directory = str_replace('\\', '/', $namespace);
            $suite = $suites->addChild('testsuite');
            $suite->addAttribute('name', str_replace('\\', '-', $namespace));
            $dir = $suite->addChild('directory', $directory);
            $dir->addAttribute('suffix', 'Test.php');
        }
        return $xml;
    }

    /**
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \ReflectionException
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function generateXML(string $sourceXML, string $targetBranch, string $outputPath): void
    {
        $namespaces = $this->getChangedNamespaces($targetBranch);
        $xml = $this->buildXml($sourceXML, $namespaces);
        $dom = dom_import_simplexml($xml)->ownerDocument;
        $dom->formatOutput = true;
        $dom->preserveWhiteSpace = false;
        $dom->save($outputPath);
    }

    private function isSourceFile(string $file): bool
    {
        if (!str_contains($file, '/')) return false;
        $parts = explode('/', $file);
        if (count($parts) < 2) return false;
        if ($parts[0] == 'src') return true;
        return false;
    }

    private function findTestDirectory(string $file): string
    {
        $parts = explode('/', $file);
        $parts[0] = 'tests';
        array_pop($parts);
        $path = implode('/', $parts);
        if (!is_dir($path)) {
            throw new TestNotFoundException("Could not find test directory: $path");
        }
        return str_replace('/', '\\', $path);
    }

    public function getShell(): ShellExec|null
    {
        return $this->shell;
    }
}
