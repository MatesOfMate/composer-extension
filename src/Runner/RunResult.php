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

/**
 * Result of a Composer command execution.
 *
 * @internal
 *
 * @author Johannes Wachter <johannes@sulu.io>
 */
readonly class RunResult
{
    public function __construct(
        public int $exitCode,
        public string $output,
        public string $errorOutput,
        public bool $isJson = false,
    ) {
    }

    public function isSuccessful(): bool
    {
        return 0 === $this->exitCode;
    }

    /**
     * @return array<string, mixed>
     */
    public function getJsonOutput(): array
    {
        if (!$this->isJson) {
            throw new \RuntimeException('Output is not JSON format');
        }

        $decoded = json_decode($this->output, true, 512, \JSON_THROW_ON_ERROR);

        if (!\is_array($decoded)) {
            return [];
        }

        return $decoded;
    }
}
