# RQuadling/Environment

[![Build Status](https://img.shields.io/travis/rquadling/environment.svg?style=for-the-badge&logo=travis)](https://travis-ci.org/rquadling/environment)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/rquadling/environment.svg?style=for-the-badge&logo=scrutinizer)](https://scrutinizer-ci.com/g/rquadling/environment/)
[![GitHub issues](https://img.shields.io/github/issues/rquadling/environment.svg?style=for-the-badge&logo=github)](https://github.com/rquadling/environment/issues)

[![PHP Version](https://img.shields.io/packagist/php-v/rquadling/environment.svg?style=for-the-badge)](https://github.com/rquadling/environment)
[![Stable Version](https://img.shields.io/packagist/v/rquadling/environment.svg?style=for-the-badge&label=Latest)](https://packagist.org/packages/rquadling/environment)

[![Total Downloads](https://img.shields.io/packagist/dt/rquadling/environment.svg?style=for-the-badge&label=Total+downloads)](https://packagist.org/packages/rquadling/environment)
[![Monthly Downloads](https://img.shields.io/packagist/dm/rquadling/environment.svg?style=for-the-badge&label=Monthly+downloads)](https://packagist.org/packages/rquadling/environment)
[![Daily Downloads](https://img.shields.io/packagist/dd/rquadling/environment.svg?style=for-the-badge&label=Daily+downloads)](https://packagist.org/packages/rquadling/environment)

An environment loader and validator used by RQuadling's various projects.

## Installation

Using Composer:

```sh
composer require rquadling/environment
```

## .env Validation

If you have a `.env.example` file in the root of your project, you can enforce validation of this file against the 
developer's `.env` file by adding `"\\RQuadling\\Environment\\Validation::postAutoloadDump"` to your
`"post-autoload-dump"` in `"scripts"` in your project's `composer.json`.

For example:
```json
  "scripts": {
    "post-autoload-dump": "\\RQuadling\\Environment\\Validation::postAutoloadDump",
  }
```
