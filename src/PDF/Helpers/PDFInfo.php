<?php
/**
 * @namspace      hphio\util\PDF\Helpers
 * @name PDFInfo
 * Summary: #$END$#
 *
 * Date: 2023-11-18
 * Time: 11:11 AM
 *
 * @author        Michael Munger <mj@hph.io>
 * @copyright (c) 2023 High Powered Help, Inc. All Rights Reserved.
 */

namespace hphio\util\PDF\Helpers;

use hphio\util\Helpers\ShellExec;
use League\Container\Container;

class PDFInfo
{
    public ?string $title = null;
    public ?string $creator = null;
    public ?string $producer = null;
    public ?string $creationDate = null;
    public ?string $modDate = null;
    public ?string $tagged = null;
    public ?string $userProperties = null;
    public ?string $suspects = null;
    public ?string $form = null;
    public ?string $javaScript = null;
    public ?string $pages = null;
    public ?string $encrypted = null;
    public ?string $pageSize = null;
    public ?string $pageRot = null;
    public ?string $filesize = null;
    public ?string $optimized = null;
    public ?string $pdfVersion = null;

    protected ?Container $container = null;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function analyzePdf(string $pdfFile): void
    {
        $shell = $this->container->get(ShellExec::class);
        $command = "pdfinfo '{$pdfFile}'";
        $shell->exec($command);

        $lines = explode("\n", $shell->getStdout());
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;
            $parts = explode(":", $line,2);
            $key = $this->translate(trim($parts[0]));
            $value = trim($parts[1]);
            $this->$key = $value;
        }
    }

    protected function translate($key): string
    {
        $map = ["Title" => 'title',
            "Creator" => 'creator',
            "Producer" => 'producer',
            "CreationDate" => 'creationDate',
            "ModDate" => 'modDate',
            "Tagged" => 'tagged',
            "UserProperties" => 'userProperties',
            "Suspects" => 'suspects',
            "Form" => 'form',
            "JavaScript" => 'javaScript',
            "Pages" => 'pages',
            "Encrypted" => 'encrypted',
            "Page size" => 'pageSize',
            "Page rot" => 'pageRot',
            "File size" => 'filesize',
            "Optimized" => 'optimized',
            "PDF version" => 'pdfVersion'];
        return $map[$key];
    }
}
