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
        // For why/why-not commands, parse the text output as dependency info
        if ('why' === $command || 'why-not' === $command) {
            return $this->parseWhyCommandOutput($result, $command);
        }

        // For install/require/update/remove commands, extract package information
        if (\in_array($command, ['install', 'require', 'update', 'remove'], true)) {
            return $this->parsePackageCommandOutput($result, $command);
        }

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

    private function parsePackageCommandOutput(RunResult $result, string $command): ParsedResult
    {
        $output = $result->output;
        $packages = [];
        $metadata = [];

        // Parse installed packages: "- Installing vendor/package (v1.2.3)"
        if (preg_match_all('/- Installing (\S+) \(([^)]+)\)/', $output, $matches, \PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $packages[] = [
                    'name' => $match[1],
                    'version' => $match[2],
                    'action' => 'installed',
                ];
            }
        }

        // Parse updated packages: "- Upgrading vendor/package (v1.0.0 => v1.2.3)" or "- Updating ..."
        if (preg_match_all('/- (?:Upgrading|Updating) (\S+) \(([^)]+)\s*=>\s*([^)]+)\)/', $output, $matches, \PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $packages[] = [
                    'name' => $match[1],
                    'version' => $match[3],
                    'previous_version' => $match[2],
                    'action' => 'updated',
                ];
            }
        }

        // Parse downgraded packages: "- Downgrading vendor/package (v2.0.0 => v1.0.0)"
        if (preg_match_all('/- Downgrading (\S+) \(([^)]+)\s*=>\s*([^)]+)\)/', $output, $matches, \PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $packages[] = [
                    'name' => $match[1],
                    'version' => $match[3],
                    'previous_version' => $match[2],
                    'action' => 'downgraded',
                ];
            }
        }

        // Parse removed packages: "- Removing vendor/package (v1.2.3)"
        if (preg_match_all('/- Removing (\S+) \(([^)]+)\)/', $output, $matches, \PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $packages[] = [
                    'name' => $match[1],
                    'version' => $match[2],
                    'action' => 'removed',
                ];
            }
        }

        // For require command, extract the selected version: "Using version ^x.x for vendor/package"
        if ('require' === $command && preg_match('/Using version (\S+) for (\S+)/', $output, $match)) {
            $metadata['selected_version'] = $match[1];
            $metadata['requested_package'] = $match[2];
        }

        $errors = $this->extractErrors($result);
        $warnings = $this->extractWarnings($result);

        return new ParsedResult(
            success: $result->isSuccessful(),
            command: $command,
            packages: $packages,
            dependencies: [],
            errors: $errors,
            warnings: $warnings,
            metadata: $metadata,
        );
    }

    private function parseWhyCommandOutput(RunResult $result, string $command): ParsedResult
    {
        $output = trim($result->output);
        $dependencies = [];

        // Parse lines like: "symfony/ai-mate v0.2.0 requires symfony/config (^5.4|^6.4|^7.3|^8.0)"
        $lines = explode("\n", $output);
        foreach ($lines as $line) {
            $line = trim($line);
            if ('' === $line) {
                continue;
            }

            // Skip hint messages
            if (str_starts_with($line, 'Not finding what you were looking for?')) {
                continue;
            }

            // Parse dependency line: "package version requires/does not require target (constraint)"
            if (preg_match('/^(\S+)\s+(\S+)\s+(requires|does not require)\s+(\S+)\s*(.*)$/', $line, $matches)) {
                $dependencies[] = [
                    'package' => $matches[1],
                    'version' => $matches[2],
                    'requires' => $matches[3],
                    'target' => $matches[4],
                    'constraint' => trim($matches[5], '() '),
                ];
            }
        }

        // For why/why-not, having output means success (even with non-zero exit code)
        $success = [] !== $dependencies || '' !== $output;

        return new ParsedResult(
            success: $success,
            command: $command,
            packages: [],
            dependencies: $dependencies,
            errors: [],
            warnings: [],
            metadata: ['raw_output' => $output],
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
