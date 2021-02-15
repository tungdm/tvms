# TV Management System

[![Build Status](https://img.shields.io/travis/cakephp/app/master.svg?style=flat-square)](https://travis-ci.org/cakephp/app)
[![License](https://img.shields.io/packagist/l/cakephp/app.svg?style=flat-square)](https://packagist.org/packages/cakephp/app)

A system is used to manage its workers.

## Installation

1. Download [Composer](https://getcomposer.org/doc/00-intro.md) or update `composer self-update`.

If Composer is installed globally, run

```bash
composer install
```

2. Install Tiny But Strong Opentbs library
Link: http://www.tinybutstrong.com/opentbs.php?doc

## Serving

1. Start Nginx

```bash
sudo nginx
```

2. Stop Nginx if need

```bash
sudo nginx -s stop
```

## Configuration

Read and edit `config/app.php` and setup the `'Datasources'` and any other
configuration relevant for your application.

## Layout

The app skeleton uses a subset of [Foundation](http://foundation.zurb.com/) (v5) CSS
framework by default. You can, however, replace it with any other library or
custom styles.
