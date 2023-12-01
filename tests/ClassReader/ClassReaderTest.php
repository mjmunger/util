<?php
/**
 * @namspace Cli
 * @name \tests\ClassReader\ClassReaderTest
 * Summary: #$END$#
 *
 * Date: 2023-02-20
 * Time: 4:37 PM
 *
 * @author Michael Munger <mj@hph.io>
 * @copyright (c) 2023 High Powered Help, Inc. All Rights Reserved.
 */

namespace tests\ClassReader;


use hphio\util\ClassReader\ClassReader;
use PHPUnit\Framework\TestCase;

class ClassReaderTest extends TestCase
{


    /**
     * @param string $path
     * @return void
     * @dataProvider providerTestAnalyze
     * @throws \Exception
     */
    public function testAnalyze(string $path, $expectedClassName, $expectedNamespace, $expectedHasConstructor, $requiresContainer) {
        $reader = new ClassReader();
        $reader->analyze($path);
        $this->assertSame($expectedClassName, $reader->classname());
        $this->assertSame($expectedNamespace, $reader->namespace());
        $this->assertSame($expectedHasConstructor, $reader->hasConstructor());
        $this->assertSame($requiresContainer, $reader->requiresContainer());
    }

    /**
     * @param string $path
     * @param $expectedExceptionCode
     * @param $expectedExceptionMessage
     * @return void
     * @dataProvider providerTestAnalyzeExceptions
     */
    public function testAnalyzeExceptions(string $path, $expectedExceptionCode, $expectedExceptionMessage) {
        $this->expectExceptionMessage($expectedExceptionMessage);
        $this->expectExceptionCode($expectedExceptionCode);
        $reader = new ClassReader();
        $reader->analyze($path);
    }

    public function providerTestAnalyzeExceptions(): array
    {
        return [
            $this->fileDoesNotExist()
        ];
    }

    private function fileDoesNotExist(): array
    {
        $path = "/tmp/does_not_exists.php";
        $expectedExceptionCode = 500;
        $expectedExceptionMessage = "Target file does not exist: " . $path;
        return [$path, $expectedExceptionCode, $expectedExceptionMessage];
    }

    public function providerTestAnalyze(): array
    {
        return [
//            $this->authenticator(),
            $this->NullContractorPerformanceReport()
        ];
    }

    private function authenticator(): array
    {
        $path = dirname(__FILE__) . "/fixtures/Authenticator.php";
        $expectedClassName = 'Authenticator';
        $expectedNamespace = 'Erc\Api\Auth';
        $expectedHasConstructor = true;
        $requiresContainer = true;

        return [$path, $expectedClassName, $expectedNamespace, $expectedHasConstructor, $requiresContainer];
    }
    private function NullContractorPerformanceReport(): array
    {
        $path = dirname(__FILE__) . "/fixtures/NullContractorPerformanceReport.php";
        $expectedClassName = 'NullContractorPerformanceReport';
        $expectedNamespace = 'tests\ClassReader\fixtures';
        $expectedHasConstructor = false;
        $requiresContainer = false;

        return [$path, $expectedClassName, $expectedNamespace, $expectedHasConstructor, $requiresContainer];
    }

    /**
     * @param $filePath
     * @param $expectedResult
     * @return void
     * @dataProvider providerTestIsClass
     */
    public function testIsClass($filePath, $expectedResult): void
    {

        $reader = new ClassReader();
        $reader->analyze($filePath);
        $this->assertSame($expectedResult, $reader->isClass());
    }

    public function providerTestIsClass(): array
    {
        return [
            $this->traitExample(),
            $this->classExample()
        ];
    }

    private function traitExample(): array
    {
        $filePath = dirname(__FILE__) . "/fixtures/UserTrait.php";
        $expectedResult = false;

        return [$filePath, $expectedResult];
    }

    private function classExample(): array
    {
        $filePath = dirname(__FILE__) . "/fixtures/Authenticator.php";
        $expectedResult = true;
        return [$filePath, $expectedResult];
    }

    public function testFullClasspath() {
        $reader = new ClassReader();

        $reflection = new \ReflectionClass($reader);
        $property = $reflection->getProperty('classname');
        $property->setAccessible(true);
        $property->setValue($reader, 'SuperClass');

        $property = $reflection->getProperty('namespace');
        $property->setAccessible(true);
        $property->setValue($reader,'\\Foo\\Bar');

        $this->assertSame('\\Foo\\Bar\\SuperClass', $reader->fullClassPath());
    }



}
