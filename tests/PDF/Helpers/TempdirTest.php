<?php
/**
 * @namspace      tests\PDF\Helpers
 * @name TempdirTest
 * Summary: #$END$#
 *
 * Date: 2023-11-17
 * Time: 10:17 AM
 *
 * @author        Michael Munger <mj@hph.io>
 * @copyright (c) 2023 High Powered Help, Inc. All Rights Reserved.
 */

namespace tests\PDF\Helpers;

use hphio\util\Helpers\Tempdir;
use PHPUnit\Framework\TestCase;

class TempdirTest extends TestCase
{

    public function testCreate()
    {
        $dir = new Tempdir();
        $path = $dir->create();
        $this->assertIsString($path);
        $this->assertDirectoryExists($path);
        $this->assertTrue(rmdir($path));
        $this->assertDirectoryDoesNotExist($path);
    }
}
