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
 * Updates dependencies to latest versions within constraints.
 *
 * @author Johannes Wachter <johannes@sulu.io>
 */
class UpdateTool
{
    public function __construct(
        private readonly ComposerRunner $runner,
        private readonly OutputParser $parser,
        private readonly ToonFormatter $formatter,
    ) {
    }

    #[McpTool(
        name: 'composer-update',
        description: 'Update Composer dependencies to latest versions within version constraints. Use when updating packages or resolving dependency conflicts. Available modes: "default" (status + errors/warnings), "summary" (just counts and status), "detailed" (full output with metadata).'
    )]
    public function execute(
        ?string $packages = null,
        bool $preferDist = true,
        bool $withDependencies = true,
        string $mode = 'default',
    ): string {
        $args = ['update'];

        if (null !== $packages && '' !== $packages) {
            $packageList = preg_split('/[\s,]+/', $packages, -1, \PREG_SPLIT_NO_EMPTY);
            if (\is_array($packageList)) {
                $args = [...$args, ...$packageList];
            }
        }

        if ($preferDist) {
            $args[] = '--prefer-dist';
        }
        if ($withDependencies) {
            $args[] = '--with-dependencies';
        }

        $runResult = $this->runner->run($args);
        $parsedResult = $this->parser->parseCommandOutput($runResult, 'update');

        return $this->formatter->format($parsedResult, $mode);
    }
}
