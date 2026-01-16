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

use MatesOfMate\ComposerExtension\Formatter\ToonFormatter;
use MatesOfMate\ComposerExtension\Parser\OutputParser;
use MatesOfMate\ComposerExtension\Runner\ComposerRunner;
use Mcp\Capability\Attribute\McpTool;

/**
 * Adds a new package requirement to composer.json.
 *
 * @author Johannes Wachter <johannes@sulu.io>
 */
class RequireTool
{
    public function __construct(
        private readonly ComposerRunner $runner,
        private readonly OutputParser $parser,
        private readonly ToonFormatter $formatter,
    ) {
    }

    #[McpTool(
        name: 'composer-require',
        description: 'Add a new package requirement to composer.json. Use when adding a new library or framework dependency to the project. Available modes: "default" (status + errors/warnings), "summary" (just counts and status), "detailed" (full output with metadata).'
    )]
    public function execute(
        string $package,
        ?string $version = null,
        bool $dev = false,
        string $mode = 'default',
    ): string {
        $args = ['require'];

        $packageSpec = null !== $version ? "{$package}:{$version}" : $package;
        $args[] = $packageSpec;

        if ($dev) {
            $args[] = '--dev';
        }

        $runResult = $this->runner->run($args);
        $parsedResult = $this->parser->parseCommandOutput($runResult, 'require');

        return $this->formatter->format($parsedResult, $mode);
    }
}
