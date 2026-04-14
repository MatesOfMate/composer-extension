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
 * Explains dependency relations and conflicts for a package.
 *
 * @author Johannes Wachter <johannes@sulu.io>
 */
class ExplainTool
{
    public function __construct(
        private readonly ComposerRunner $runner,
        private readonly OutputParser $parser,
        private readonly ToonFormatter $formatter,
    ) {
    }

    /**
     * @param string      $package package name to inspect
     * @param string|null $version Version to diagnose. When omitted, the tool explains why the package is installed.
     * @param string      $mode    output detail level: default, summary, or detailed
     */
    #[McpTool(
        name: 'composer-explain',
        description: 'Explain why a package is installed or why a specific version cannot be installed.'
    )]
    public function execute(
        string $package,
        ?string $version = null,
        string $mode = 'default',
    ): string {
        $command = null === $version ? 'why' : 'why-not';
        $args = [$command, $package];

        if (null !== $version) {
            $args[] = $version;
        }

        $runResult = $this->runner->run($args);
        $parsedResult = $this->parser->parseCommandOutput($runResult, $command);

        return $this->formatter->format($parsedResult, $mode);
    }
}
