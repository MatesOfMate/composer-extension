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

use MatesOfMate\ComposerExtension\Capability\UpdateTool;
use MatesOfMate\ComposerExtension\Formatter\ToonFormatter;
use MatesOfMate\ComposerExtension\Parser\OutputParser;
use MatesOfMate\ComposerExtension\Parser\ParsedResult;
use MatesOfMate\ComposerExtension\Runner\ComposerRunner;
use MatesOfMate\ComposerExtension\Runner\RunResult;
use PHPUnit\Framework\TestCase;

/**
 * @author Johannes Wachter <johannes@sulu.io>
 */
class UpdateToolTest extends TestCase
{
    public function testExecuteWithNoPackages(): void
    {
        $runner = $this->createMock(ComposerRunner::class);
        $parser = $this->createMock(OutputParser::class);
        $formatter = $this->createMock(ToonFormatter::class);

        $runResult = new RunResult(0, 'output', '', false);
        $parsedResult = new ParsedResult(success: true, command: 'update');

        $runner->expects($this->once())
            ->method('run')
            ->with($this->callback(static fn (array $args): bool => \in_array('update', $args, true)
                && \in_array('--prefer-dist', $args, true)
                && \in_array('--with-dependencies', $args, true)))
            ->willReturn($runResult);

        $parser->method('parseCommandOutput')->willReturn($parsedResult);
        $formatter->method('format')->willReturn('formatted output');

        $tool = new UpdateTool($runner, $parser, $formatter);
        $result = $tool->execute();

        $this->assertSame('formatted output', $result);
    }

    public function testExecuteWithSpecificPackages(): void
    {
        $runner = $this->createMock(ComposerRunner::class);
        $parser = $this->createMock(OutputParser::class);
        $formatter = $this->createMock(ToonFormatter::class);

        $runResult = new RunResult(0, 'output', '', false);
        $parsedResult = new ParsedResult(success: true, command: 'update');

        $runner->expects($this->once())
            ->method('run')
            ->with($this->callback(static fn (array $args): bool => \in_array('symfony/console', $args, true)
                && \in_array('symfony/process', $args, true)))
            ->willReturn($runResult);

        $parser->method('parseCommandOutput')->willReturn($parsedResult);
        $formatter->method('format')->willReturn('formatted output');

        $tool = new UpdateTool($runner, $parser, $formatter);
        $result = $tool->execute('symfony/console, symfony/process');

        $this->assertSame('formatted output', $result);
    }

    public function testExecuteWithoutWithDependencies(): void
    {
        $runner = $this->createMock(ComposerRunner::class);
        $parser = $this->createMock(OutputParser::class);
        $formatter = $this->createMock(ToonFormatter::class);

        $runResult = new RunResult(0, 'output', '', false);
        $parsedResult = new ParsedResult(success: true, command: 'update');

        $runner->expects($this->once())
            ->method('run')
            ->with($this->callback(static fn (array $args): bool => !\in_array('--with-dependencies', $args, true)))
            ->willReturn($runResult);

        $parser->method('parseCommandOutput')->willReturn($parsedResult);
        $formatter->method('format')->willReturn('formatted output');

        $tool = new UpdateTool($runner, $parser, $formatter);
        $result = $tool->execute(withDependencies: false);

        $this->assertSame('formatted output', $result);
    }
}
