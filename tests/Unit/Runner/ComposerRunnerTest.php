<?php

/*
 * This file is part of the MatesOfMate Organisation.
 *
 * (c) Johannes Wachter <johannes@sulu.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MatesOfMate\ComposerExtension\Tests\Unit\Runner;

use MatesOfMate\Common\Process\ProcessExecutorInterface;
use MatesOfMate\Common\Process\ProcessResult;
use MatesOfMate\ComposerExtension\Runner\ComposerRunner;
use PHPUnit\Framework\TestCase;

/**
 * @author Johannes Wachter <johannes@sulu.io>
 */
class ComposerRunnerTest extends TestCase
{
    public function testRunExecutesComposerCommand(): void
    {
        $executor = $this->createMock(ProcessExecutorInterface::class);
        $executor->expects($this->once())
            ->method('execute')
            ->with(
                'composer',
                ['install', '--prefer-dist'],
                300,
                true
            )
            ->willReturn(new ProcessResult(
                exitCode: 0,
                output: 'Installing dependencies',
                errorOutput: '',
            ));

        $runner = new ComposerRunner($executor);
        $result = $runner->run(['install', '--prefer-dist']);

        $this->assertTrue($result->isSuccessful());
        $this->assertSame('Installing dependencies', $result->output);
        $this->assertFalse($result->isJson);
    }

    public function testRunWithJsonOutputAddsFormatFlag(): void
    {
        $executor = $this->createMock(ProcessExecutorInterface::class);
        $executor->expects($this->once())
            ->method('execute')
            ->with(
                'composer',
                ['why', 'symfony/console', '--format=json'],
                300,
                true
            )
            ->willReturn(new ProcessResult(
                exitCode: 0,
                output: '[]',
                errorOutput: '',
            ));

        $runner = new ComposerRunner($executor);
        $result = $runner->run(['why', 'symfony/console'], jsonOutput: true);

        $this->assertTrue($result->isSuccessful());
        $this->assertTrue($result->isJson);
    }

    public function testRunCapturesErrorOutput(): void
    {
        $executor = $this->createMock(ProcessExecutorInterface::class);
        $executor->expects($this->once())
            ->method('execute')
            ->willReturn(new ProcessResult(
                exitCode: 1,
                output: '',
                errorOutput: 'Package not found',
            ));

        $runner = new ComposerRunner($executor);
        $result = $runner->run(['require', 'invalid/package']);

        $this->assertFalse($result->isSuccessful());
        $this->assertSame('Package not found', $result->errorOutput);
    }
}
