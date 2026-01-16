<?php

/*
 * This file is part of the MatesOfMate Organisation.
 *
 * (c) Johannes Wachter <johannes@sulu.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MatesOfMate\ComposerExtension\Tests\Unit\Formatter;

use MatesOfMate\ComposerExtension\Formatter\ToonFormatter;
use MatesOfMate\ComposerExtension\Parser\ParsedResult;
use PHPUnit\Framework\TestCase;

/**
 * @author Johannes Wachter <johannes@sulu.io>
 */
class ToonFormatterTest extends TestCase
{
    private ToonFormatter $formatter;

    protected function setUp(): void
    {
        $this->formatter = new ToonFormatter();
    }

    public function testFormatDefaultMode(): void
    {
        $result = new ParsedResult(
            success: true,
            command: 'install',
            packages: [
                ['name' => 'symfony/console', 'version' => '6.4.0'],
            ],
        );

        $output = $this->formatter->format($result, 'default');

        $this->assertStringContainsString('install', $output);
        $this->assertStringContainsString('SUCCESS', $output);
        $this->assertStringContainsString('symfony/console', $output);
    }

    public function testFormatSummaryMode(): void
    {
        $result = new ParsedResult(
            success: true,
            command: 'show',
            packages: [
                ['name' => 'symfony/console', 'version' => '6.4.0'],
                ['name' => 'symfony/process', 'version' => '6.4.0'],
            ],
        );

        $output = $this->formatter->format($result, 'summary');

        $this->assertStringContainsString('show', $output);
        $this->assertStringContainsString('SUCCESS', $output);
        $this->assertStringContainsString('package_count', $output);
    }

    public function testFormatDetailedMode(): void
    {
        $result = new ParsedResult(
            success: false,
            command: 'require',
            errors: ['Package not found'],
            metadata: ['target_package' => 'invalid/package'],
        );

        $output = $this->formatter->format($result, 'detailed');

        $this->assertStringContainsString('require', $output);
        $this->assertStringContainsString('FAILED', $output);
        $this->assertStringContainsString('Package not found', $output);
        $this->assertStringContainsString('target_package', $output);
    }

    public function testFormatWithDependencies(): void
    {
        $result = new ParsedResult(
            success: true,
            command: 'why',
            dependencies: [
                ['package' => 'symfony/framework-bundle', 'requires' => 'symfony/console', 'constraint' => '^6.4'],
            ],
        );

        $output = $this->formatter->format($result, 'default');

        $this->assertStringContainsString('dependencies', $output);
        $this->assertStringContainsString('symfony/framework-bundle', $output);
    }

    public function testFormatWithWarnings(): void
    {
        $result = new ParsedResult(
            success: true,
            command: 'install',
            warnings: ['Package is deprecated'],
        );

        $output = $this->formatter->format($result, 'default');

        $this->assertStringContainsString('warnings', $output);
        $this->assertStringContainsString('Package is deprecated', $output);
    }

    public function testFormatThrowsExceptionForInvalidMode(): void
    {
        $result = new ParsedResult(
            success: true,
            command: 'install',
        );

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Unknown format mode: invalid');

        $this->formatter->format($result, 'invalid');
    }
}
