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

/**
 * Executes Composer commands and captures output.
 *
 * @internal
 *
 * @author Johannes Wachter <johannes@sulu.io>
 */
class ComposerRunner
{
    public function __construct(
        private readonly ProcessExecutorInterface $executor,
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

        $result = $this->executor->execute('composer', $args, timeout: 300, usePhpBinary: true);

        return new RunResult(
            exitCode: $result->exitCode,
            output: $result->output,
            errorOutput: $result->errorOutput,
            isJson: $jsonOutput,
        );
    }
}
