<?php

namespace tests\TestScope;

use hphio\util\Helpers\ShellExec;
use hphio\util\TestScope\ChangedFiles;
use League\Container\Container;
use PHPUnit\Framework\TestCase;

class ChangedFilesTest extends TestCase
{
    /**
     * @param Container $container
     *
     * @return void
     * @dataProvider providerTestDetectChanges
     */
    public function testDetectChanges(Container $container)
    {
    }

    public function providerTestDetectChanges(): array
    {
        return [
            $this->diffBranch()
        ];
    }

    private function diffBranch()
    {

        $container = new Container();
        $container->add()

        $buffer = [];
        $buffer[] = 'src/Api/Clients/AdminUpdate941Wages.php';
        $buffer[] = 'src/Api/Clients/Businessimpact/ClientBusinessImpact.php';
        $buffer[] = 'src/Api/Clients/Receivables/GetClientReceivablesService.php';
        $buffer[] = 'src/Api/Workflow/Analyzers/Workflow325.php';
        $buffer[] = 'src/Api/Workflow/Analyzers/Workflow340.php';

        $mockShell = $this->getMockBuilder(ShellExec::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getStdout'])
            ->getMock();

        $mockShell->method('getStdout')->willReturn(implode("\n", $buffer));

        $container->add(ShellExec::class, $mockShell);
    }
}
