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

use MatesOfMate\ComposerExtension\Runner\RunResult;
use PHPUnit\Framework\TestCase;

/**
 * @author Johannes Wachter <johannes@sulu.io>
 */
class RunResultTest extends TestCase
{
    public function testIsSuccessfulWithExitCodeZero(): void
    {
        $result = new RunResult(
            exitCode: 0,
            output: 'Success output',
            errorOutput: '',
            isJson: false,
        );

        $this->assertTrue($result->isSuccessful());
    }

    public function testIsSuccessfulWithNonZeroExitCode(): void
    {
        $result = new RunResult(
            exitCode: 1,
            output: '',
            errorOutput: 'Error message',
            isJson: false,
        );

        $this->assertFalse($result->isSuccessful());
    }

    public function testGetJsonOutputReturnsDecodedArray(): void
    {
        $jsonData = ['name' => 'test/package', 'version' => '1.0.0'];
        $result = new RunResult(
            exitCode: 0,
            output: json_encode($jsonData, \JSON_THROW_ON_ERROR),
            errorOutput: '',
            isJson: true,
        );

        $this->assertSame($jsonData, $result->getJsonOutput());
    }

    public function testGetJsonOutputThrowsExceptionWhenNotJson(): void
    {
        $result = new RunResult(
            exitCode: 0,
            output: 'plain text',
            errorOutput: '',
            isJson: false,
        );

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Output is not JSON format');

        $result->getJsonOutput();
    }

    public function testGetJsonOutputReturnsEmptyArrayForNonArrayJson(): void
    {
        $result = new RunResult(
            exitCode: 0,
            output: '"just a string"',
            errorOutput: '',
            isJson: true,
        );

        $this->assertSame([], $result->getJsonOutput());
    }
}
