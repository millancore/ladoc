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

> [!TIP]
> Unlike Laravel Boost, Ladoc supports versions prior to 10.x — all the way back to 4.x —
> making it perfect for maintaining older Laravel projects.
>
> Ladoc itself needs PHP 8.2 to run, but that's independent of your project: you can browse
> Laravel 4.x docs while maintaining an app stuck on an old PHP version. If your machine
> only has an older PHP, use the [Docker image](#or-using-docker) instead.

## Index

- [Use with AI tools](#use-with-ai-tools)
  - [MCP Server](#mcp-server)
  - [Agent Skill (CLI)](#agent-skill-cli)
- [Installation](#installation)
- [Usage](#usage)
  - [Search](#search)
  - [List all sections](#list-all-sections)
  - [Filter Main List](#filter-main-list)
  - [Navigation System](#navigation-system)
  - [Using the search with index](#using-the-search-with-index)
  - [Versions](#versions)
- [Working with legacy Laravel versions](#working-with-legacy-laravel-versions)

## Use with AI tools

Ladoc can be used by AI coding agents (Claude Code, Cursor, etc.) to look up accurate,
version-specific Laravel documentation. There are two ways to connect it.

> Both require ladoc to be installed first — see [Installation](#installation).

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
# current project, only for you (default)
claude mcp add laravel-docs -- ladoc-mcp

# all your projects
claude mcp add --scope user laravel-docs -- ladoc-mcp

# project scope: shared with your team via a .mcp.json committed to the repo
claude mcp add --scope project laravel-docs -- ladoc-mcp
```

**Cursor, Windsurf, and other MCP clients**

For project scope, add a `.mcp.json` to the project root (Claude Code reads this file,
and it is what `--scope project` generates):

```json
{
    "mcpServers": {
        "laravel-docs": {
            "command": "ladoc-mcp"
        }
    }
}
```

The same block works in each client's own configuration file, e.g. `.cursor/mcp.json`
(project scope) or `~/.cursor/mcp.json` (global) for Cursor.

> `ladoc-mcp` is installed by `composer global require millancore/ladoc`. If your composer
> global bin directory is not in `PATH`, use the full path, e.g.
> `~/.config/composer/vendor/bin/ladoc-mcp`.

**Using Docker**

The Docker image includes `ladoc-mcp`, so you don't need PHP on the host. Keep a named
container running (the downloaded doc indexes are reused between sessions):

```bash
docker run -td --name ladoc millancore/ladoc
```

Then point your MCP client at it:

```bash
claude mcp add laravel-docs -- docker exec -i ladoc ladoc-mcp
```

```json
{
    "mcpServers": {
        "laravel-docs": {
            "command": "docker",
            "args": ["exec", "-i", "ladoc", "ladoc-mcp"]
        }
    }
}
```

> Use `-i`, never `-t`: MCP communicates over stdin/stdout and a TTY corrupts the stream.
> A throwaway container also works (`docker run --rm -i millancore/ladoc ladoc-mcp` as the
> command), but it re-downloads the docs on the first search of each session.

### Agent Skill (CLI)

Any agent that can run shell commands can use the `ladoc` CLI directly — output is plain
text (no ANSI codes) when piped. A ready-made skill for Claude Code is included in
[`skills/laravel-docs`](skills/laravel-docs/SKILL.md); install it with:

```bash
mkdir -p ~/.claude/skills
cp -r "$(composer global config home)/vendor/millancore/ladoc/skills/laravel-docs" ~/.claude/skills/
```

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

## Working with legacy Laravel versions

Ladoc covers every Laravel version back to 4.0, so you can maintain old projects with the
documentation that actually matches them.

**1. Find the project's Laravel version**

Check `laravel/framework` in the project's `composer.json`:

```json
"require": {
    "laravel/framework": "4.2.*"
}
```

**2. Pass it to ladoc with `-b`**

Versions 6 and later use the `Nx` branch format; versions 4 and 5 use the exact minor:

| Laravel | `-b` value | Example |
|---------|-----------|---------|
| 6.0 and later | `6.x` … `13.x` | `ladoc -b8.x eloquent scopes` |
| 5.0 – 5.8 | `5.0` … `5.8` | `ladoc -b5.2 blade` |
| 4.0 – 4.2 | `4.0`, `4.1`, `4.2` | `ladoc -b4.2 templates` |

The first search on a version downloads and indexes its docs; after that it's instant.
Each version keeps its own index, so you can switch between projects freely.

> [!NOTE]
> Old versions organize the docs differently — for example, Laravel 4.x has `templates`
> instead of `blade`. Run `ladoc -b4.2` (no query) to list the sections that exist in
> that version, and if you search a section that doesn't exist, ladoc suggests the
> closest match.

**3. With AI tools**

Agents using the [MCP server](#mcp-server) pass the version as the `version` argument of
each tool; with the [skill](#agent-skill-cli), they read the constraint from
`composer.json` and add `-b` automatically. Either way, the agent gets answers for the
version your project actually runs.

Ladoc itself requires PHP 8.2, no matter which Laravel version you're consulting — if the
legacy machine can't provide it, use the [Docker image](#or-using-docker).

---

Ladoc is an open-sourced software licensed under the **[MIT license](https://opensource.org/licenses/MIT)**.
