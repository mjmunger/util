<?php
/**
 * @namspace      hphio\util\PDF
 * @name VersionParser
 * Summary: #$END$#
 *
 * Date: 2023-11-17
 * Time: 9:36 AM
 *
 * @author        Michael Munger <mj@hph.io>
 * @copyright (c) 2023 High Powered Help, Inc. All Rights Reserved.
 */

namespace hphio\util\PDF;

use hphio\util\PDF\Exceptions\PDFNotFound;

class VersionParser
{

    /**
     * @throws PDFNotFound
     */
    public function getVersion($sourceFile): string
    {
        $this->checkFile($sourceFile);
        return $this->getPDFHeader($sourceFile);
    }

    private function checkFile($sourceFile): void
    {
        if(!file_exists($sourceFile) || !is_readable($sourceFile)) throw new PDFNotFound("PDF not found: {$sourceFile}");
    }

    private function getPDFHeader($sourceFile): string
    {
        $stream = fopen($sourceFile, 'rb');
        $header = fgets($stream);
        $header = trim($header);
        $version = str_replace('%PDF-', '', $header);
        $version = substr($version, 0, 3);

        fclose($stream);
        return $version;
    }
}
