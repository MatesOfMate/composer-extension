<?php

/*
 * This file is part of the MatesOfMate Organisation.
 *
 * (c) Johannes Wachter <johannes@sulu.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MatesOfMate\ComposerExtension\Tests\Unit\Capability;

use MatesOfMate\ComposerExtension\Capability\ConfigResource;
use MatesOfMate\ComposerExtension\Config\ConfigurationDetector;
use PHPUnit\Framework\TestCase;
use Symfony\AI\Mate\Encoding\ResponseEncoder;

/**
 * @author Johannes Wachter <johannes@sulu.io>
 */
class ConfigResourceTest extends TestCase
{
    public function testGetConfigurationReturnsValidStructure(): void
    {
        $projectRoot = \dirname(__DIR__, 3);
        $configDetector = new ConfigurationDetector($projectRoot);

        $resource = new ConfigResource($configDetector);
        $result = $resource->getConfiguration();

        $this->assertArrayHasKey('uri', $result);
        $this->assertArrayHasKey('mimeType', $result);
        $this->assertArrayHasKey('text', $result);

        $this->assertSame('composer://config', $result['uri']);
        $this->assertSame('text/plain', $result['mimeType']);
    }

    public function testGetConfigurationReturnsDecodablePayload(): void
    {
        $projectRoot = \dirname(__DIR__, 3);
        $configDetector = new ConfigurationDetector($projectRoot);

        $resource = new ConfigResource($configDetector);
        $result = $resource->getConfiguration();

        $decoded = ResponseEncoder::decode($result['text']);

        $this->assertSame('matesofmate/composer-extension', $decoded['name']);
    }

    public function testGetConfigurationWithEmptyConfig(): void
    {
        $configDetector = $this->createMock(ConfigurationDetector::class);
        $configDetector->method('getComposerJson')->willReturn([]);

        $resource = new ConfigResource($configDetector);
        $result = $resource->getConfiguration();

        $this->assertSame('composer://config', $result['uri']);
        $this->assertSame('text/plain', $result['mimeType']);
        $this->assertSame([], ResponseEncoder::decode($result['text']));
    }

    public function testGetConfigurationIncludesDependencies(): void
    {
        $configDetector = $this->createMock(ConfigurationDetector::class);
        $configDetector->method('getComposerJson')->willReturn([
            'name' => 'test/package',
            'require' => [
                'php' => '^8.2',
                'symfony/process' => '^6.4|^7.0',
            ],
        ]);

        $resource = new ConfigResource($configDetector);
        $result = $resource->getConfiguration();

        $decoded = ResponseEncoder::decode($result['text']);

        $this->assertSame('test/package', $decoded['name']);
        $this->assertSame('^8.2', $decoded['require']['php']);
    }
}
