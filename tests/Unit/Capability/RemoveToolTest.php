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

use MatesOfMate\ComposerExtension\Capability\RemoveTool;
use MatesOfMate\ComposerExtension\Formatter\ToonFormatter;
use MatesOfMate\ComposerExtension\Parser\OutputParser;
use MatesOfMate\ComposerExtension\Parser\ParsedResult;
use MatesOfMate\ComposerExtension\Runner\ComposerRunner;
use MatesOfMate\ComposerExtension\Runner\RunResult;
use PHPUnit\Framework\TestCase;

/**
 * @author Johannes Wachter <johannes@sulu.io>
 */
class RemoveToolTest extends TestCase
{
    public function testExecuteRemovesPackage(): void
    {
        $runner = $this->createMock(ComposerRunner::class);
        $parser = $this->createMock(OutputParser::class);
        $formatter = $this->createMock(ToonFormatter::class);

        $runResult = new RunResult(0, '- Removing vendor/package (v1.2.3)', '', false);
        $parsedResult = new ParsedResult(
            success: true,
            command: 'remove',
            packages: [['name' => 'vendor/package', 'version' => 'v1.2.3', 'action' => 'removed']],
        );

        $runner->expects($this->once())
            ->method('run')
            ->with(['remove', 'vendor/package'])
            ->willReturn($runResult);

        $parser->expects($this->once())
            ->method('parseCommandOutput')
            ->with($runResult, 'remove')
            ->willReturn($parsedResult);

        $formatter->expects($this->once())
            ->method('format')
            ->with($parsedResult, 'default')
            ->willReturn('formatted output');

        $tool = new RemoveTool($runner, $parser, $formatter);
        $result = $tool->execute('vendor/package');

        $this->assertSame('formatted output', $result);
    }

    public function testExecuteWithDevFlag(): void
    {
        $runner = $this->createMock(ComposerRunner::class);
        $parser = $this->createMock(OutputParser::class);
        $formatter = $this->createMock(ToonFormatter::class);

        $runResult = new RunResult(0, '', '', false);
        $parsedResult = new ParsedResult(success: true, command: 'remove');

        $runner->expects($this->once())
            ->method('run')
            ->with(['remove', 'vendor/package', '--dev'])
            ->willReturn($runResult);

        $parser->method('parseCommandOutput')->willReturn($parsedResult);
        $formatter->method('format')->willReturn('formatted output');

        $tool = new RemoveTool($runner, $parser, $formatter);
        $result = $tool->execute('vendor/package', dev: true);

        $this->assertSame('formatted output', $result);
    }

    public function testExecuteWithSummaryMode(): void
    {
        $runner = $this->createMock(ComposerRunner::class);
        $parser = $this->createMock(OutputParser::class);
        $formatter = $this->createMock(ToonFormatter::class);

        $runResult = new RunResult(0, '', '', false);
        $parsedResult = new ParsedResult(success: true, command: 'remove');

        $runner->method('run')->willReturn($runResult);
        $parser->method('parseCommandOutput')->willReturn($parsedResult);

        $formatter->expects($this->once())
            ->method('format')
            ->with($parsedResult, 'summary')
            ->willReturn('summary output');

        $tool = new RemoveTool($runner, $parser, $formatter);
        $result = $tool->execute('vendor/package', mode: 'summary');

        $this->assertSame('summary output', $result);
    }
}
