# Flat

[![Build Status](https://travis-ci.org/mattbit/flat.svg?branch=master)](https://travis-ci.org/mattbit/flat)

A flat NoSQL database for PHP.

⚠️ Still under heavy development!

```php
$database = Flat::db('database_name');
$database->collection('pages')->find(['published' => true]);
```