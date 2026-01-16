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
 * Installs dependencies from composer.json and composer.lock.
 *
 * @author Johannes Wachter <johannes@sulu.io>
 */
class InstallTool
{
    public function __construct(
        private readonly ComposerRunner $runner,
        private readonly OutputParser $parser,
        private readonly ToonFormatter $formatter,
    ) {
    }

    #[McpTool(
        name: 'composer-install',
        description: 'Install Composer dependencies from composer.json and composer.lock. Use this when setting up a project or ensuring dependencies are up to date. Available modes: "default" (status + errors/warnings), "summary" (just counts and status), "detailed" (full output with metadata).'
    )]
    public function execute(
        bool $preferDist = true,
        bool $noDev = false,
        bool $optimizeAutoloader = false,
        string $mode = 'default',
    ): string {
        $args = ['install'];

        if ($preferDist) {
            $args[] = '--prefer-dist';
        }
        if ($noDev) {
            $args[] = '--no-dev';
        }
        if ($optimizeAutoloader) {
            $args[] = '--optimize-autoloader';
        }

        $runResult = $this->runner->run($args);
        $parsedResult = $this->parser->parseCommandOutput($runResult, 'install');

        return $this->formatter->format($parsedResult, $mode);
    }
}
