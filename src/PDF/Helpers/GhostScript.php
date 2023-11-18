<?php
/**
 * @namspace      hphio\util\PDF\Helpers
 * @name GhostScript
 * Summary: #$END$#
 *
 * Date: 2023-11-17
 * Time: 10:14 AM
 *
 * @author        Michael Munger <mj@hph.io>
 * @copyright (c) 2023 High Powered Help, Inc. All Rights Reserved.
 */

namespace hphio\util\PDF\Helpers;

use hphio\util\Exceptions\PackageNotInstalled;
use hphio\util\Helpers\ShellExec;
use hphio\util\PDF\Exceptions\PageCountMismatch;
use League\Container\Container;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class GhostScript
{
    protected ?Container $container = null;

    /**
     * @param Container $container
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws PackageNotInstalled
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->checkInstallation();
    }
    public function downgrade($inputFile, $outputFile): void
    {
        $inputFile = addcslashes($inputFile, ' ');
        $sourceInfo = $this->container->get(PDFInfo::class);
        $sourceInfo->analyzePdf($inputFile);

        if($sourceInfo->pageCount() == 0) {
            throw new \Exception("Source PDF had no pages. Cannot downgrade. ({$inputFile})");
        }

        $command = "gs -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dNOPAUSE -dQUIET -dBATCH -sOutputFile=$outputFile '{$inputFile}'";
        $shell = $this->container->get(ShellExec::class);
        $shell->exec($command);

        $targetInfo = $this->container->get(PDFInfo::class);
        $targetInfo->analyzePdf($outputFile);

        if($sourceInfo->pageCount() != $targetInfo->pageCount()) {
            throw new PageCountMismatch("Downgrade failed. Source PDF had {$sourceInfo->pageCount()} pages, but the target PDF had {$targetInfo->pageCount()} pages.");
        }
    }

    /**
     * @throws PackageNotInstalled
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function checkInstallation(): void
    {
        $this->verifyGhostScript();
        $this->verifyPdfInfo();
    }

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws PackageNotInstalled
     */
    protected function verifyGhostScript(): void
    {
        $command = "gs -v";
        $shell = $this->container->get(ShellExec::class);
        $shell->exec($command);
        if (!str_contains($shell->getStdout(), 'GPL Ghostscript')) {
            throw new PackageNotInstalled("Ghostscript is not installed. Please install Ghostscript and try again. (sudo apt-get install ghostscript)");
        }
    }

    protected function verifyPdfInfo():void
    {
        $command = "pdfinfo -v";
        /** @var ShellExec $shell */
        $shell = $this->container->get(ShellExec::class);
        $shell->exec($command);
        if (!str_contains($shell->getStderr(), 'pdfinfo version')) {
            throw new PackageNotInstalled("PDFInfo is not installed. Please install PDFInfo and try again. (sudo apt-get install poppler-utils)");
        }
    }
}
