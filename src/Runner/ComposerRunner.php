<?php

/*
 * This file is part of the MatesOfMate Organisation.
 *
 * (c) Johannes Wachter <johannes@sulu.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MatesOfMate\ComposerExtension\Runner;

use MatesOfMate\Common\Process\ProcessExecutorInterface;
use Symfony\Component\Process\Process;

/**
 * Executes Composer commands and captures output.
 *
 * @internal
 *
 * @author Johannes Wachter <johannes@sulu.io>
 */
class ComposerRunner
{
    /**
     * @param array<int, string> $customCommand
     */
    public function __construct(
        private readonly ProcessExecutorInterface $executor,
        private readonly ?string $projectRoot = null,
        private readonly array $customCommand = [],
    ) {
    }

    /**
     * @param array<int, string> $args
     */
    public function run(array $args, bool $jsonOutput = false): RunResult
    {
        if ($jsonOutput) {
            $args[] = '--format=json';
        }

        if ([] !== $this->customCommand) {
            return $this->runCustomCommand($args, $jsonOutput);
        }

        $result = $this->executor->execute('composer', $args, timeout: 300, usePhpBinary: true);

        return new RunResult(
            exitCode: $result->exitCode,
            output: $result->output,
            errorOutput: $result->errorOutput,
            isJson: $jsonOutput,
        );
    }

    /**
     * @param array<int, string> $args
     */
    private function runCustomCommand(array $args, bool $jsonOutput): RunResult
    {
        $process = new Process(
            [...$this->customCommand, ...$args],
            $this->projectRoot,
        );
        $process->setTimeout(300);
        $process->run();

        return new RunResult(
            exitCode: $process->getExitCode() ?? 1,
            output: $process->getOutput(),
            errorOutput: $process->getErrorOutput(),
            isJson: $jsonOutput,
        );
    }
}
