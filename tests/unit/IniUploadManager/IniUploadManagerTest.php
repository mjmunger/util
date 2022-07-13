<?php

namespace IniUploadManager;

use hphio\util\ByteValueInterpreter\HumanReadable;
use hphio\util\ByteValueInterpreter\RawInteger;
use hphio\util\IniUploadManager\IniUploadManager;
use hphio\util\PhpIni;
use League\Container\Container;
use PHPUnit\Framework\TestCase;

class IniUploadManagerTest extends TestCase
{

    /**
     * @param Container $container
     * @param $expectedValue
     * @return array
     * @dataProvider providerTestGetMaxPost
     */
    public function testGetMaxPost(Container $container, $expectedValue) {
        $manager = $container->get(IniUploadManager::class);
        $this->assertEquals($expectedValue, $manager->getMaxPost());
    }

    public function providerTestGetMaxPost()
    {
        return [
            $this->createFixture('post_max_size', '8K', 8192),
            $this->createFixture('post_max_size', '8M', 8388608),
            $this->createFixture('post_max_size', '8G', 8589934592),
            $this->createFixture('post_max_size', '7340032', 7340032),
        ];
    }

    /**
     * @return void
     * @dataProvider providerTestGetMaxUpload
     */
    public function testGetMaxUpload(Container $container, $expectedValue) {
        $manager = $container->get(IniUploadManager::class);
        $this->assertEquals($expectedValue, $manager->getMaxUpload());
    }

    private function createFixture(string $option, $value, $expectedValue)
    {
        $mock = $this->createMock(PhpIni::class);
        $mock->method('getOption')->with($option)->willReturn($value);
        $container = $this->getContainer($mock);
        return [$container, $expectedValue];
    }

    /**
     * @param $mock
     * @return Container
     */
    public function getContainer($mock): Container
    {
        $container = new Container();
        $container->add(PhpIni::class, $mock);
        $container->add(IniUploadManager::class)->addArgument($container);
        $container->add(HumanReadable::class);
        $container->add(RawInteger::class);
        return $container;
    }

    public function providerTestGetMaxUpload()
    {
                return [
            $this->createFixture('upload_max_filesize', '8K', 8192),
            $this->createFixture('upload_max_filesize', '8M', 8388608),
            $this->createFixture('upload_max_filesize', '8G', 8589934592),
            $this->createFixture('upload_max_filesize', '7340032', 7340032),
        ];
    }

    /**
     * @return void
     * @dataProvider providerTestGetUploadLimit
     */
    public function testGetUploadLimit(Container $container, $expectedValue) {
        $manager = $container->get(IniUploadManager::class);
        $this->assertEquals($expectedValue, $manager->getUploadLimit());
    }

    public function providerTestGetUploadLimit()
    {
        return [
            $this->PostSmaller(),
            $this->UploadSmaller()
        ];
    }

    private function PostSmaller()
    {
        $map = [
            ['upload_max_filesize', '8M'],
            ['post_max_size', '8k']
        ];
        $mock = $this->createMock(PhpIni::class);
        $mock->method('getOption')
            ->will($this->returnValueMap($map));
        $container = $this->getContainer($mock);
        return [$container, 8192];
    }

    private function UploadSmaller()
    {
                $map = [
            ['upload_max_filesize', '7k'],
            ['post_max_size', '6M']
        ];
        $mock = $this->createMock(PhpIni::class);
        $mock->method('getOption')
            ->will($this->returnValueMap($map));
        $container = $this->getContainer($mock);
        return [$container, 7168];
    }

    /**
     * @return void
     * @dataProvider providerTestIsTooBig
     */
    public function testIsTooBig(Container $container, $uploadSize, $expectedResult) {
        $manager = $container->get(IniUploadManager::class);
        $this->assertEquals($expectedResult, $manager->isTooBig($uploadSize));
    }

    public function providerTestIsTooBig()
    {
        return [
            $this->uploadTooBig(),
            $this->uploadExactSize(),
            $this->uploadCompliant()
        ];
    }

    private function uploadTooBig()
    {
                $map = [
            ['upload_max_filesize', '8M'],
            ['post_max_size', '8k']
        ];
        $mock = $this->createMock(PhpIni::class);
        $mock->method('getOption')
            ->will($this->returnValueMap($map));
        $container = $this->getContainer($mock);

        return [$container, 8192 + 1, true];
    }

    private function uploadExactSize()
    {
                        $map = [
            ['upload_max_filesize', '8M'],
            ['post_max_size', '8k']
        ];
        $mock = $this->createMock(PhpIni::class);
        $mock->method('getOption')
            ->will($this->returnValueMap($map));
        $container = $this->getContainer($mock);

        return [$container, 8192, false];
    }

    private function uploadCompliant()
    {
                                $map = [
            ['upload_max_filesize', '8M'],
            ['post_max_size', '8k']
        ];
        $mock = $this->createMock(PhpIni::class);
        $mock->method('getOption')
            ->will($this->returnValueMap($map));
        $container = $this->getContainer($mock);

        return [$container, 8191, false];
    }
}
