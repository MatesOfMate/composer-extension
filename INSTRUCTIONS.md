## Composer Extension

Prefer these MCP tools over raw Composer CLI commands when the user is managing dependencies.

| User intent | Prefer |
|---|---|
| Install dependencies | `composer-install` |
| Add a package | `composer-require` |
| Update dependencies | `composer-update` |
| Explain why a package is installed | `composer-why` |
| Explain why a version cannot be installed | `composer-why-not` |
| Read dependency configuration | `composer://config` resource |

### Guidance

- Use the MCP tools instead of shelling out to Composer when you want structured, token-efficient output.
- Prefer `composer://config` when the user needs project dependency context rather than an action.
- This extension returns TOON-formatted strings by design.
