<?php
/**
 * @namspace      tests\PDF
 * @name VersionParserTest
 * Summary: #$END$#
 *
 * Date: 2023-11-17
 * Time: 9:36 AM
 *
 * @author        Michael Munger <mj@hph.io>
 * @copyright (c) 2023 High Powered Help, Inc. All Rights Reserved.
 */

namespace tests\PDF;

use hphio\util\PDF\Exceptions\PDFNotFound;
use hphio\util\PDF\VersionParser;
use PHPUnit\Framework\TestCase;

class VersionParserTest extends TestCase
{

    /**
     * Parses the version of the PDF from the file header.
     *
     * @return void
     * @dataProvider providerTestGetVersion
     * @throws PDFNotFound
     */
    public function testGetVersion($sourceFile, $expectedVersion) {

        $this->assertFileExists($sourceFile);
        $this->assertIsReadable($sourceFile);
        $parser = new VersionParser();
        $this->assertSame($expectedVersion, $parser->getVersion($sourceFile));

    }

    public function providerTestGetVersion(): array
    {
        return [
            $this->sixteen(),
            $this->fourteen(),
            $this->seventeen()
        ];
    }

    private function fourteen(): array
    {
        $sourceFile = dirname(__FILE__) . "/fixtures/pdf-v1.4.pdf";
        $version = "1.4";
        return [$sourceFile, $version];
    }

    private function seventeen(): array
    {
        $sourceFile = dirname(__FILE__) . "/fixtures/pdf-v1.7.pdf";
        $version = "1.7";
        return [$sourceFile, $version];
    }

    /**
     * @param $sourceFile
     * @param $expectedException
     *
     * @return void
     * @throws PDFNotFound
     * @dataProvider providerTestGetVersionExceptions
     */
    public function testGetVersionExceptions($sourceFile, $expectedException) {

        $this->assertFileDoesNotExist($sourceFile);
        $this->expectExceptionObject($expectedException);
        $parser = new VersionParser();
        $parser->getVersion($sourceFile);
    }

    public function providerTestGetVersionExceptions(): array
    {
        return [
            $this->fileDoesNotExist()
        ];
    }

    private function fileDoesNotExist(): array
    {
        $sourceFile = dirname(__FILE__) . "/fixtures/this-should-not-exist.pdf";

        return [$sourceFile, new PDFNotFound("PDF not found: {$sourceFile}")];
    }

    private function sixteen(): array
    {
        $sourceFile = dirname(__FILE__) . "/fixtures/pdf-v1.6.pdf";
        $version = "1.6";
        return [$sourceFile, $version];
    }
}
