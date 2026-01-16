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

use MatesOfMate\ComposerExtension\Parser\OutputParser;
use MatesOfMate\ComposerExtension\Runner\RunResult;
use PHPUnit\Framework\TestCase;

/**
 * @author Johannes Wachter <johannes@sulu.io>
 */
class OutputParserTest extends TestCase
{
    private OutputParser $parser;

    protected function setUp(): void
    {
        $this->parser = new OutputParser();
    }

    public function testParseCommandOutputWithSuccess(): void
    {
        $runResult = new RunResult(
            exitCode: 0,
            output: 'Installing dependencies',
            errorOutput: '',
            isJson: false,
        );

        $result = $this->parser->parseCommandOutput($runResult, 'install');

        $this->assertTrue($result->isSuccessful());
        $this->assertSame('install', $result->command);
        $this->assertFalse($result->hasErrors());
    }

    public function testParseCommandOutputWithErrors(): void
    {
        $runResult = new RunResult(
            exitCode: 1,
            output: '',
            errorOutput: "Package not found\nVersion constraint invalid",
            isJson: false,
        );

        $result = $this->parser->parseCommandOutput($runResult, 'require');

        $this->assertFalse($result->isSuccessful());
        $this->assertTrue($result->hasErrors());
        $this->assertContains('Package not found', $result->errors);
    }

    public function testParseCommandOutputWithWarnings(): void
    {
        $runResult = new RunResult(
            exitCode: 0,
            output: 'Warning: Package is deprecated',
            errorOutput: '',
            isJson: false,
        );

        $result = $this->parser->parseCommandOutput($runResult, 'install');

        $this->assertTrue($result->isSuccessful());
        $this->assertTrue($result->hasWarnings());
    }

    public function testParseWhyOutput(): void
    {
        $json = [
            ['symfony/framework-bundle', 'requires', 'symfony/console (^5.4|^6.0|^7.0)'],
            ['symfony/console', 'requires', 'psr/log (^1|^2|^3)'],
        ];

        $result = $this->parser->parseWhyOutput($json, 'psr/log');

        $this->assertTrue($result->isSuccessful());
        $this->assertSame('why', $result->command);
        $this->assertCount(2, $result->dependencies);
        $this->assertSame('symfony/framework-bundle', $result->dependencies[0]['package']);
    }

    public function testParseWhyNotOutput(): void
    {
        $json = [
            ['symfony/console', 'requires', 'php (>=8.1)'],
        ];

        $result = $this->parser->parseWhyNotOutput($json, 'php', '7.4');

        $this->assertTrue($result->isSuccessful());
        $this->assertSame('why-not', $result->command);
        $this->assertCount(1, $result->dependencies);
        $this->assertSame('7.4', $result->metadata['target_version']);
    }

    public function testParseShowOutput(): void
    {
        $json = [
            'installed' => [
                ['name' => 'symfony/console', 'version' => '6.4.0', 'description' => 'Console component'],
                ['name' => 'symfony/process', 'version' => '6.4.0', 'description' => 'Process component'],
            ],
        ];

        $result = $this->parser->parseShowOutput($json);

        $this->assertTrue($result->isSuccessful());
        $this->assertSame('show', $result->command);
        $this->assertSame(2, $result->getPackageCount());
        $this->assertSame('symfony/console', $result->packages[0]['name']);
    }

    public function testParseShowOutputWithFlatArray(): void
    {
        $json = [
            ['name' => 'symfony/console', 'version' => '6.4.0', 'description' => 'Console component'],
        ];

        $result = $this->parser->parseShowOutput($json);

        $this->assertTrue($result->isSuccessful());
        $this->assertSame(1, $result->getPackageCount());
    }
}
