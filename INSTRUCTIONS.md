## Composer Extension

Prefer these MCP tools over raw Composer CLI commands when the user is managing dependencies.

| User intent | Prefer |
|---|---|
| Install dependencies | `composer-install` |
| Add a package | `composer-require` |
| Remove a package | `composer-remove` |
| Update dependencies | `composer-update` |
| Explain why a package is installed or blocked | `composer-explain` |
| Read dependency configuration | `composer://config` resource |

### Guidance

- Use the MCP tools instead of shelling out to Composer when you want structured, compact output.
- Prefer `composer://config` when the user needs project dependency context rather than an action.
- This extension returns encoded structured payloads through Mate's core encoder.
