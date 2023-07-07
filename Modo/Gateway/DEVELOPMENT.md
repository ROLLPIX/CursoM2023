## Ejecutar tests 

Estos comandos deben ejecutarse desde la raiz de magento:

```sh
php ./vendor/phpunit/phpunit/phpunit -c dev/tests/unit/phpunit.xml.dist app/code/Modo/Gateway/
```

Ejecutar test con reporte de coverage:

```sh
export XDEBUG_MODE="coverage"
php ./vendor/phpunit/phpunit/phpunit -c dev/tests/unit/phpunit.xml.dist app/code/Modo/Gateway/ --coverage-html coverage
```

## Requisitos para tests

- PHP 7.3 ó 7.4
- composer
- Una instancia de Magento 2.3 ó 2.4

Instalar Composer:

```sh
$ cd
$ curl -sS https://getcomposer.org/installer -o composer-setup.php
# Install 'composer' command:
$ sudo php composer-setup.php --install-dir=/usr/local/bin --filename=composer
```
