# CLAUDE.md

Guidance for working on the Composer extension.

## Overview

This package provides Composer dependency management tools for Symfony Mate. It uses Mate's core response encoder for tool payloads.

## Current Mate Workflow

- initialize projects with `vendor/bin/mate init`
- current Mate setups auto-discover extensions after install and update
- `vendor/bin/mate discover` refreshes discovery and agent instruction artifacts
- use `vendor/bin/mate debug:extensions` and `vendor/bin/mate debug:capabilities` for troubleshooting
- run tools from the CLI with `vendor/bin/mate tools:call <tool> --<param>=<value>`

## Structure

- `src/Capability/` for Mate tools and resources
- `src/Runner/` for Composer process execution
- `src/Parser/` for command parsing
- `src/Formatter/` for encoded tool output
- `config/config.php` for service registration
- `skills/` for the Agent Skills declared through `extra.ai-mate.skills`

## Service Registration

Capabilities are registered in `config/config.php`, not `config/services.php`.

## Output Strategy

- Tools return encoded strings through the formatter layer and Mate's core `ResponseEncoder`.
- The `composer://config` resource also returns encoded text.
- Document TOON as optional runtime behavior with JSON fallback.

## Commands

```bash
composer install
composer test
composer lint
composer fix
vendor/bin/mate debug:capabilities
vendor/bin/mate tools:list --extension=matesofmate/composer-extension
```

## Standards

- no `declare(strict_types=1)` by project convention
- non-final classes by project convention
- JSON examples use `\JSON_THROW_ON_ERROR | \JSON_PRETTY_PRINT`
- docs must match actual tool names, config keys, and file layout
