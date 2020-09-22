
# NGT\Monitor(ing)

## Documentation

This repository provides a tool to run checks on a given website or a bunch of websites.

ATTENTION:
lib/techie is not public usable! You need to aquire a license for that.


## Installation

``` bash
git clone git@github.com:nmngt/Monitor.git moni
cd moni && chmod +x console
composer update
```

## Usage

This command will run all checks from config on all configured sites.

```bash
./console check:config
```

This command will run default checks on an given site.

```bash
./console check:site fbo.de
```

This command will run ssl checks on all configured sites.

```bash
./console check:ssl
```

If you add `--report` to any of the commands, a email report is sent to the configured email address.

```bash
./console check:config --report
```


## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email norm@ngeorg.com or use the issue tracker.

## -----

## Used components

- symfony/console (https://symfony.com/doc/current/components/console.html)
- symfony/var_dumper (https://symfony.com/doc/current/components/var_dumper.html)
- symfony/debug (https://symfony.com/doc/current/components/debug.html)
- https://github.com/filp/whoops
- https://github.com/shuchkin/simplemail
