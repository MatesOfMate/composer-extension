<?php

/*
 * This file is part of the MatesOfMate Organisation.
 *
 * (c) Johannes Wachter <johannes@sulu.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MatesOfMate\ComposerExtension\Tests\Unit\Config;

use MatesOfMate\ComposerExtension\Config\ConfigurationDetector;
use PHPUnit\Framework\TestCase;

/**
 * @author Johannes Wachter <johannes@sulu.io>
 */
class ConfigurationDetectorTest extends TestCase
{
    public function testDetectFindsComposerJson(): void
    {
        $projectRoot = \dirname(__DIR__, 3);
        $detector = new ConfigurationDetector($projectRoot);

        $path = $detector->detect();

        $this->assertNotNull($path);
        $this->assertStringEndsWith('composer.json', $path);
        $this->assertFileExists($path);
    }

    public function testGetComposerJsonReturnsArray(): void
    {
        $projectRoot = \dirname(__DIR__, 3);
        $detector = new ConfigurationDetector($projectRoot);

        $config = $detector->getComposerJson();

        $this->assertIsArray($config);
        $this->assertArrayHasKey('name', $config);
        $this->assertSame('matesofmate/composer-extension', $config['name']);
    }

    public function testDetectReturnsNullForInvalidPath(): void
    {
        $detector = new ConfigurationDetector('/nonexistent/path');

        $path = $detector->detect();

        $this->assertNull($path);
    }

    public function testGetComposerJsonReturnsEmptyArrayForInvalidPath(): void
    {
        $detector = new ConfigurationDetector('/nonexistent/path');

        $config = $detector->getComposerJson();

        $this->assertSame([], $config);
    }
}
