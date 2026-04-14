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

    /**
     * @param bool   $preferDist         prefer distribution archives over source installs
     * @param bool   $noDev              skip development dependencies
     * @param bool   $optimizeAutoloader optimize the generated Composer autoloader
     * @param string $mode               output detail level: default, summary, or detailed
     */
    #[McpTool(
        name: 'composer-install',
        description: 'Install Composer dependencies from the project lock file.'
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
