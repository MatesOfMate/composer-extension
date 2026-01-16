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

use MatesOfMate\ComposerExtension\Capability\WhyNotTool;
use MatesOfMate\ComposerExtension\Formatter\ToonFormatter;
use MatesOfMate\ComposerExtension\Parser\OutputParser;
use MatesOfMate\ComposerExtension\Parser\ParsedResult;
use MatesOfMate\ComposerExtension\Runner\ComposerRunner;
use MatesOfMate\ComposerExtension\Runner\RunResult;
use PHPUnit\Framework\TestCase;

/**
 * @author Johannes Wachter <johannes@sulu.io>
 */
class WhyNotToolTest extends TestCase
{
    public function testExecuteWithPackageOnly(): void
    {
        $runner = $this->createMock(ComposerRunner::class);
        $parser = $this->createMock(OutputParser::class);
        $formatter = $this->createMock(ToonFormatter::class);

        $jsonOutput = [
            ['symfony/console', 'requires', 'php (>=8.1)'],
        ];
        $runResult = new RunResult(0, json_encode($jsonOutput, \JSON_THROW_ON_ERROR), '', true);
        $parsedResult = new ParsedResult(success: true, command: 'why-not');

        $runner->expects($this->once())
            ->method('run')
            ->with(['why-not', 'php'], true)
            ->willReturn($runResult);

        $parser->expects($this->once())
            ->method('parseWhyNotOutput')
            ->with($jsonOutput, 'php', null)
            ->willReturn($parsedResult);

        $formatter->method('format')->willReturn('formatted output');

        $tool = new WhyNotTool($runner, $parser, $formatter);
        $result = $tool->execute('php');

        $this->assertSame('formatted output', $result);
    }

    public function testExecuteWithVersion(): void
    {
        $runner = $this->createMock(ComposerRunner::class);
        $parser = $this->createMock(OutputParser::class);
        $formatter = $this->createMock(ToonFormatter::class);

        $jsonOutput = [];
        $runResult = new RunResult(0, json_encode($jsonOutput, \JSON_THROW_ON_ERROR), '', true);
        $parsedResult = new ParsedResult(success: true, command: 'why-not');

        $runner->expects($this->once())
            ->method('run')
            ->with(['why-not', 'php', '7.4'], true)
            ->willReturn($runResult);

        $parser->expects($this->once())
            ->method('parseWhyNotOutput')
            ->with($jsonOutput, 'php', '7.4')
            ->willReturn($parsedResult);

        $formatter->method('format')->willReturn('formatted output');

        $tool = new WhyNotTool($runner, $parser, $formatter);
        $result = $tool->execute('php', '7.4');

        $this->assertSame('formatted output', $result);
    }
}
