# Composer Extension for Symfony AI Mate

Composer dependency management tools for AI assistants, with TOON-optimized output tailored for token-efficient workflows.

## Features

- install, require, update, why, and why-not Composer operations
- `composer://config` resource for project dependency context
- TOON-formatted output for compact responses
- custom command support for Docker or wrapper-based setups

## Installation

```bash
composer require --dev matesofmate/composer-extension
vendor/bin/mate init
```

In current AI Mate setups, extension discovery is handled automatically after Composer install and update. Use `vendor/bin/mate discover` when you want to refresh discovery artifacts such as `mate/AGENT_INSTRUCTIONS.md`.

Use these commands when troubleshooting:

```bash
vendor/bin/mate debug:extensions
vendor/bin/mate debug:capabilities
vendor/bin/mate mcp:tools:list --extension=matesofmate/composer-extension
```

For Codex, use the generated wrapper:

```bash
./bin/codex
```

## Custom Command Configuration

If Composer must run through Docker or another wrapper, configure `matesofmate_composer.custom_command`.

```php
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container): void {
    $container->parameters()->set('matesofmate_composer.custom_command', [
        'docker', 'compose', 'exec', 'php', 'composer',
    ]);
};
```

## Requirements

- PHP 8.2+
- Symfony AI Mate 0.6+ recommended
- Composer available locally, or `matesofmate_composer.custom_command` configured

## Available Tools

- `composer-install`
- `composer-require`
- `composer-update`
- `composer-why`
- `composer-why-not`

All tools return TOON-formatted strings in this package. That is an intentional MatesOfMate product choice. Upstream `symfony/ai` is moving toward optional TOON with JSON fallback in PR `#1439`, but this package currently remains explicitly TOON-first.

## Available Resource

- `composer://config`

This resource exposes `composer.json` content in token-efficient TOON format.

## Development

```bash
composer install
composer test
composer lint
composer fix
```

## Contributing

Contributions are welcome. See [CONTRIBUTING.md](../../CONTRIBUTING.md).

## License

MIT
