# AGENTS.md

Guidelines for agents working on the Composer extension.

## Focus

Maintain a package-specific MCP extension for Composer workflows. Keep docs, examples, and tool behavior aligned with the actual package.

## Important Rules

- Register capabilities in `config/config.php`.
- Keep docs aligned with the current Mate workflow: `mate init`, automatic discovery, `mate discover` refreshes, and Codex wrappers.
- This package uses Mate's core `ResponseEncoder` for MCP-facing payloads.
- Describe TOON as optional runtime behavior provided by Mate, with JSON fallback.

## When Adding or Updating Tools

1. add or update the capability in `src/Capability/`
2. wire dependencies in `config/config.php`
3. update parser, runner, or formatter code as needed
4. add or update unit tests
5. update README and `INSTRUCTIONS.md` if behavior changed

## Quality Checks

Run:

```bash
composer test
composer lint
```

## Commit Messages

Never include AI attribution. Describe conceptual changes and user-facing outcomes.
