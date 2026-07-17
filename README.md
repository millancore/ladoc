# Laravel Documentation for `Console`

<p align="center">
    <img src="https://raw.githubusercontent.com/millancore/lo/main/art/example.png" alt="Ladoc example" height="408">
    <p align="center">
        <a href="https://github.com/millancore/ladoc/actions"><img alt="GitHub Workflow Status (master)" src="https://img.shields.io/github/actions/workflow/status/millancore/ladoc/test.yml"></a>
        <a href="https://packagist.org/packages/millancore/ladoc"><img alt="Total Downloads" src="https://img.shields.io/packagist/dt/millancore/ladoc"></a>
        <a href="https://packagist.org/packages/millancore/ladoc"><img alt="Latest Version" src="https://img.shields.io/packagist/v/millancore/ladoc"></a>
        <a href="https://packagist.org/packages/millancore/ladoc"><img alt="License" src="https://img.shields.io/packagist/l/millancore/ladoc"></a>
    </p>
</p>

------

**Ladoc** allows you to search and browse Laravel documentation in all its versions.

## Installation

### Using Composer
**Requires [PHP 8.2](https://php.net/releases/)**

```bash
composer global require "millancore/ladoc"
```

----

### or Using Docker
```bash
 docker run -td --name ladoc millancore/ladoc
```

Uses:
```bash
docker exec -it ladoc sh # (and then zz or ladoc)
```

## Usage

> **Tip:** To make it easier to use, create an alias, I usually use `zz`.

### Search

`ladoc <section> <query>`

```bash
ladoc blade @once
```
### List all sections

simply execute the command without parameters,  you will see a list of all the sections (in brackets).

```bash
ladoc
```
Result:
```
 Main List

• [0] Artisan Console (artisan)
• [1] Authentication (authentication)
• [2] Authorization (authorization)
• [3] Laravel Cashier (Stripe) (billing)
• [4] Blade Templates (blade)
... 
```
### Filter Main List
To simplify the navigation you can filter main list with '--letter' or `-l` and initial letter.

```bash
ladoc -lv
```
Result:
```
 Main List | filter: V

• [0] Validation (validation)
• [1] Views (views)
```

### Navigation System
You can navigate through all sections using the indexes in the list. 

```bash
ladoc 4
```
Result:
```
 Blade Templates

• [0] Introduction (+)
• [1] Displaying Data (+)
• [2] Blade Directives (+)
...
```
and continue in that way

```bash
ladoc 4 2
```
Result:
```
Blade Directives

In addition to template inheritance and displaying data...

────────────────────────
• [0] If Statements
• [1] Switch Statements
• [2] Loops
• [3] The Loop Variable
...
```
### Using the search with index

You can search directly in a section using its index. `ladoc 4 @once` it's equal to `ladoc blade @once`.

### Versions

Ladoc allows you to search all versions of Laravel, just use `--branch` or `-b` to define the version you want to use.

```bash
ladoc -b5.2 blade
```
> If no version is set, use the latest one.

## Use with AI tools

Ladoc can be used by AI coding agents (Claude Code, Cursor, etc.) to look up accurate,
version-specific Laravel documentation. There are two ways to connect it.

### MCP Server

Ladoc ships with an [MCP](https://modelcontextprotocol.io) server (`ladoc-mcp`, stdio transport)
that exposes three tools:

| Tool | Description |
|------|-------------|
| `list_sections` | List all sections of the documentation |
| `get_section` | Get the topic index of a section |
| `search_docs` | Search a section and return the matching articles |

All tools accept an optional `version` (e.g. `"12.x"`, defaults to the latest).

**Claude Code**

```bash
claude mcp add laravel-docs -- ladoc-mcp
```

**Cursor, Windsurf, and other MCP clients**

Add to your client's MCP configuration (e.g. `.cursor/mcp.json`):

```json
{
    "mcpServers": {
        "laravel-docs": {
            "command": "ladoc-mcp"
        }
    }
}
```

> `ladoc-mcp` is installed by `composer global require millancore/ladoc`. If your composer
> global bin directory is not in `PATH`, use the full path, e.g.
> `~/.config/composer/vendor/bin/ladoc-mcp`.

### Agent Skill (CLI)

Any agent that can run shell commands can use the `ladoc` CLI directly — output is plain
text (no ANSI codes) when piped. A ready-made skill for Claude Code is included in
[`skills/laravel-docs`](skills/laravel-docs/SKILL.md); install it with:

```bash
mkdir -p ~/.claude/skills
cp -r "$(composer global config home)/vendor/millancore/ladoc/skills/laravel-docs" ~/.claude/skills/
```

For other agents, add the equivalent to your instructions file (`AGENTS.md`, rules, etc.):

```
To look up Laravel documentation use the ladoc CLI:
- `ladoc` lists all sections (names in parentheses).
- `ladoc <section> <query>` searches a section, e.g. `ladoc blade @once`.
- Add `-b <version>` for a specific Laravel version, e.g. `ladoc -b 11.x eloquent-relationships hasMany`.
- On no results, ladoc suggests the sections that contain the term.
```

---

Ladoc is an open-sourced software licensed under the **[MIT license](https://opensource.org/licenses/MIT)**.



