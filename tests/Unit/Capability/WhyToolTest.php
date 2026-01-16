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

use MatesOfMate\ComposerExtension\Capability\WhyTool;
use MatesOfMate\ComposerExtension\Formatter\ToonFormatter;
use MatesOfMate\ComposerExtension\Parser\OutputParser;
use MatesOfMate\ComposerExtension\Parser\ParsedResult;
use MatesOfMate\ComposerExtension\Runner\ComposerRunner;
use MatesOfMate\ComposerExtension\Runner\RunResult;
use PHPUnit\Framework\TestCase;

/**
 * @author Johannes Wachter <johannes@sulu.io>
 */
class WhyToolTest extends TestCase
{
    public function testExecuteWithJsonOutput(): void
    {
        $runner = $this->createMock(ComposerRunner::class);
        $parser = $this->createMock(OutputParser::class);
        $formatter = $this->createMock(ToonFormatter::class);

        $jsonOutput = [
            ['symfony/framework-bundle', 'requires', 'psr/log (^1|^2|^3)'],
        ];
        $runResult = new RunResult(0, json_encode($jsonOutput, \JSON_THROW_ON_ERROR), '', true);
        $parsedResult = new ParsedResult(success: true, command: 'why');

        $runner->expects($this->once())
            ->method('run')
            ->with(['why', 'psr/log'], true)
            ->willReturn($runResult);

        $parser->expects($this->once())
            ->method('parseWhyOutput')
            ->with($jsonOutput, 'psr/log')
            ->willReturn($parsedResult);

        $formatter->method('format')->willReturn('formatted output');

        $tool = new WhyTool($runner, $parser, $formatter);
        $result = $tool->execute('psr/log');

        $this->assertSame('formatted output', $result);
    }

    public function testExecuteWithNonJsonOutput(): void
    {
        $runner = $this->createMock(ComposerRunner::class);
        $parser = $this->createMock(OutputParser::class);
        $formatter = $this->createMock(ToonFormatter::class);

        $runResult = new RunResult(1, 'error', 'error output', false);
        $parsedResult = new ParsedResult(success: false, command: 'why');

        $runner->method('run')->willReturn($runResult);

        $parser->expects($this->once())
            ->method('parseCommandOutput')
            ->with($runResult, 'why')
            ->willReturn($parsedResult);

        $formatter->method('format')->willReturn('formatted output');

        $tool = new WhyTool($runner, $parser, $formatter);
        $result = $tool->execute('unknown/package');

        $this->assertSame('formatted output', $result);
    }
}
