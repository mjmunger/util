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

use Exception;
use hphio\util\Exceptions\PackageNotInstalled;
use hphio\util\Helpers\ShellExec;
use hphio\util\PDF\Helpers\GhostScript;
use hphio\util\PDF\Helpers\PDFInfo;
use hphio\util\PDF\VersionParser;
use League\Container\Container;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class GhostScriptTest extends TestCase
{

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @dataProvider providerTestDowngrade
     */
    public function testDowngrade(Container $container,
                                  string    $sourcePDFPath,
                                  string    $expectedVersion
    )
    {
        $shell = $container->get(GhostScript::class);
        $this->assertFileExists($sourcePDFPath);

        $tempfile = tempnam(sys_get_temp_dir(), 'test_') . ".pdf";
        $shell->downgrade($sourcePDFPath, $tempfile);

        $this->assertFileExists($tempfile);

        $version = $container->get(VersionParser::class);
        $this->assertSame($expectedVersion, $version->getVersion($tempfile));

        $sourceInfo = $container->get(PDFInfo::class);
        $targetInfo = $container->get(PDFInfo::class);

        $sourceInfo->analyzePdf($sourcePDFPath);
        $targetInfo->analyzePdf($tempfile);

        $this->assertSame($sourceInfo->pages, $targetInfo->pages);

        unlink($tempfile);

        $this->assertFileDoesNotExist($tempfile);
    }

    /**
     * @param Container $container
     * @param string    $sourcePDFPath
     * @param string    $expectedVersion
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @dataProvider providerTestDowngradeExceptions
     */
    public function testDowngradeException(Container $container,
                                           string    $sourcePDFPath,
                                           string    $expectedVersion,
                                           Exception $expectedException
    )
    {
        $shell = $container->get(GhostScript::class);
        $this->assertFileExists($sourcePDFPath);

        $tempfile = tempnam(sys_get_temp_dir(), 'test_') . ".pdf";
        $this->expectExceptionObject($expectedException);
        $shell->downgrade($sourcePDFPath, $tempfile);
//
//        $this->assertFileExists($tempfile);
//
//        $version = $container->get(VersionParser::class);
//        $this->assertSame($expectedVersion, $version->getVersion($tempfile));
//
//        $sourceInfo = $container->get(PDFInfo::class);
//        $targetInfo = $container->get(PDFInfo::class);
//
//        $sourceInfo->analyzePdf($sourcePDFPath);
//        $targetInfo->analyzePdf($tempfile);
//
//        $this->assertSame($sourceInfo->pages, $targetInfo->pages);
//
//        unlink($tempfile);
//
//        $this->assertFileDoesNotExist($tempfile);
    }

    public
    function providerTestDowngradeExceptions(): array
    {
        return [
            $this->downgradeFailedPageCountCheck()
        ];
    }

    private
    function downgradeFailedPageCountCheck(): array
    {

        $mockPdfInfo = $this->getMockBuilder(PDFInfo::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['analyzePdf', 'pageCount'])
            ->getMock();

        $mockPdfInfo->method('pageCount')
            ->willReturnOnConsecutiveCalls(15, 1, 15, 1);

        $container = new Container();
        $container->add(ShellExec::class);
        $container->add(VersionParser::class);
        $container->add(PDFInfo::class, $mockPdfInfo);
        $container->add(GhostScript::class)->addArgument($container);

        $sourcePDFPath = dirname(__FILE__) . "/fixtures/pdf-v1.7.pdf";

        $expectedVersion = '1.4';
        return [$container, $sourcePDFPath, $expectedVersion, new Exception("Downgrade failed. Source PDF had 15 pages, but the target PDF had 1 pages.")];
    }

    /**
     * @param Container $container
     *
     * @throws PackageNotInstalled
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @dataProvider providerTestConstruct
     */
    public
    function test__construct(Container $container)
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
    public
    function testConstructExceptions(Container $container, $exception)
    {
        $this->expectExceptionObject($exception);
        $ghostscript = $container->get(GhostScript::class);
    }

    public
    function providerTestConstructExceptions(): array
    {
        return [
            $this->ghostscriptNotInstalled(),
            $this->pdfinfoNotInstalled()
        ];
    }

    private
    function ghostscriptNotInstalled(): array
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

        return [$container, new PackageNotInstalled("Ghostscript is not installed. Please install Ghostscript and try again.")];

    }

    public
    function providerTestConstruct(): array
    {
        return [
            $this->ghostscriptInstalled()
        ];
    }

    private
    function ghostscriptInstalled(): array
    {
        $container = new Container();

        $shell = $this->getMockBuilder(ShellExec::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getStdout'])
            ->getMock();

        $shell->method('getStdout')
            ->willReturnOnConsecutiveCalls(
                'GPL Ghostscript 9.26 (2018-11-20)',
                'pdfinfo version 3.03'
            );
//            ->willReturn('GPL Ghostscript 9.26 (2018-11-20)');

        $container->add(ShellExec::class, $shell);
        return [$container];
    }

    private
    function pdfinfoNotInstalled(): array
    {
        $container = new Container();

        $shell = $this->getMockBuilder(ShellExec::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getStdout', 'getStderr'])
            ->getMock();

        $shell->method('getStdout')
            ->willReturnOnConsecutiveCalls(
                'GPL Ghostscript 9.26 (2018-11-20)'
            );

        $shell->method('getStderr')
            ->willReturnOnConsecutiveCalls('',
                'bash: pdfinfo -v: command not found');

        $container->add(ShellExec::class, $shell);
        $container->add(VersionParser::class);
        $container->add(GhostScript::class)->addArgument($container);

        return [$container, new PackageNotInstalled("PDFInfo is not installed. Please install PDFInfo and try again. (sudo apt-get install poppler-utils)")];
    }

    public
    function providerTestDowngrade(): array
    {
        return [
            $this->validPdf(),
            $this->pdfWithSpaceInPath(),
        ];
    }

    private
    function validPdf(): array
    {
        $container = new Container();
        $container->add(ShellExec::class);
        $container->add(VersionParser::class);
        $container->add(PDFInfo::class)->addArgument($container);
        $container->add(GhostScript::class)->addArgument($container);

        $sourcePDFPath = dirname(__FILE__) . "/fixtures/pdf-v1.7.pdf";

        $expectedVersion = '1.4';
        return [$container, $sourcePDFPath, $expectedVersion];
    }

    private
    function pdfWithSpaceInPath(): array
    {
        $container = new Container();
        $container->add(ShellExec::class);
        $container->add(VersionParser::class);
        $container->add(PDFInfo::class)->addArgument($container);
        $container->add(GhostScript::class)->addArgument($container);

        $sourcePDFPath = dirname(__FILE__) . "/fixtures/COVID-19 Lockdown Policies at the State and Local Level.pdf";

        $expectedVersion = '1.4';
        return [$container, $sourcePDFPath, $expectedVersion];
    }
}
