<a name="introduction"></a>
## Introduction

Artisan is the command line interface included with Laravel. Artisan exists at the root of your application as the `artisan` script and provides a number of helpful commands that can assist you while you build your application. To view a list of all available Artisan commands, you may use the `list` command:

```shell
php artisan list
```

Every command also includes a "help" screen which displays and describes the command's available arguments and options. To view a help screen, precede the name of the command with `help`:

```shell
php artisan help migrate
```

<a name="laravel-sail"></a>
#### Laravel Sail

If you are using [Laravel Sail](/docs/{{version}}/sail) as your local development environment, remember to use the `sail` command line to invoke Artisan commands. Sail will execute your Artisan commands within your application's Docker containers:

```shell
./vendor/bin/sail artisan list
```