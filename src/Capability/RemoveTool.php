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
 * Removes a package from composer.json.
 *
 * @author Johannes Wachter <johannes@sulu.io>
 */
class RemoveTool
{
    public function __construct(
        private readonly ComposerRunner $runner,
        private readonly OutputParser $parser,
        private readonly ToonFormatter $formatter,
    ) {
    }

    #[McpTool(
        name: 'composer-remove',
        description: 'Remove a package from composer.json. Use when removing a library or framework dependency from the project. Available modes: "default" (status + removed packages), "summary" (just counts and status), "detailed" (full output with metadata).'
    )]
    public function execute(
        string $package,
        bool $dev = false,
        string $mode = 'default',
    ): string {
        $args = ['remove', $package];

        if ($dev) {
            $args[] = '--dev';
        }

        $runResult = $this->runner->run($args);
        $parsedResult = $this->parser->parseCommandOutput($runResult, 'remove');

        return $this->formatter->format($parsedResult, $mode);
    }
}
