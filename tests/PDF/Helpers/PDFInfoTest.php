<?php
/**
 * @namspace      tests\PDF\Helpers
 * @name PDFInfoTest
 * Summary: #$END$#
 *
 * Date: 2023-11-18
 * Time: 11:12 AM
 *
 * @author        Michael Munger <mj@hph.io>
 * @copyright (c) 2023 High Powered Help, Inc. All Rights Reserved.
 */

namespace tests\PDF\Helpers;

use hphio\util\Helpers\ShellExec;
use hphio\util\PDF\Helpers\PDFInfo;
use League\Container\Container;
use PHPUnit\Framework\TestCase;

class PDFInfoTest extends TestCase
{
    public function testConstruct()
    {
        $container = new Container();
        $container->add(ShellExec::class);

        $pdfInfo = new PDFInfo($container);
        $this->assertInstanceOf(PDFInfo::class, $pdfInfo);

        $reflection = new \ReflectionClass($pdfInfo);
        $containerProperty = $reflection->getProperty('container');
        $containerProperty->setAccessible(true);
        $this->assertSame($container, $containerProperty->getValue($pdfInfo));

    }

    /**
     * @param Container $container
     * @param string    $pdfFile
     * @param array     $expected
     *
     * @return void
     * @dataProvider providerTestAnalyzePdf
     */
    public function testAnalyzePdf(Container $container, string $pdfFile, array $expected)
    {
        $pdfInfo = new PDFInfo($container);
        $pdfInfo->analyzePdf($pdfFile);

        $this->assertSame(
            $expected,
            json_decode(json_encode($pdfInfo), true)
        );
    }

    public function providerTestAnalyzePdf(): array
    {
        return [
            $this->lockdownPolicies()
        ];
    }

    private function lockdownPolicies(): array
    {
        $container = new Container();
        $container->add(ShellExec::class);

        $expected = [
            'title' => '',
            'creator' => 'Acrobat Pro DC 20.12.20041',
            'producer' => 'Adobe PDF Library 15.0',
            'creationDate' => 'Wed Aug 26 17:41:11 2020 EDT',
            'modDate' => 'Wed Aug 26 17:41:11 2020 EDT',
            'tagged' => 'yes',
            'userProperties' => 'no',
            'suspects' => 'no',
            'form' => 'none',
            'javaScript' => 'no',
            'pages' => '15',
            'encrypted' => 'no',
            'pageSize' => '612 x 792 pts (letter)',
            'pageRot' => '0',
            'filesize' => '1721629 bytes',
            'optimized' => 'no',
            'pdfVersion' => '1.6',
            'author' => null
        ];

        $pdfPath = dirname(__FILE__) . '/fixtures/COVID-19 Lockdown Policies at the State and Local Level.pdf';
        $this->assertFileExists($pdfPath);

        return [$container, $pdfPath, $expected];
    }
}
