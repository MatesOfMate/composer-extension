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

use MatesOfMate\ComposerExtension\Runner\RunResult;

/**
 * Parses Composer command output into structured data.
 *
 * @internal
 *
 * @author Johannes Wachter <johannes@sulu.io>
 */
class OutputParser
{
    public function parseCommandOutput(RunResult $result, string $command): ParsedResult
    {
        $errors = $this->extractErrors($result);
        $warnings = $this->extractWarnings($result);

        return new ParsedResult(
            success: $result->isSuccessful(),
            command: $command,
            packages: [],
            dependencies: [],
            errors: $errors,
            warnings: $warnings,
            metadata: ['output' => $result->output],
        );
    }

    /**
     * @param array<int|string, mixed> $json
     */
    public function parseWhyOutput(array $json, string $package): ParsedResult
    {
        $dependencies = [];

        foreach ($json as $entry) {
            if (!\is_array($entry)) {
                continue;
            }

            $dependencies[] = [
                'package' => (string) ($entry[0] ?? ''),
                'requires' => (string) ($entry[1] ?? ''),
                'version' => (string) ($entry[2] ?? ''),
                'constraint' => (string) ($entry[2] ?? ''),
            ];
        }

        return new ParsedResult(
            success: true,
            command: 'why',
            packages: [],
            dependencies: $dependencies,
            errors: [],
            warnings: [],
            metadata: ['target_package' => $package],
        );
    }

    /**
     * @param array<int|string, mixed> $json
     */
    public function parseWhyNotOutput(array $json, string $package, ?string $version): ParsedResult
    {
        $dependencies = [];

        foreach ($json as $entry) {
            if (!\is_array($entry)) {
                continue;
            }

            $dependencies[] = [
                'package' => (string) ($entry[0] ?? ''),
                'requires' => (string) ($entry[1] ?? ''),
                'version' => (string) ($entry[2] ?? ''),
                'constraint' => (string) ($entry[2] ?? ''),
            ];
        }

        return new ParsedResult(
            success: true,
            command: 'why-not',
            packages: [],
            dependencies: $dependencies,
            errors: [],
            warnings: [],
            metadata: [
                'target_package' => $package,
                'target_version' => $version,
            ],
        );
    }

    /**
     * @param array<int|string, mixed> $json
     */
    public function parseShowOutput(array $json): ParsedResult
    {
        $packages = [];

        $installed = $json['installed'] ?? $json;
        if (!\is_array($installed)) {
            return new ParsedResult(
                success: true,
                command: 'show',
                packages: [],
                dependencies: [],
                errors: [],
                warnings: [],
                metadata: [],
            );
        }

        foreach ($installed as $pkg) {
            if (!\is_array($pkg)) {
                continue;
            }

            $packages[] = [
                'name' => (string) ($pkg['name'] ?? ''),
                'version' => (string) ($pkg['version'] ?? ''),
                'description' => (string) ($pkg['description'] ?? ''),
            ];
        }

        return new ParsedResult(
            success: true,
            command: 'show',
            packages: $packages,
            dependencies: [],
            errors: [],
            warnings: [],
            metadata: ['package_count' => \count($packages)],
        );
    }

    /**
     * @return array<int, string>
     */
    private function extractErrors(RunResult $result): array
    {
        $errors = [];
        $errorOutput = $result->errorOutput;

        if ('' !== $errorOutput && !$result->isSuccessful()) {
            // Split error output into lines and filter relevant messages
            $lines = explode("\n", $errorOutput);
            foreach ($lines as $line) {
                $line = trim($line);
                if ('' !== $line && !str_starts_with($line, 'Warning:')) {
                    $errors[] = $line;
                }
            }
        }

        return $errors;
    }

    /**
     * @return array<int, string>
     */
    private function extractWarnings(RunResult $result): array
    {
        $warnings = [];
        $allOutput = $result->output.$result->errorOutput;

        // Extract warning messages
        if (preg_match_all('/Warning:\s*(.+)/i', $allOutput, $matches)) {
            foreach ($matches[1] as $warning) {
                $warnings[] = trim($warning);
            }
        }

        return $warnings;
    }
}
