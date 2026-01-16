<?php

/*
 * This file is part of the MatesOfMate Organisation.
 *
 * (c) Johannes Wachter <johannes@sulu.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MatesOfMate\ComposerExtension\Parser;

/**
 * Parsed result from Composer command output.
 *
 * @internal
 *
 * @author Johannes Wachter <johannes@sulu.io>
 */
class ParsedResult
{
    /**
     * @param array<int, array{name: string, version: string, description?: string}>                      $packages
     * @param array<int, array{package: string, requires: string, version?: string, constraint?: string}> $dependencies
     * @param array<int, string>                                                                          $errors
     * @param array<int, string>                                                                          $warnings
     * @param array<string, mixed>                                                                        $metadata
     */
    public function __construct(
        public readonly bool $success,
        public readonly string $command,
        public readonly array $packages = [],
        public readonly array $dependencies = [],
        public readonly array $errors = [],
        public readonly array $warnings = [],
        public readonly array $metadata = [],
    ) {
    }

    public function isSuccessful(): bool
    {
        return $this->success;
    }

    public function getPackageCount(): int
    {
        return \count($this->packages);
    }

    public function hasErrors(): bool
    {
        return [] !== $this->errors;
    }

    public function hasWarnings(): bool
    {
        return [] !== $this->warnings;
    }
}
