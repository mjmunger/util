<?php
/**
 * @namspace      tests\PDF\Helpers
 * @name GhostScriptTest
 * Summary: #$END$#
 *
 * Date: 2023-11-17
 * Time: 11:25 AM
 *
 * @author        Michael Munger <mj@hph.io>
 * @copyright (c) 2023 High Powered Help, Inc. All Rights Reserved.
 */

namespace tests\PDF\Helpers;

use hphio\util\Exceptions\PackageNotInstalled;
use hphio\util\Helpers\ShellExec;
use hphio\util\PDF\Helpers\GhostScript;
use hphio\util\PDF\VersionParser;
use League\Container\Container;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class GhostScriptTest extends TestCase
{

    public function testDowngrade()
    {
        $container = new Container();
        $container->add(ShellExec::class);
        $container->add(VersionParser::class);
        $container->add(GhostScript::class)->addArgument($container);

        $shell = $container->get(GhostScript::class);
        $sourcePDFPath = dirname(__FILE__) . "/fixtures/pdf-v1.7.pdf";
        $this->assertFileExists($sourcePDFPath);

        $tempfile = tempnam(sys_get_temp_dir(), 'test_') . ".pdf";
        $shell->downgrade($sourcePDFPath, $tempfile);

        $this->assertFileExists($tempfile);

        $version = $container->get(VersionParser::class);
        $this->assertSame('1.4', $version->getVersion($tempfile));

        unlink($tempfile);

        $this->assertFileDoesNotExist($tempfile);
    }

    /**
     * @param Container $container
     *
     * @throws PackageNotInstalled
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @dataProvider providerTestConstruct
     */
    public function test__construct(Container $container)
    {
        $ghostscript = new GhostScript($container);
        $this->assertInstanceOf(GhostScript::class, $ghostscript);
        $reflection = new \ReflectionClass($ghostscript);
        $property = $reflection->getProperty('container');
        $property->setAccessible(true);
        $this->assertInstanceOf(Container::class, $property->getValue($ghostscript));
    }

    /**
     * @param Container $container
     * @param           $exception
     *
     * @return void
     * @dataProvider providerTestConstructExceptions
     */
    public function testConstructExceptions(Container $container, $exception)
    {
        $this->expectExceptionObject($exception);
        $ghostscript = $container->get(GhostScript::class);
    }

    public function providerTestConstructExceptions(): array
    {
        return [
            $this->ghostscriptNotInstalled()
        ];
    }

    private function ghostscriptNotInstalled(): array
    {
        $container = new Container();

        $shell = $this->getMockBuilder(ShellExec::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getStdout'])
            ->getMock();

        $shell->method('getStdout')
            ->willReturn('bash: gs -v: command not found');

        $container->add(ShellExec::class, $shell);
        $container->add(VersionParser::class);
        $container->add(GhostScript::class)->addArgument($container);

        return [$container,new PackageNotInstalled("Ghostscript is not installed. Please install Ghostscript and try again.")];

    }

    public function providerTestConstruct(): array
    {
        return [
            $this->ghostscriptInstalled()
        ];
    }

    private function ghostscriptInstalled(): array
    {
        $container = new Container();

        $shell = $this->getMockBuilder(ShellExec::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getStdout'])
            ->getMock();

        $shell->method('getStdout')
            ->willReturn('GPL Ghostscript 9.26 (2018-11-20)');

        $container->add(ShellExec::class, $shell);
        return [$container];
    }
}
