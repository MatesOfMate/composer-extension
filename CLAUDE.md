# CLAUDE.md

Guidance for working on the Composer extension.

## Overview

This package provides Composer dependency management tools for Symfony AI Mate. It is intentionally TOON-first and uses package-specific formatters rather than the optional encoder pattern proposed upstream in `symfony/ai` PR `#1439`.

## Current Mate Workflow

- initialize projects with `vendor/bin/mate init`
- current Mate setups auto-discover extensions after install and update
- `vendor/bin/mate discover` refreshes discovery and agent instruction artifacts
- use `vendor/bin/mate debug:extensions` and `vendor/bin/mate debug:capabilities` for troubleshooting
- use `./bin/codex` for Codex sessions

## Structure

- `src/Capability/` for MCP tools and resources
- `src/Runner/` for Composer process execution
- `src/Parser/` for command parsing
- `src/Formatter/` for TOON output
- `config/config.php` for service registration

## Service Registration

Capabilities are registered in `config/config.php`, not `config/services.php`.

## Output Strategy

- Tools return TOON-formatted strings through `ToonFormatter`.
- The `composer://config` resource also returns TOON-oriented content.
- When documenting this package, be explicit that the upstream optional TOON pattern exists, but this package currently keeps TOON as a required runtime dependency and a deliberate product choice.

## Commands

```bash
composer install
composer test
composer lint
composer fix
vendor/bin/mate debug:capabilities
vendor/bin/mate mcp:tools:list --extension=matesofmate/composer-extension
```

## Standards

- no `declare(strict_types=1)` by project convention
- non-final classes by project convention
- JSON examples use `\JSON_THROW_ON_ERROR | \JSON_PRETTY_PRINT`
- docs must match actual tool names, config keys, and file layout
