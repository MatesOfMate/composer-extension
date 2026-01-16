<?php

/*
 * This file is part of the MatesOfMate Organisation.
 *
 * (c) Johannes Wachter <johannes@sulu.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MatesOfMate\ComposerExtension\Config;

use MatesOfMate\Common\Config\ConfigurationDetector as CommonConfigurationDetector;

/**
 * Detects composer.json configuration file and extracts project information.
 *
 * @internal
 *
 * @author Johannes Wachter <johannes@sulu.io>
 */
class ConfigurationDetector
{
    private readonly CommonConfigurationDetector $detector;

    public function __construct(
        private readonly string $projectRoot,
    ) {
        $this->detector = new CommonConfigurationDetector([
            'composer.json',
        ]);
    }

    public function detect(?string $projectRoot = null): ?string
    {
        return $this->detector->detect($projectRoot ?: $this->projectRoot);
    }

    /**
     * @return array<string, mixed>
     */
    public function getComposerJson(): array
    {
        $path = $this->detect();
        if (null === $path || !file_exists($path)) {
            return [];
        }

        $content = file_get_contents($path);
        if (false === $content) {
            return [];
        }

        $decoded = json_decode($content, true, 512, \JSON_THROW_ON_ERROR);

        if (!\is_array($decoded)) {
            return [];
        }

        return $decoded;
    }
}
