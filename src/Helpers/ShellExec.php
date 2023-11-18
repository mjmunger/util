<?php

namespace hphio\util\Helpers;

use Exception;

/**
 * Wrapper for the shell_exec system call.
 */
class ShellExec
{
    protected ?string $stdout = null;
    protected ?int $returnValue = null;
    protected ?string $stderr = null;

    /**
     * Execute a command, and return the stdout and stderr to pipes.
     * Optionally define current working directory and environment vars.
     *
     * @param             $command
     * @param string|null $cwd
     * @param array       $env
     *
     * @return void
     * @throws Exception
     */

    public function exec($command, string $cwd = null, array $env = []): void
    {
        $descriptorSpecs = array(
            0 => array("pipe", "r"),  // stdin is a pipe that the child will read from
            1 => array("pipe", "w"),  // stdout is a pipe that the child will write to
            2 => array("pipe", "w")   // stderr is a pipe that the child will write to
        );

        if (!is_null($cwd)) {
            $this->cwdValidate($cwd);
            chdir($cwd);
        }

//        $env = array('some_option' => 'aeiou');

        $process = proc_open($command, $descriptorSpecs, $pipes, $cwd, $env);

        if (!is_resource($process)) {
            throw new Exception("Could not open process!");
        }
        $this->getResponse($pipes[1]);
        $this->getErrors($pipes[2]);
        $this->returnValue = proc_close($process);
    }

    /**
     * @param string $cwd
     *
     * @return void
     * @throws Exception
     */
    private function cwdValidate(string $cwd): void
    {
        if (!file_exists($cwd)) {
            throw new Exception("cwd must exist!");
        }

        if (!is_dir($cwd)) {
            throw new Exception("cwd must be a directory!");
        }
    }

    private function getResponse($stdout): void
    {
        $this->stdout = stream_get_contents($stdout);
        fclose($stdout);
    }

    private function getErrors($stderr): void
    {
        $this->stderr = stream_get_contents($stderr);
        fclose($stderr);
    }

    public function getStdout(): ?string
    {
        return $this->stdout;
    }

    public function getStderr(): ?string
    {
        return $this->stderr;
    }

    public function getExitCode(): ?int
    {
        return $this->returnValue;
    }
}
