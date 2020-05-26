# Profile Blank

## Overview

This profile creates an empty Slim 4 project interactively or with default settings.

### File Structure

```
project-name
└───app
│   │   dependencies.php
│   │   routes.php
│   │   settings.php
│
└───logs
|   |   *empty*
|
└───public
|   │   .htaccess
|   │   index.php
|
└───src
|   |   *empty*
|
└───tests
|   │   bootstrap.php
|
│   .gitignore
│   composer.json
│   docker-compose.yml
│   phpunit.xml
```

### Dependencies

The user can select the following dependencies:

* PSR-7 HTTP message interface
    * Slim PSR-7 (Default)
    * Laminas
    * Guzzle
    * Nyholm
* Dependency Container
    * PHP DI (Default)
    * Pimple
    * Other (From user input)
* PSR-3 Logger
    * Monolog (Default)

## Authors

- [**Temuri Takalandze**](https://github.com/ABGEO)
