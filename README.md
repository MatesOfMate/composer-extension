# Composer Extension for Symfony AI Mate

[![PHP Version](https://img.shields.io/badge/php-%3E%3D8.2-8892BF.svg)](https://php.net/)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)

MCP extension providing Composer dependency management tools for AI assistants, with token-optimized TOON format output.

## Features

- **5 MCP Tools** for Composer operations (install, require, update, why, why-not)
- **1 MCP Resource** for accessing composer.json configuration
- **TOON Format** output for 40-50% token reduction
- **Multiple Output Modes** (default, summary, detailed)
- **Token-Optimized** responses designed for AI assistants

## Installation

```bash
composer require matesofmate/composer-extension
```

## Custom Command Configuration

If Composer must run through Docker or another wrapper command, configure `matesofmate_composer.custom_command`.

When set, the extension skips local binary lookup and runs the configured command from the project root.

```php
// config/packages/matesofmate.php
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container): void {
    $container->parameters()->set('matesofmate_composer.custom_command', [
        'docker', 'compose', 'exec', 'php', 'composer',
    ]);
};
```

## Requirements

- PHP 8.2 or higher
- Symfony AI Mate ^0.1 or ^0.2
- Composer available in system PATH, or `matesofmate_composer.custom_command` configured

## Available Tools

### `composer-install`

Install dependencies from composer.json and composer.lock.

**Parameters:**
- `preferDist` (bool): Download dist packages (default: true)
- `noDev` (bool): Skip dev dependencies (default: false)
- `optimizeAutoloader` (bool): Optimize autoloader (default: false)
- `mode` (string): Output format mode (default: 'default')

**Example:**
```
composer-install(noDev: true, mode: 'summary')
```

### `composer-require`

Add a new package requirement to composer.json.

**Parameters:**
- `package` (string): Package name (e.g., "symfony/console")
- `version` (string|null): Version constraint (e.g., "^6.4")
- `dev` (bool): Require as dev dependency (default: false)
- `mode` (string): Output format mode (default: 'default')

**Example:**
```
composer-require(package: 'symfony/console', version: '^6.4')
composer-require(package: 'phpunit/phpunit', dev: true)
```

### `composer-update`

Update dependencies to latest versions within constraints.

**Parameters:**
- `packages` (string|null): Specific packages to update (comma/space-separated, empty = all)
- `preferDist` (bool): Download dist packages (default: true)
- `withDependencies` (bool): Update dependencies too (default: true)
- `mode` (string): Output format mode (default: 'default')

**Example:**
```
composer-update()
composer-update(packages: 'symfony/console, symfony/process')
```

### `composer-why`

Show which packages depend on a specific package.

**Parameters:**
- `package` (string): Package name to investigate
- `mode` (string): Output format mode (default: 'default')

**Example:**
```
composer-why(package: 'psr/log')
```

### `composer-why-not`

Show why a specific package version cannot be installed.

**Parameters:**
- `package` (string): Package name to investigate
- `version` (string|null): Specific version to check
- `mode` (string): Output format mode (default: 'default')

**Example:**
```
composer-why-not(package: 'php', version: '7.4')
```

## Available Resources

### `composer://config`

Provides the content of composer.json file including dependencies, autoloading, and scripts configuration in token-optimized TOON format.

**MIME Type:** `text/plain`

## Output Modes

All tools support multiple output modes via the `mode` parameter:

- **`default`**: Standard output with key information (status, packages/dependencies, errors, warnings)
- **`summary`**: Ultra-compact output (just counts and status)
- **`detailed`**: Full information including metadata without truncation

## Response Format

All tools return **TOON-formatted strings** for maximum token efficiency:

```
command: install
status: SUCCESS
packages[2]{name,version}:
  symfony/console|6.4.0
  symfony/process|6.4.0
package_count: 2
```

## Development

```bash
# Install dependencies
composer install

# Run tests
composer test

# Check code quality
composer lint

# Auto-fix code style
composer fix
```

## License

MIT License - see [LICENSE](LICENSE) for details.

## Contributing

Contributions are welcome! Please see [CONTRIBUTING.md](../../CONTRIBUTING.md) for guidelines.

---

*"Because every Mate needs Mates"*
