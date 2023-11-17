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
        $command = "gs -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dNOPAUSE -dQUIET -dBATCH -sOutputFile=$outputFile $inputFile";
        $shell = $this->container->get(ShellExec::class);
        $shell->exec($command);
    }

    /**
     * @throws PackageNotInstalled
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function checkInstallation(): void
    {
        $command = "gs -v";
        $shell = $this->container->get(ShellExec::class);
        $shell->exec($command);
        if (!str_contains($shell->getStdout(), 'GPL Ghostscript')) {
            throw new PackageNotInstalled("Ghostscript is not installed. Please install Ghostscript and try again.");
        }
    }
}