<?php

namespace tests\TestScope;

use hphio\util\ClassReader\ClassReader;
use hphio\util\Helpers\ShellExec;
use hphio\util\TestScope\ChangedFiles;
use League\Container\Container;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionClass;
use SimpleXMLElement;

class ChangedFilesTest extends TestCase
{
    public function testConstruct()
    {
        $container = new Container();
        $diff = new ChangedFiles($container);

        $class = new ReflectionClass($diff);
        $prop = $class->getProperty('container');
        $prop->setAccessible(true);
        $this->assertSame($container, $prop->getValue($diff));
    }

    /**
     * @param Container $container
     * @param string    $targetBranch
     * @param string    $expectedChanges
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     * @dataProvider providerTestDiffFilesWith
     */
    public function testDiffFilesWith(Container $container, string $targetBranch, string $expectedChanges)
    {
        $diff = $container->get(ChangedFiles::class);
        $reflectedClass = new ReflectionClass($diff);
        $method = $reflectedClass->getMethod('diffFilesWith');
        $method->setAccessible(true);
        $changes = $method->invoke($diff, $targetBranch);
        $this->assertSame($expectedChanges, $changes);
    }

    /**
     * @param Container $container
     * @param string    $targetBranch
     * @param string    $mockChanges
     * @param array     $expectedNameSpaces
     *
     * @return void
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \ReflectionException
     * @dataProvider providerTestGetNamespaces
     */
    public function testGetNamespaces(Container $container, string $targetBranch, string $mockChanges, array $expectedNameSpaces)
    {

        $diff = $container->get(ChangedFiles::class);
        $reflection = new ReflectionClass($diff);

        $method = $reflection->getMethod('getNamespaces');
        $method->setAccessible(true);
        $namespaces = $method->invoke($diff, $mockChanges);

        $this->assertSame($expectedNameSpaces, $namespaces);
    }

    /**
     * @param Container         $container
     * @param                   $sourceXML
     * @param                   $namespaces
     * @param \SimpleXMLElement $expectedXml
     *
     * @return void
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \ReflectionException
     * @dataProvider providerTestBuildXML
     */
    public function testBuildXML(Container $container, $sourceXML, $namespaces, SimpleXMLElement $expectedXml)
    {

        $diff = $container->get(ChangedFiles::class);
        $reflection = new ReflectionClass($diff);
        $method = $reflection->getMethod('buildXml');
        $method->setAccessible(true);

        /** @var \SimpleXMLElement $xml */
        $xml = $method->invoke($diff, $sourceXML, $namespaces);

        $this->assertEquals($expectedXml->testsuites->testsuite->directory->count(), $xml->testsuites->testsuite->directory->count());
        $this->assertEquals($expectedXml->testsuites, $xml->testsuites);
        $this->assertEquals($expectedXml->testsuites->directory, $xml->testsuites->directory);
    }

    public function providerTestDiffFilesWith(): array
    {
        return [
            $this->diffDev()
        ];
    }

    private function diffDev(): array
    {

        $container = new Container();
        $container->add(ClassReader::class);
        $container->add(ChangedFiles::class)->addArgument($container);


        $shellOutput = [];
        $shellOutput[] = 'tests/TestScope/fixtures/Bar/BarClass.php';
        $shellOutput[] = 'tests/TestScope/fixtures/Baz/BazClass.php';
        $shellOutput[] = 'tests/TestScope/fixtures/Zorg/ZorgClass.php';
        $shellOutput[] = ''; //Shell exec seems to end with a \n. Keep this here.

        $expectedChanges = implode("\n", $shellOutput);
        $mockShell = $this->getMockBuilder(ShellExec::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getStdout', 'exec'])
            ->getMock();

        $mockShell->expects($this->once())
            ->method('exec')
            ->with("git diff HEAD origin/dev --name-only");

        $mockShell->expects($this->once())
            ->method('getStdout')
            ->willReturn($expectedChanges);

        $container->add(ShellExec::class, $mockShell);

        $targetBranch = 'origin/dev';

        return [$container, $targetBranch, $expectedChanges];
    }

    public function providerTestBuildXML(): array
    {
        return [
            $this->diffedXML()
        ];
    }

    private function diffedXML(): array
    {

        $container = new Container();
        $container->add(ClassReader::class);
        $container->add(ChangedFiles::class)->addArgument($container);


        $shellOutput = [];
        $shellOutput[] = 'tests/TestScope/fixtures/Bar/BarClass.php';
        $shellOutput[] = 'tests/TestScope/fixtures/Baz/BazClass.php';
        $shellOutput[] = 'tests/TestScope/fixtures/Zorg/ZorgClass.php';

        $expectedChanges = implode("\n", $shellOutput);
        $mockShell = $this->getMockBuilder(ShellExec::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getStdout'])
            ->getMock();

        $mockShell->method('getStdout')->willReturn($expectedChanges);

        $container->add(ShellExec::class, $mockShell);

        $expectedPhpUnitXmlPath = dirname(__FILE__) . "/fixtures/phpunit/phpunit-fixture.xml";
        $this->assertFileExists($expectedPhpUnitXmlPath);

        $expectedXml = simplexml_load_file($expectedPhpUnitXmlPath);

        $outputPath = dirname(__FILE__) . "/output/phpunit.xml";

        if (file_exists($outputPath)) unlink($outputPath);

        $this->assertFileDoesNotExist($outputPath);

        $targetBranch = 'origin/dev';

        $sourceXML = dirname(__FILE__) . "/fixtures/phpunit-docker.xml";
        $this->assertFileExists($sourceXML);

        $namespaces = [];
        $namespaces[] = 'tests\TestScope\fixtures\Bar';
        $namespaces[] = 'tests\TestScope\fixtures\Baz';
        $namespaces[] = 'tests\TestScope\fixtures\Zorg';

        return [$container, $sourceXML, $namespaces, $expectedXml];
    }

    public function providerTestGetNamespaces(): array
    {
        return [
            $this->nsdiff()
        ];
    }

    private function nsdiff(): array
    {

        $container = new Container();
        $container->add(ClassReader::class);
        $container->add(ChangedFiles::class)->addArgument($container);


        $shellOutput = [];
        $shellOutput[] = 'tests/TestScope/fixtures/Bar/BarClass.php';
        $shellOutput[] = 'tests/TestScope/fixtures/Baz/BazClass.php';
        $shellOutput[] = 'tests/TestScope/fixtures/Zorg/ZorgClass.php';
        $shellOutput[] = '';

        $mockChanges = implode("\n", $shellOutput);
        $mockShell = $this->getMockBuilder(ShellExec::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getStdout', 'exec'])
            ->getMock();

        $mockShell->expects($this->once())
            ->method('exec')
            ->with("git diff HEAD origin/dev --name-only");

        $mockShell->expects($this->once())
            ->method('getStdout')
            ->willReturn($mockChanges);

        $container->add(ShellExec::class, $mockShell);

        $targetBranch = 'origin/dev';
        $expectedNameSpaces = [];
        $expectedNameSpaces[] = 'tests\TestScope\fixtures\Bar';
        $expectedNameSpaces[] = 'tests\TestScope\fixtures\Baz';
        $expectedNameSpaces[] = 'tests\TestScope\fixtures\Zorg';

        return [$container, $targetBranch, $mockChanges, $expectedNameSpaces];

    }

    /**
     * @param string $sourceXML
     * @param string $targetBranch
     * @param string $outputPath
     * @param string $expectedFile
     *
     * @return void
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @dataProvider providerTestGenerateXML
     */
    public function testGenerateXML(Container $container, string $sourceXML, string $targetBranch, string $outputPath, string $expectedFile) {

        $this->assertFileExists($sourceXML);
        $diff = $container->get(ChangedFiles::class);
        $diff->generateXML($sourceXML, $targetBranch, $outputPath);

        $this->assertFileExists($outputPath);
        $this->assertFileExists($expectedFile);

        $this->assertEquals(simplexml_load_file($expectedFile), simplexml_load_file($outputPath));
    }

    public function providerTestGenerateXML(): array
    {
        return [
            $this->fullTest()
        ];
    }

    private function fullTest(): array
    {
        $container = new Container();
        $container->add(ClassReader::class);
        $container->add(ChangedFiles::class)->addArgument($container);


        $shellOutput = [];
        $shellOutput[] = 'tests/TestScope/fixtures/Bar/BarClass.php';
        $shellOutput[] = 'tests/TestScope/fixtures/Baz/BazClass.php';
        $shellOutput[] = 'tests/TestScope/fixtures/Zorg/ZorgClass.php';

        $expectedChanges = implode("\n", $shellOutput);
        $mockShell = $this->getMockBuilder(ShellExec::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getStdout'])
            ->getMock();

        $mockShell->method('getStdout')->willReturn($expectedChanges);

        $container->add(ShellExec::class, $mockShell);

        $expectedPhpUnitXmlPath = dirname(__FILE__) . "/fixtures/phpunit/phpunit-fixture.xml";
        $this->assertFileExists($expectedPhpUnitXmlPath);

        $expectedXml = simplexml_load_file($expectedPhpUnitXmlPath);

        $outputPath = dirname(__FILE__) . "/output/phpunit.xml";

        if (file_exists($outputPath)) unlink($outputPath);

        $this->assertFileDoesNotExist($outputPath);

        $targetBranch = 'origin/dev';

        $sourceXML = dirname(__FILE__) . "/fixtures/phpunit-docker.xml";
        $this->assertFileExists($sourceXML);


        return [$container, $sourceXML, $targetBranch, $outputPath, $expectedPhpUnitXmlPath];
    }

    /**
     * @param \League\Container\Container $container
     * @param string                      $targetBranch
     * @param string                      $mockChanges
     * @param array                       $expectedNameSpaces
     *
     * @return void
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \ReflectionException
     * @dataProvider providerTestGetNamespacesExceptions
     */
    public function testGetNamespacesExceptions(Container $container, string $targetBranch, $mockChanges, \Exception $expectedException)
    {

        $diff = $container->get(ChangedFiles::class);
        $reflection = new ReflectionClass($diff);

        $method = $reflection->getMethod('getNamespaces');
        $method->setAccessible(true);
        $this->expectExceptionObject($expectedException);
        $namespaces = $method->invoke($diff, $mockChanges);
    }

    public function providerTestGetNamespacesExceptions(): array
    {
        return [
            $this->noChangedFiles()
        ];
    }

    private function noChangedFiles():array
    {

        $container = new Container();
        $container->add(ClassReader::class);
        $container->add(ChangedFiles::class)->addArgument($container);


        $shellOutput = null;

        $mockChanges = null;
        $mockShell = $this->getMockBuilder(ShellExec::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getStdout', 'exec'])
            ->getMock();

        $mockShell->expects($this->once())
            ->method('exec')
            ->with("git diff HEAD origin/dev --name-only");

        $mockShell->expects($this->once())
            ->method('getStdout')
            ->willReturn($mockChanges);

        $container->add(ShellExec::class, $mockShell);

        $targetBranch = 'origin/dev';


        return [$container, $targetBranch, $mockChanges, new \Exception("No changed files found.")];
    }
}
