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
 * Shows which packages depend on a given package.
 *
 * @author Johannes Wachter <johannes@sulu.io>
 */
class WhyTool
{
    public function __construct(
        private readonly ComposerRunner $runner,
        private readonly OutputParser $parser,
        private readonly ToonFormatter $formatter,
    ) {
    }

    #[McpTool(
        name: 'composer-why',
        description: 'Show which packages depend on a specific package. Use this to understand why a package is installed or to trace dependency chains. Available modes: "default" (dependency list), "summary" (just counts), "detailed" (full dependency details).'
    )]
    public function execute(
        string $package,
        string $mode = 'default',
    ): string {
        $args = ['why', $package];

        $runResult = $this->runner->run($args, jsonOutput: true);

        if ($runResult->isSuccessful() && $runResult->isJson) {
            try {
                $parsedResult = $this->parser->parseWhyOutput($runResult->getJsonOutput(), $package);
            } catch (\JsonException) {
                $parsedResult = $this->parser->parseCommandOutput($runResult, 'why');
            }
        } else {
            $parsedResult = $this->parser->parseCommandOutput($runResult, 'why');
        }

        return $this->formatter->format($parsedResult, $mode);
    }
}
