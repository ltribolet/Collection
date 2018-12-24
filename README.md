# Collection

This aim to be a little library as experiment to play with Collection and add as little as possible or no dependencies.

# Command helper

* `cs-fix`: Fix the code following the standards described in [.php_cs.dist](),
* `analyze`: Run PHPStan at highest level

# Docker

1. Build the image, replace `<name>` by any prefix of your choice:
```
docker build -t <name>/tools build/tools
```

2. Run any command
For Windows, replace `$PWD` by the full path:
```
docker run --rm --interactive --tty --volume $PWD:/app <name>/tools composer install
```

# XDebug

By default XDebug is on, you can override any of the directives in `.env` file created automatically after `composer install`
and run the image with the following option:

```
docker run --env-file .env --rm --interactive --tty --volume $PWD:/app <name>/tools composer install
``` 
