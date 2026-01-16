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
 * Shows why a package cannot be installed.
 *
 * @author Johannes Wachter <johannes@sulu.io>
 */
class WhyNotTool
{
    public function __construct(
        private readonly ComposerRunner $runner,
        private readonly OutputParser $parser,
        private readonly ToonFormatter $formatter,
    ) {
    }

    #[McpTool(
        name: 'composer-why-not',
        description: 'Show why a specific package version cannot be installed. Use this to diagnose dependency conflicts or version constraint issues. Available modes: "default" (conflict list), "summary" (just counts), "detailed" (full conflict details with metadata).'
    )]
    public function execute(
        string $package,
        ?string $version = null,
        string $mode = 'default',
    ): string {
        $args = ['why-not', $package];

        if (null !== $version) {
            $args[] = $version;
        }

        $runResult = $this->runner->run($args, jsonOutput: true);

        if ($runResult->isSuccessful() && $runResult->isJson) {
            try {
                $parsedResult = $this->parser->parseWhyNotOutput($runResult->getJsonOutput(), $package, $version);
            } catch (\JsonException) {
                $parsedResult = $this->parser->parseCommandOutput($runResult, 'why-not');
            }
        } else {
            $parsedResult = $this->parser->parseCommandOutput($runResult, 'why-not');
        }

        return $this->formatter->format($parsedResult, $mode);
    }
}
