---
name: laravel-docs
description: Search and browse the official Laravel documentation (all versions) using the ladoc CLI. Use when working on Laravel code or answering questions about Laravel features, directives, APIs, or configuration to get accurate, version-specific documentation.
---

# Laravel Documentation (ladoc)

Use the `ladoc` CLI to look up official Laravel documentation instead of relying on memory.

## Commands

| Task | Command | Example |
|------|---------|---------|
| List all sections | `ladoc` | `ladoc` |
| Topic index of a section | `ladoc <section>` | `ladoc blade` |
| Search inside a section | `ladoc <section> <query>` | `ladoc blade @once` |
| Use a specific Laravel version | add `-b <version>` | `ladoc -b 10.x eloquent-relationships hasMany` |

## Workflow

1. If you don't know the section name, run `ladoc` and read the names in parentheses, e.g. `Blade Templates (blade)`.
2. Search with `ladoc <section> <query>`. The output is the full text of every matching article.
3. If there are no results, ladoc lists the sections that do contain the term and prints a ready-to-run `Try: ladoc ...` command — follow it.
4. If the section name is misspelled, ladoc answers `Did you mean: ...?` with the closest match.

## Notes

- Match the version to the project: check `composer.json` for the `laravel/framework` constraint and pass it with `-b` (e.g. `-b 11.x`). Without `-b`, the latest version is used.
- The first run for a version downloads and indexes the docs; it may take a few seconds.
- Output is plain text when piped — no ANSI codes to clean up.
