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
    public function testExecuteReturnsFormattedOutput(): void
    {
        $runner = $this->createMock(ComposerRunner::class);
        $parser = $this->createMock(OutputParser::class);
        $formatter = $this->createMock(ToonFormatter::class);

        $runResult = new RunResult(0, 'psr/log is required by symfony/framework-bundle', '', false);
        $parsedResult = new ParsedResult(success: true, command: 'why');

        $runner->expects($this->once())
            ->method('run')
            ->with(['why', 'psr/log'])
            ->willReturn($runResult);

        $parser->expects($this->once())
            ->method('parseCommandOutput')
            ->with($runResult, 'why')
            ->willReturn($parsedResult);

        $formatter->expects($this->once())
            ->method('format')
            ->with($parsedResult, 'default')
            ->willReturn('formatted output');

        $tool = new WhyTool($runner, $parser, $formatter);
        $result = $tool->execute('psr/log');

        $this->assertSame('formatted output', $result);
    }

    public function testExecuteWithSummaryMode(): void
    {
        $runner = $this->createMock(ComposerRunner::class);
        $parser = $this->createMock(OutputParser::class);
        $formatter = $this->createMock(ToonFormatter::class);

        $runResult = new RunResult(0, 'output', '', false);
        $parsedResult = new ParsedResult(success: true, command: 'why');

        $runner->method('run')->willReturn($runResult);
        $parser->method('parseCommandOutput')->willReturn($parsedResult);

        $formatter->expects($this->once())
            ->method('format')
            ->with($parsedResult, 'summary')
            ->willReturn('summary output');

        $tool = new WhyTool($runner, $parser, $formatter);
        $result = $tool->execute('psr/log', 'summary');

        $this->assertSame('summary output', $result);
    }
}
