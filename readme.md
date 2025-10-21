# FluentPDO - PHP 8.1+ Edition

**A modernized fork of the original FluentPDO project, updated for PHP 8.1+ with modern type declarations and enhanced features.**

FluentPDO is a PHP SQL query builder using PDO. It's a quick and light library featuring a smart join builder, which automatically creates table joins for you.

## About This Fork

This is a modernized fork of the original [envms/fluentpdo](https://github.com/envms/fluentpdo) project. Since the original project has been inactive for 3+ years (last commit in 2021), this fork provides:

- **PHP 8.1+ compatibility** with modern type declarations
- **Strict typing** throughout the codebase
- **Union types** and **mixed types** where appropriate  
- **Typed properties** for better IDE support
- **Array parameter handling** with automatic JSON serialization
- **Enhanced error handling** and debugging capabilities
- **Updated dependencies** and development tools

## Features

- Easy interface for creating robust queries
- Supports any database compatible with PDO
- Ability to build complex SELECT, INSERT, UPDATE & DELETE queries with little code
- Type hinting for magic methods with code completion in smart IDEs

## PHP Version Requirements

**This fork requires PHP 8.1 or higher.**

### What's New in This PHP 8.1+ Edition

- **Modern Type System**: Full use of PHP 8.1+ type declarations including union types
- **Strict Types**: All files use `declare(strict_types=1)` for better type safety
- **Array Handling**: Automatic JSON serialization of array parameters to prevent SQL errors
- **Enhanced Performance**: Better memory usage and performance through strict typing
- **Developer Experience**: Improved IDE support with proper type hints and autocomplete
- **Future Ready**: Prepared for upcoming PHP versions

### Migrating from Original FluentPDO

If you're upgrading from the original FluentPDO (v2.x), this version maintains API compatibility while requiring PHP 8.1+. The main changes are internal improvements and type safety enhancements.

## Reference

[Sitepoint - Getting Started with FluentPDO](http://www.sitepoint.com/getting-started-fluentpdo/)

## Installation

### Composer

The preferred way to install this PHP 8.1+ edition of FluentPDO is via [composer](http://getcomposer.org/).

```bash
composer require vitexsoftware/fluentpdo
```

Or add the following line in your `composer.json` file:

```json
{
    "require": {
        "vitexsoftware/fluentpdo": "^3.0"
    }
}
```

Then run `composer update` and you're done!

### Requirements

- **PHP 8.1+** (required)
- **PDO extension** (required)
- Any PDO-compatible database (MySQL, PostgreSQL, SQLite, etc.)

## Getting Started

Create a new PDO instance, and pass the instance to FluentPDO:

```php
<?php
declare(strict_types=1);

use Envms\FluentPDO\Query;

$pdo = new PDO('mysql:dbname=fluentdb', 'user', 'password');
$fluent = new Query($pdo);
```

Then, creating queries is quick and easy:

```php
$query = $fluent->from('comment')
             ->where('article.published_at > ?', $date)
             ->orderBy('published_at DESC')
             ->limit(5);
```

which would build the query below:

```mysql
SELECT comment.*
FROM comment
LEFT JOIN article ON article.id = comment.article_id
WHERE article.published_at > ?
ORDER BY article.published_at DESC
LIMIT 5
```

To get data from the select, all we do is loop through the returned array:

```php
foreach ($query as $row) {
    echo "$row['title']\n";
}
```

## Using the Smart Join Builder

Let's start with a traditional join, below:

```php
$query = $fluent->from('article')
             ->leftJoin('user ON user.id = article.user_id')
             ->select('user.name');
```

That's pretty verbose, and not very smart. If your tables use proper primary and foreign key names, you can shorten the above to:

```php
$query = $fluent->from('article')
             ->leftJoin('user')
             ->select('user.name');
```

That's better, but not ideal. However, it would be even easier to **not write any joins**:

```php
$query = $fluent->from('article')
             ->select('user.name');
```

Awesome, right? FluentPDO is able to build the join for you, by you prepending the foreign table name to the requested column.

All three snippets above will create the exact same query:

```mysql
SELECT article.*, user.name 
FROM article 
LEFT JOIN user ON user.id = article.user_id
```

##### Close your connection

Finally, it's always a good idea to free resources as soon as they are done with their duties:
 
 ```php
$fluent->close();
```

## CRUD Query Examples

##### SELECT

```php
$query = $fluent->from('article')->where('id', 1)->fetch();
$query = $fluent->from('user', 1)->fetch(); // shorter version if selecting one row by primary key
```

##### INSERT

```php
$values = array('title' => 'article 1', 'content' => 'content 1');

$query = $fluent->insertInto('article')->values($values)->execute();
$query = $fluent->insertInto('article', $values)->execute(); // shorter version
```

##### UPDATE

```php
use Envms\FluentPDO\Literal;

$set = ['published_at' => new Literal('NOW()')];

$query = $fluent->update('article')->set($set)->where('id', 1)->execute();
$query = $fluent->update('article', $set, 1)->execute(); // shorter version if updating one row by primary key
```

##### DELETE

```php
$query = $fluent->deleteFrom('article')->where('id', 1)->execute();
$query = $fluent->deleteFrom('article', 1)->execute(); // shorter version if deleting one row by primary key
```

***Note**: INSERT, UPDATE and DELETE queries will only run after you call `->execute()`*

## Modern PHP 8.1+ Features

### Array Parameter Handling

This edition automatically handles array parameters by converting them to JSON strings:

```php
// Arrays are automatically converted to JSON
$data = [
    'name' => 'John Doe',
    'tags' => ['php', 'mysql', 'programming'],  // Automatically converts to JSON
    'metadata' => ['created' => '2025-01-01', 'active' => true]
];

$fluent->insertInto('users', $data)->execute();
```

### Strict Type Declarations

All classes use strict typing for better performance and error detection:

```php
<?php
declare(strict_types=1);

// Your code here...
```

### Enhanced IDE Support

With full type declarations, you get better autocomplete and error detection in modern IDEs.

## Credits

- **Original FluentPDO**: [envms/fluentpdo](https://github.com/envms/fluentpdo) by envms
- **PHP 8.1+ Modernization**: [VitexSoftware](https://vitexsoftware.com)

## License

Free for commercial and non-commercial use under the [Apache 2.0](http://www.apache.org/licenses/LICENSE-2.0.html) or [GPL 2.0](http://www.gnu.org/licenses/gpl-2.0.html) licenses.
