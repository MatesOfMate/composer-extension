CHANGELOG
=========

0.7.0
-----

 * Support symfony/ai-mate 0.13
 * Replace the `#[McpTool]` and `#[McpResource]` attributes with Mate's native `#[MateTool]` and `#[MateResource]`

0.6.0
-----

 * Support symfony/ai-mate 0.12

0.4.0
-----

 * Support symfony/ai-mate 0.11

0.3.0
-----

 * Support symfony/ai-mate 0.8 through 0.10
 * Fix invalid MCP resource name

0.2.0
-----

 * Simplify dependency explanation by replacing why and why-not with a single explain tool
 * Improve tool and parameter descriptions for generated schemas
 * Align extension documentation and instructions with the simplified tool inventory

0.1.0
-----

 * Add Composer command tools (install, require, remove, update, why, why-not)
 * Add config resource providing composer.json content
 * Add TOON formatted output for token efficiency
 * Add multiple output modes: default, summary, detailed
 * Add INSTRUCTIONS.md for AI agent guidance
