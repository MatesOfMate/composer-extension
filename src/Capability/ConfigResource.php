<?php

/*
 * This file is part of the MatesOfMate Organisation.
 *
 * (c) Johannes Wachter <johannes@sulu.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MatesOfMate\ComposerExtension\Capability;

use MatesOfMate\ComposerExtension\Config\ConfigurationDetector;
use Mcp\Capability\Attribute\McpResource;
use Symfony\AI\Mate\Encoding\ResponseEncoder;

/**
 * Provides composer.json content to AI assistants as an encoded resource payload.
 *
 * @author Johannes Wachter <johannes@sulu.io>
 */
class ConfigResource
{
    public function __construct(
        private readonly ConfigurationDetector $configDetector,
    ) {
    }

    /**
     * @return array{uri: string, mimeType: string, text: string}
     */
    #[McpResource(
        uri: 'composer://config',
        name: 'Composer Configuration',
        description: 'Provides the content of composer.json file including dependencies, autoloading, and scripts configuration as an encoded structured payload.',
        mimeType: 'text/plain'
    )]
    public function getConfiguration(): array
    {
        $config = $this->configDetector->getComposerJson();

        return [
            'uri' => 'composer://config',
            'mimeType' => 'text/plain',
            'text' => ResponseEncoder::encode($config),
        ];
    }
}
