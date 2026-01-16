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

use MatesOfMate\ComposerExtension\Capability\InstallTool;
use MatesOfMate\ComposerExtension\Formatter\ToonFormatter;
use MatesOfMate\ComposerExtension\Parser\OutputParser;
use MatesOfMate\ComposerExtension\Parser\ParsedResult;
use MatesOfMate\ComposerExtension\Runner\ComposerRunner;
use MatesOfMate\ComposerExtension\Runner\RunResult;
use PHPUnit\Framework\TestCase;

/**
 * @author Johannes Wachter <johannes@sulu.io>
 */
class InstallToolTest extends TestCase
{
    public function testExecuteWithDefaultParameters(): void
    {
        $runner = $this->createMock(ComposerRunner::class);
        $parser = $this->createMock(OutputParser::class);
        $formatter = $this->createMock(ToonFormatter::class);

        $runResult = new RunResult(0, 'output', '', false);
        $parsedResult = new ParsedResult(success: true, command: 'install');

        $runner->expects($this->once())
            ->method('run')
            ->with($this->callback(static fn (array $args): bool => \in_array('install', $args, true)
                && \in_array('--prefer-dist', $args, true)))
            ->willReturn($runResult);

        $parser->expects($this->once())
            ->method('parseCommandOutput')
            ->with($runResult, 'install')
            ->willReturn($parsedResult);

        $formatter->expects($this->once())
            ->method('format')
            ->with($parsedResult, 'default')
            ->willReturn('formatted output');

        $tool = new InstallTool($runner, $parser, $formatter);
        $result = $tool->execute();

        $this->assertSame('formatted output', $result);
    }

    public function testExecuteWithNoDev(): void
    {
        $runner = $this->createMock(ComposerRunner::class);
        $parser = $this->createMock(OutputParser::class);
        $formatter = $this->createMock(ToonFormatter::class);

        $runResult = new RunResult(0, 'output', '', false);
        $parsedResult = new ParsedResult(success: true, command: 'install');

        $runner->expects($this->once())
            ->method('run')
            ->with($this->callback(static fn (array $args): bool => \in_array('--no-dev', $args, true)))
            ->willReturn($runResult);

        $parser->method('parseCommandOutput')->willReturn($parsedResult);
        $formatter->method('format')->willReturn('formatted output');

        $tool = new InstallTool($runner, $parser, $formatter);
        $result = $tool->execute(noDev: true);

        $this->assertSame('formatted output', $result);
    }

    public function testExecuteWithOptimizeAutoloader(): void
    {
        $runner = $this->createMock(ComposerRunner::class);
        $parser = $this->createMock(OutputParser::class);
        $formatter = $this->createMock(ToonFormatter::class);

        $runResult = new RunResult(0, 'output', '', false);
        $parsedResult = new ParsedResult(success: true, command: 'install');

        $runner->expects($this->once())
            ->method('run')
            ->with($this->callback(static fn (array $args): bool => \in_array('--optimize-autoloader', $args, true)))
            ->willReturn($runResult);

        $parser->method('parseCommandOutput')->willReturn($parsedResult);
        $formatter->method('format')->willReturn('formatted output');

        $tool = new InstallTool($runner, $parser, $formatter);
        $result = $tool->execute(optimizeAutoloader: true);

        $this->assertSame('formatted output', $result);
    }

    public function testExecuteWithSummaryMode(): void
    {
        $runner = $this->createMock(ComposerRunner::class);
        $parser = $this->createMock(OutputParser::class);
        $formatter = $this->createMock(ToonFormatter::class);

        $runResult = new RunResult(0, 'output', '', false);
        $parsedResult = new ParsedResult(success: true, command: 'install');

        $runner->method('run')->willReturn($runResult);
        $parser->method('parseCommandOutput')->willReturn($parsedResult);

        $formatter->expects($this->once())
            ->method('format')
            ->with($parsedResult, 'summary')
            ->willReturn('summary output');

        $tool = new InstallTool($runner, $parser, $formatter);
        $result = $tool->execute(mode: 'summary');

        $this->assertSame('summary output', $result);
    }
}
