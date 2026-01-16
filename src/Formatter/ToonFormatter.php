<?php

/*
 * This file is part of the MatesOfMate Organisation.
 *
 * (c) Johannes Wachter <johannes@sulu.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MatesOfMate\ComposerExtension\Formatter;

use MatesOfMate\ComposerExtension\Parser\ParsedResult;

/**
 * Formats Composer results using TOON (Token-Oriented Object Notation) for token-efficient output.
 *
 * @internal
 *
 * @author Johannes Wachter <johannes@sulu.io>
 */
class ToonFormatter
{
    public function format(ParsedResult $result, string $mode = 'default'): string
    {
        return match ($mode) {
            'default' => $this->formatDefault($result),
            'summary' => $this->formatSummary($result),
            'detailed' => $this->formatDetailed($result),
            default => throw new \InvalidArgumentException("Unknown format mode: {$mode}"),
        };
    }

    private function formatDefault(ParsedResult $result): string
    {
        $data = [
            'command' => $result->command,
            'status' => $result->isSuccessful() ? 'SUCCESS' : 'FAILED',
        ];

        if ([] !== $result->packages) {
            $data['packages'] = array_map(
                $this->formatPackage(...),
                $result->packages
            );
            $data['package_count'] = $result->getPackageCount();
        }

        if ([] !== $result->dependencies) {
            $data['dependencies'] = array_map(
                $this->formatDependency(...),
                $result->dependencies
            );
        }

        if ($result->hasErrors()) {
            $data['errors'] = $result->errors;
        }

        if ($result->hasWarnings()) {
            $data['warnings'] = $result->warnings;
        }

        return toon($data);
    }

    private function formatSummary(ParsedResult $result): string
    {
        $data = [
            'command' => $result->command,
            'status' => $result->isSuccessful() ? 'SUCCESS' : 'FAILED',
        ];

        if ([] !== $result->packages) {
            $data['package_count'] = $result->getPackageCount();
        }

        if ([] !== $result->dependencies) {
            $data['dependency_count'] = \count($result->dependencies);
        }

        if ($result->hasErrors()) {
            $data['error_count'] = \count($result->errors);
        }

        if ($result->hasWarnings()) {
            $data['warning_count'] = \count($result->warnings);
        }

        return toon($data);
    }

    private function formatDetailed(ParsedResult $result): string
    {
        $data = [
            'command' => $result->command,
            'status' => $result->isSuccessful() ? 'SUCCESS' : 'FAILED',
        ];

        if ([] !== $result->packages) {
            $data['packages'] = $result->packages;
            $data['package_count'] = $result->getPackageCount();
        }

        if ([] !== $result->dependencies) {
            $data['dependencies'] = $result->dependencies;
        }

        if ($result->hasErrors()) {
            $data['errors'] = $result->errors;
        }

        if ($result->hasWarnings()) {
            $data['warnings'] = $result->warnings;
        }

        if ([] !== $result->metadata) {
            // Include relevant metadata but exclude raw output in detailed mode
            $metadata = $result->metadata;
            unset($metadata['output']);
            if ([] !== $metadata) {
                $data['metadata'] = $metadata;
            }
        }

        return toon($data);
    }

    /**
     * @param array<string, string> $pkg
     *
     * @return array<string, string>
     */
    private function formatPackage(array $pkg): array
    {
        $formatted = [
            'name' => $pkg['name'],
            'version' => $pkg['version'],
        ];

        // Include action for install/update/require commands
        if (isset($pkg['action'])) {
            $formatted['action'] = $pkg['action'];
        }

        // Include previous version for updates/downgrades
        if (isset($pkg['previous_version'])) {
            $formatted['from'] = $pkg['previous_version'];
        }

        return $formatted;
    }

    /**
     * @param array<string, string> $dep
     *
     * @return array<string, string>
     */
    private function formatDependency(array $dep): array
    {
        // Format for why/why-not commands (has 'target' key)
        if (isset($dep['target'])) {
            return [
                'package' => $dep['package'],
                'version' => $dep['version'] ?? '',
                'relation' => $dep['requires'] ?? '',
                'target' => $dep['target'],
                'constraint' => $dep['constraint'] ?? '',
            ];
        }

        // Standard format
        return [
            'package' => $dep['package'],
            'requires' => $dep['requires'] ?? '',
            'constraint' => $dep['constraint'] ?? $dep['version'] ?? '',
        ];
    }
}
