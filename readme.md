# Flat

A flat NoSQL database for PHP.

⚠️ Still under heavy development!

```php
$database = Flat::db('database_name');
$database->collection('pages')->find(['published' => true]);
```