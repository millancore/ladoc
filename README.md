
<h1 align="center" style="border:none !important">
    <code>laravel documentation for Console</code>
</h1>

<p align="center">
    <img src="https://raw.githubusercontent.com/millancore/lo/main/art/example.png" alt="Ladoc example" height="408">
</p>

------

**Ladoc** ladoc allows you to search and browse Laravel documentation in all its versions.

## Installation

```bash
git clone https://github.com/millancore/ladoc.git
```
## Usage

> **Tip:** To make it easier to use, create an alias pointing to `repo-dir/bin/ladoc`, I usually use `zz` but it can be any alias.

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
to simplify the navigation you can filter the list by initial letter 

```bash
ladoc -lv
```
Result:
```
 Main List | filter: V

• [85] Validation (validation)
• [87] Views (views)
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

Ladoc allows you to search all versions of Laravel, just use `--brach` or `-b` to define the version you want to use.

```bash
ladoc -b5.2 blade
```
> If no version is set, use the latest one.

---

Ladoc is an open-sourced software licensed under the **[MIT license](https://opensource.org/licenses/MIT)**.



