# WARP.md

This file provides guidance to WARP (warp.dev) when working with code in this repository.

## Project Overview

FluentPDO - PHP 8.1+ Edition is a modernized fork of the original FluentPDO project. It's a PHP SQL query builder library using PDO with a smart join builder that automatically creates table joins. This version (3.x) requires PHP 8.1+ and includes modern type declarations, strict typing, and enhanced features.

## Common Development Commands

### Testing
```bash
# Run all tests
phpunit --configuration phpunit.xml

# Install dependencies first if needed
composer install

# Set up test database (for local development)
mysql -u $DB_USER < tests/_resources/fluentdb.sql
```

### Development Setup
```bash
# Install dependencies
composer install

# For testing, you need a MySQL database named 'fluentdb'
# Check tests/_resources/init.php for database configuration
```

## Code Architecture

### Core Classes Structure
- `Query` - Main entry point, creates query objects (SELECT, INSERT, UPDATE, DELETE)
- `Structure` - Handles table relationships, primary/foreign key conventions
- `Base` - Abstract base class for all query types, handles execution and parameters
- `Common` - Shared functionality for SELECT/UPDATE/DELETE queries (WHERE, JOIN clauses)

### Query Types (src/Queries/)
- `Select` - SELECT query builder with smart joins, implements Countable
- `Insert` - INSERT query builder  
- `Update` - UPDATE query builder
- `Delete` - DELETE query builder
- `Json` - JSON query support

### Key Features
- **Smart Join Builder**: Automatically creates JOINs based on column references (e.g., `user.name` auto-joins user table)
- **Fluent Interface**: Method chaining for building queries
- **PDO Integration**: Built on top of PDO for database portability
- **Type Conversion**: Optional type conversion for read/write operations

### Database Conventions
- Primary keys default to 'id' 
- Foreign keys follow pattern '%s_id' (e.g., `user_id` for user table)
- These conventions can be customized via `Structure` class

### Usage Patterns
```php
// Basic setup
$fluent = new \Envms\FluentPDO\Query($pdo);

// Smart joins automatically created
$query = $fluent->from('comment')
    ->where('article.published_at > ?', $date)  // Auto-joins article table
    ->orderBy('published_at DESC');

// Shorthand methods for single-row operations
$user = $fluent->from('user', 1)->fetch();  // WHERE id = 1
$fluent->update('article', $data, 1)->execute();  // WHERE id = 1
```

## Testing Environment

Tests use MySQL with database 'fluentdb'. Test configuration:
- Travis CI: Uses root user with no password
- Local development: Uses 'vagrant'/'vagrant' credentials
- Database schema: `tests/_resources/fluentdb.sql`
- Test initialization: `tests/_resources/init.php`

## Development Notes

- **Modern PHP 8.1+**: This fork requires PHP 8.1+ with strict typing throughout
- **Type Safety**: All classes use `declare(strict_types=1)` and proper type declarations
- **Array Handling**: Arrays are automatically converted to JSON strings for database storage
- **Union Types**: Uses modern PHP union types where appropriate (e.g., `mixed`, `string|null`)
- **PSR-4 Autoloading**: Namespace remains `Envms\FluentPDO` for compatibility
- **Iterator Support**: All query builders implement `IteratorAggregate` for direct iteration
- **Debugging**: Debug mode available via `$fluent->debug` property (mixed type)
- **Error Handling**: Exception handling configurable via `exceptionOnError` property
- **Fetch Modes**: Supports both object and array fetch modes with enhanced type safety

## Fork Information

This is a modernized fork of [envms/fluentpdo](https://github.com/envms/fluentpdo) (inactive since 2021). Changes include:
- PHP 8.1+ compatibility with modern type system
- Fixed array parameter handling (JSON serialization)
- Enhanced IDE support with proper type hints
- Updated development dependencies and tools
