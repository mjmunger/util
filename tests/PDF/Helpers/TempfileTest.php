<?php
/**
 * @namspace      tests\PDF\Helpers
 * @name TempfileTest
 * Summary: #$END$#
 *
 * Date: 2023-11-17
 * Time: 10:16 AM
 *
 * @author        Michael Munger <mj@hph.io>
 * @copyright (c) 2023 High Powered Help, Inc. All Rights Reserved.
 */

namespace tests\PDF\Helpers;

use hphio\util\Helpers\Tempfile;
use PHPUnit\Framework\TestCase;

class TempfileTest extends TestCase
{

    public function testCreate()
    {
        $tempfile = new Tempfile();
        $path = $tempfile->create();
        $this->assertIsString($path);
        $this->assertFileExists($path);
        $this->assertTrue(unlink($path));
        $this->assertFileDoesNotExist($path);
        $this->assertSame(sys_get_temp_dir(), dirname($path));
    }
}
