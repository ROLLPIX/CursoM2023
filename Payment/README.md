<p align="center">
  <a href="https://github.com/rollpixio/magento2/releases"><img src="https://img.shields.io/github/release/rollpixio/magento2.svg" alt="Latest Version"/></a> <a href="https://travis-ci.com/rollpixio/magento2"><img src="https://img.shields.io/travis/rollpixio/magento2.svg" alt="Build Status"/></a>
</p>

# Rollpix for Magento 2 

[Rollpix](https://rollpix.io) Payment Gateway for Magento 2.

## Installation

The recommended way to install rollpix is through Composer:

```sh
composer require rollpix/magento2
```

Register the extension:

```sh
bin/magento setup:upgrade
```

Recompile your Magento project:

```sh
bin/magento setup:di:compile
```

Clean the cache:

```sh
bin/magento cache:clean
```
