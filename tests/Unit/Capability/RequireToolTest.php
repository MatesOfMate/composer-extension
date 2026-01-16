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

use MatesOfMate\ComposerExtension\Capability\RequireTool;
use MatesOfMate\ComposerExtension\Formatter\ToonFormatter;
use MatesOfMate\ComposerExtension\Parser\OutputParser;
use MatesOfMate\ComposerExtension\Parser\ParsedResult;
use MatesOfMate\ComposerExtension\Runner\ComposerRunner;
use MatesOfMate\ComposerExtension\Runner\RunResult;
use PHPUnit\Framework\TestCase;

/**
 * @author Johannes Wachter <johannes@sulu.io>
 */
class RequireToolTest extends TestCase
{
    public function testExecuteWithPackageOnly(): void
    {
        $runner = $this->createMock(ComposerRunner::class);
        $parser = $this->createMock(OutputParser::class);
        $formatter = $this->createMock(ToonFormatter::class);

        $runResult = new RunResult(0, 'output', '', false);
        $parsedResult = new ParsedResult(success: true, command: 'require');

        $runner->expects($this->once())
            ->method('run')
            ->with($this->callback(static fn (array $args): bool => \in_array('require', $args, true)
                && \in_array('symfony/console', $args, true)))
            ->willReturn($runResult);

        $parser->method('parseCommandOutput')->willReturn($parsedResult);
        $formatter->method('format')->willReturn('formatted output');

        $tool = new RequireTool($runner, $parser, $formatter);
        $result = $tool->execute('symfony/console');

        $this->assertSame('formatted output', $result);
    }

    public function testExecuteWithVersion(): void
    {
        $runner = $this->createMock(ComposerRunner::class);
        $parser = $this->createMock(OutputParser::class);
        $formatter = $this->createMock(ToonFormatter::class);

        $runResult = new RunResult(0, 'output', '', false);
        $parsedResult = new ParsedResult(success: true, command: 'require');

        $runner->expects($this->once())
            ->method('run')
            ->with($this->callback(static fn (array $args): bool => \in_array('symfony/console:^6.4', $args, true)))
            ->willReturn($runResult);

        $parser->method('parseCommandOutput')->willReturn($parsedResult);
        $formatter->method('format')->willReturn('formatted output');

        $tool = new RequireTool($runner, $parser, $formatter);
        $result = $tool->execute('symfony/console', '^6.4');

        $this->assertSame('formatted output', $result);
    }

    public function testExecuteWithDev(): void
    {
        $runner = $this->createMock(ComposerRunner::class);
        $parser = $this->createMock(OutputParser::class);
        $formatter = $this->createMock(ToonFormatter::class);

        $runResult = new RunResult(0, 'output', '', false);
        $parsedResult = new ParsedResult(success: true, command: 'require');

        $runner->expects($this->once())
            ->method('run')
            ->with($this->callback(static fn (array $args): bool => \in_array('--dev', $args, true)))
            ->willReturn($runResult);

        $parser->method('parseCommandOutput')->willReturn($parsedResult);
        $formatter->method('format')->willReturn('formatted output');

        $tool = new RequireTool($runner, $parser, $formatter);
        $result = $tool->execute('phpunit/phpunit', dev: true);

        $this->assertSame('formatted output', $result);
    }
}
