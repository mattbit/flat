# Flat

[![Build Status](https://travis-ci.org/mattbit/flat.svg?branch=master)](https://travis-ci.org/mattbit/flat)
[![Code Coverage](https://scrutinizer-ci.com/g/mattbit/flat/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/mattbit/flat/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/mattbit/flat/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/mattbit/flat/?branch=master)

Flat is a flat-file database with a mongo-like API, written in PHP.

⚠️ Still under heavy development!

### Inserting documents
```php
$db = Flat::localDatabase("/path/to/db");

$doc = new Document([
    'title' => 'Flat',
    'description' => 'A flat NoSQL database',
]);

$db->collection('pages')->insert($doc);
```

### Finding documents
```php
$db->collection('pages')->find(['published' => true]);

// Something slightly more complex
$db->collection('pages')->find([
    'published' => true,
    'view_count' => [ '$gt' => 1000 ],
    'author' => ['$in' => [ 'john', 'jane', 'jack']]
]);

// Or with boolean combination
$db->collection('pages')->find([
    '$or' => [
        ['published' => false],
        ['author' => 'admin']
    ]
]);
```

##### Supported query operators

**Logical**: `$and`, `$or`, `$not`.

**Comparison**: `$eq`, `$gt`, `$gte`, `$lt`, `$lte`, `$ne`, `$regex`.

**Array**: `$in`, `$nin`.

### Removing documents

```php
$db->collection('pages')->remove(['published' => false]);
```
