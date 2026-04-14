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

use MatesOfMate\ComposerExtension\Capability\ExplainTool;
use MatesOfMate\ComposerExtension\Formatter\ToonFormatter;
use MatesOfMate\ComposerExtension\Parser\OutputParser;
use MatesOfMate\ComposerExtension\Parser\ParsedResult;
use MatesOfMate\ComposerExtension\Runner\ComposerRunner;
use MatesOfMate\ComposerExtension\Runner\RunResult;
use PHPUnit\Framework\TestCase;

/**
 * @author Johannes Wachter <johannes@sulu.io>
 */
class ExplainToolTest extends TestCase
{
    public function testExecuteUsesWhyWhenVersionIsOmitted(): void
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

        $tool = new ExplainTool($runner, $parser, $formatter);
        $result = $tool->execute('psr/log');

        $this->assertSame('formatted output', $result);
    }

    public function testExecuteUsesWhyNotWhenVersionIsProvided(): void
    {
        $runner = $this->createMock(ComposerRunner::class);
        $parser = $this->createMock(OutputParser::class);
        $formatter = $this->createMock(ToonFormatter::class);

        $runResult = new RunResult(1, 'conflict output', '', false);
        $parsedResult = new ParsedResult(success: true, command: 'why-not');

        $runner->expects($this->once())
            ->method('run')
            ->with(['why-not', 'psr/log', '^3.0'])
            ->willReturn($runResult);

        $parser->expects($this->once())
            ->method('parseCommandOutput')
            ->with($runResult, 'why-not')
            ->willReturn($parsedResult);

        $formatter->expects($this->once())
            ->method('format')
            ->with($parsedResult, 'summary')
            ->willReturn('summary output');

        $tool = new ExplainTool($runner, $parser, $formatter);
        $result = $tool->execute('psr/log', '^3.0', 'summary');

        $this->assertSame('summary output', $result);
    }
}
