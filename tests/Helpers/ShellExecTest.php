<?php
/**
 * @namspace      tests\Helpers
 * @name ShellExecTest
 * Summary: #$END$#
 *
 * Date: 2023-11-17
 * Time: 10:37 AM
 *
 * @author        Michael Munger <mj@hph.io>
 * @copyright (c) 2023 High Powered Help, Inc. All Rights Reserved.
 */

namespace tests\Helpers;

use Exception;
use hphio\util\Helpers\ShellExec;
use PHPUnit\Framework\TestCase;

class ShellExecTest extends TestCase
{

    public function testExec()
    {
        $shell = new ShellExec();
        $cwd = sys_get_temp_dir();
        $shell->exec("echo 'test' && echo 'error test' >&2", $cwd);
        $this->assertEquals('test' . PHP_EOL, $shell->getStdout());
        $this->assertEquals('error test' . PHP_EOL, $shell->getStderr());
        $this->assertEquals(0, $shell->getExitCode());
    }

    /**
     * @throws Exception
     */
    public function testGetStdout()
    {
        $shell = new ShellExec();
        $shell->exec("echo 'stdout test'");
        $this->assertStringContainsString('stdout test', $shell->getStdout());
    }

    /**
     * @throws Exception
     */
    public function testGetStderr()
    {
        $shell = new ShellExec();
        $shell->exec("echo 'error test' >&2");
        $this->assertStringContainsString('error test', $shell->getStderr());
    }

    /**
     * @return void
     * @dataProvider providerTestExecExceptions
     * @throws Exception
     */
    public function testExecExceptions($cwd, $expectedException) {
        $this->expectExceptionObject($expectedException);
        $shell = new ShellExec();
        $shell->exec("echo 'test'", $cwd);
    }

    public function providerTestExecExceptions(): array
    {
        return [
            $this->nonExistingCwd(),
            $this->existingFileNotDir()
        ];
    }

    private function nonExistingCwd(): array
    {
        return ['/tmp/this/does/not/exist', new Exception('cwd must exist!')];
    }

    private function existingFileNotDir(): array
    {
        return [__FILE__, new Exception('cwd must be a directory!')];
    }
}
