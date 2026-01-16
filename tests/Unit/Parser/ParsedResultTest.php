<?php

/*
 * This file is part of the MatesOfMate Organisation.
 *
 * (c) Johannes Wachter <johannes@sulu.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MatesOfMate\ComposerExtension\Tests\Unit\Parser;

use MatesOfMate\ComposerExtension\Parser\ParsedResult;
use PHPUnit\Framework\TestCase;

/**
 * @author Johannes Wachter <johannes@sulu.io>
 */
class ParsedResultTest extends TestCase
{
    public function testIsSuccessful(): void
    {
        $result = new ParsedResult(
            success: true,
            command: 'install',
        );

        $this->assertTrue($result->isSuccessful());
    }

    public function testIsNotSuccessful(): void
    {
        $result = new ParsedResult(
            success: false,
            command: 'install',
        );

        $this->assertFalse($result->isSuccessful());
    }

    public function testGetPackageCount(): void
    {
        $result = new ParsedResult(
            success: true,
            command: 'show',
            packages: [
                ['name' => 'symfony/console', 'version' => '6.4.0'],
                ['name' => 'symfony/process', 'version' => '6.4.0'],
            ],
        );

        $this->assertSame(2, $result->getPackageCount());
    }

    public function testHasErrors(): void
    {
        $result = new ParsedResult(
            success: false,
            command: 'install',
            errors: ['Package not found'],
        );

        $this->assertTrue($result->hasErrors());
    }

    public function testHasNoErrors(): void
    {
        $result = new ParsedResult(
            success: true,
            command: 'install',
        );

        $this->assertFalse($result->hasErrors());
    }

    public function testHasWarnings(): void
    {
        $result = new ParsedResult(
            success: true,
            command: 'install',
            warnings: ['Deprecated package'],
        );

        $this->assertTrue($result->hasWarnings());
    }

    public function testHasNoWarnings(): void
    {
        $result = new ParsedResult(
            success: true,
            command: 'install',
        );

        $this->assertFalse($result->hasWarnings());
    }
}
