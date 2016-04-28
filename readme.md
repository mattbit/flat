# Flat

[![Build Status](https://travis-ci.org/mattbit/flat.svg?branch=master)](https://travis-ci.org/mattbit/flat)
[![Code Coverage](https://scrutinizer-ci.com/g/mattbit/flat/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/mattbit/flat/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/mattbit/flat/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/mattbit/flat/?branch=master)

A flat NoSQL database for PHP.

⚠️ Still under heavy development!

```php
$database = Flat::db('database_name');
$database->collection('pages')->find(['published' => true]);
```