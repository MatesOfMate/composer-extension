# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a **Composer extension for symfony/ai-mate** that provides token-optimized dependency management tools for AI assistants.

**Key Features:**
- TOON (Token-Oriented Object Notation) format for 40-50% token reduction
- Composer command execution via Symfony Process component
- JSON output parsing for structured results
- Auto-detection of composer.json configuration

**Core Components:**
- **Tools** (`src/Capability/`): MCP tools for Composer operations
- **Runner** (`src/Runner/`): Composer process execution
- **Parser** (`src/Parser/`): JSON and command output parsing
- **Formatter** (`src/Formatter/`): TOON output formatting using helgesverre/toon
- **Config** (`src/Config/`): Composer.json detection and parsing

**Available Tools:**
- `composer-install` - Install dependencies from composer.json/composer.lock
- `composer-require` - Add new package requirements
- `composer-update` - Update packages to latest versions
- `composer-why` - Show dependency chains for a package
- `composer-why-not` - Show why a version cannot be installed

**Available Resources:**
- `composer://config` - Provides composer.json content

## Response Format

All tools return **TOON-formatted strings** for maximum token efficiency:

```
command: install
status: SUCCESS
package_count: 25
```

## Formatter Modes

Tools support multiple output modes via the `mode` parameter:

- **`default`**: Standard output (status + packages/dependencies + errors/warnings)
- **`summary`**: Just counts and status (ultra-compact)
- **`detailed`**: Full information with metadata

## Essential Commands

### Development Workflow
```bash
# Install dependencies
composer install

# Run all tests
composer test

# Check code quality (validates composer.json, runs Rector, PHP CS Fixer, PHPStan)
composer lint

# Auto-fix code style and apply automated refactorings
composer fix
```

### Individual Quality Tools
```bash
# PHP CS Fixer (code style)
vendor/bin/php-cs-fixer fix --dry-run --diff
vendor/bin/php-cs-fixer fix

# PHPStan (static analysis at level 8)
vendor/bin/phpstan analyse

# Rector (automated refactoring to PHP 8.2)
vendor/bin/rector process --dry-run
vendor/bin/rector process

# PHPUnit (run specific test)
vendor/bin/phpunit tests/Unit/Capability/InstallToolTest.php
vendor/bin/phpunit --filter testMethodName
```

## Architecture

### Core Concepts

**Tools vs Resources:**
- **Tools** (`#[McpTool]`): Executable actions invoked by AI (install, require, update, why, why-not)
- **Resources** (`#[McpResource]`): Static/semi-static data provided to AI (config)

**Discovery Mechanism:**
The `extra.ai-mate` section in `composer.json` defines:
- `scan-dirs`: Directories to scan for `#[McpTool]` and `#[McpResource]` attributes
- `includes`: Service configuration files to load

### Directory Structure

```
src/
├── Capability/          # MCP tools and resources
│   ├── InstallTool.php
│   ├── RequireTool.php
│   ├── UpdateTool.php
│   ├── WhyTool.php
│   ├── WhyNotTool.php
│   └── ConfigResource.php
├── Runner/              # Process execution
│   ├── ComposerRunner.php
│   └── RunResult.php
├── Parser/              # Output parsing
│   ├── OutputParser.php
│   └── ParsedResult.php
├── Formatter/           # TOON formatting
│   └── ToonFormatter.php
└── Config/              # Configuration detection
    └── ConfigurationDetector.php
```

### Discovery Mechanism

Symfony AI Mate auto-discovers tools and resources via `composer.json`:

```json
{
    "extra": {
        "ai-mate": {
            "scan-dirs": ["src/Capability"],
            "includes": ["config/services.php"]
        }
    }
}
```

### Service Registration Pattern

In `config/services.php`:
```php
$services = $container->services()
    ->defaults()
    ->autowire()
    ->autoconfigure();

$services->set(YourTool::class);
```

### Tool Implementation Pattern

```php
use Mcp\Capability\Attribute\McpTool;

class YourTool
{
    public function __construct(
        private readonly ComposerRunner $runner,
        private readonly OutputParser $parser,
        private readonly ToonFormatter $formatter,
    ) {
    }

    #[McpTool(
        name: 'composer-action-name',
        description: 'Precise description of when AI should use this tool'
    )]
    public function execute(string $param, string $mode = 'default'): string
    {
        $runResult = $this->runner->run(['action', $param]);
        $parsedResult = $this->parser->parseCommandOutput($runResult, 'action');
        return $this->formatter->format($parsedResult, $mode);
    }
}
```

### Resource Implementation Pattern

```php
use Mcp\Capability\Attribute\McpResource;

class ConfigResource
{
    #[McpResource(
        uri: 'composer://config',
        name: 'composer_config',
        mimeType: 'application/json'
    )]
    public function getConfig(): array
    {
        return [
            'uri' => 'composer://config',
            'mimeType' => 'application/json',
            'text' => json_encode($data, \JSON_THROW_ON_ERROR | \JSON_PRETTY_PRINT),
        ];
    }
}
```

**Key points:**
- Must return array with `uri`, `mimeType`, and `text` keys
- URI uses custom scheme (`composer://`)
- Typically return `application/json` or `text/plain`

## Code Quality Standards

### Important Design Decisions

- **No strict types declarations** - All PHP files omit `declare(strict_types=1)` by convention
- **No final classes** - All classes are non-final to allow extensibility
- **JSON error handling** - Always use `\JSON_THROW_ON_ERROR | \JSON_PRETTY_PRINT` with `json_encode()`

### PHPStan Configuration
- **Level 8** (maximum strictness)
- Analyzes both `src/` and `tests/`

### File Header Template

All PHP files must include:
```php
<?php

/*
 * This file is part of the MatesOfMate Organisation.
 *
 * (c) Johannes Wachter <johannes@sulu.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
```

### DocBlock Annotations

**@author annotation**: Required on all class-level DocBlocks:
```php
/**
 * Description of the class.
 *
 * @author Johannes Wachter <johannes@sulu.io>
 */
class YourClass
```

**@internal annotation**: Mark implementation details not for external use:
```php
/**
 * Internal helper class.
 *
 * @internal
 * @author Johannes Wachter <johannes@sulu.io>
 */
class InternalHelper
```

Use @internal for:
- Runner, Parser, Formatter classes
- Internal DTOs like RunResult, ParsedResult
- Classes not intended for extension consumers

## Testing Conventions

- Tests live in `tests/Unit/` mirroring `src/` structure
- Extend `PHPUnit\Framework\TestCase`
- Use descriptive test method names
- Mock dependencies for unit tests
- Test output format and structure

## CI/CD

GitHub Actions workflow runs automatically:
- **Lint job**: Validates composer.json, runs Rector, PHP CS Fixer, PHPStan
- **Test job**: Runs PHPUnit on PHP 8.2 and 8.3

## Commit Message Convention

Keep commit messages clean without AI attribution.

**Format:**
```
Short summary (50 chars or less)

- Conceptual change description
- Another concept or improvement
```

**Rules:**
- ❌ NO AI attribution
- ✅ Short, descriptive summary line
- ✅ Bullet list describing concepts/improvements
- ✅ Focus on the WHY and WHAT
