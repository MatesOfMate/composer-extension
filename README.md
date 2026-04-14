# Composer Extension for Symfony AI Mate

Composer dependency management tools for AI assistants, with compact encoded output tailored for token-efficient workflows.

## Features

- install, require, remove, update, and explain Composer operations
- `composer://config` resource for project dependency context
- core Mate encoded output for compact responses
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
- Symfony AI Mate 0.7+ required
- Composer available locally, or `matesofmate_composer.custom_command` configured

## Available Tools

- `composer-install`
- `composer-remove`
- `composer-require`
- `composer-update`
- `composer-explain`

All tools return encoded strings through Mate's core `ResponseEncoder`. Install the suggested `helgesverre/toon` package if you want TOON responses; otherwise the same payload falls back to JSON.

## Available Resource

- `composer://config`

This resource exposes `composer.json` content as an encoded structured payload.

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
