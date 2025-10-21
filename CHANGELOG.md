# Changelog

All notable changes to this project will be documented in this file.

## [3.0.0] - 2025-01-21

### Added
- **PHP 8.1+ Support**: Complete modernization for PHP 8.1 and higher
- **Strict Type Declarations**: All files now use `declare(strict_types=1)`
- **Modern Type System**: Full use of PHP 8.1+ type declarations including:
  - Union types (e.g., `string|null`, `array|string|null`)
  - Mixed types for flexible parameters
  - Proper return type declarations
  - Typed properties throughout the codebase
- **Array Parameter Handling**: Automatic JSON serialization of array parameters
- **Enhanced IDE Support**: Better autocomplete and error detection with proper type hints
- **Updated Dependencies**: Modern development tools and PHPUnit 12.4+

### Changed
- **Breaking**: Minimum PHP version increased from 7.3 to 8.1
- **Package Name**: Changed from `envms/fluentpdo` to `vitexsoftware/fluentpdo`
- **Type Safety**: All method signatures updated with proper type declarations
- **Error Handling**: Improved error messages and debugging capabilities
- **Performance**: Better memory usage through strict typing

### Fixed
- **Array to String Conversion**: Fixed PHP warnings when arrays are passed as parameters
- **Type Compatibility**: Resolved all PHP 8.1+ compatibility issues
- **Method Signatures**: Updated all method signatures to match modern PHP standards

### Removed
- **PHP < 8.1 Support**: No longer supports PHP versions below 8.1

## About This Fork

This is a modernized fork of the original [envms/fluentpdo](https://github.com/envms/fluentpdo) project. The original project has been inactive since 2021, with the last commit being 3+ years old. This fork aims to:

- Provide PHP 8.1+ compatibility with modern features
- Maintain API compatibility with the original project
- Add enhanced type safety and developer experience improvements
- Keep the library up-to-date with current PHP best practices

## Migration from Original FluentPDO

If you're migrating from the original `envms/fluentpdo`, the API remains compatible, but you'll need:

1. PHP 8.1 or higher
2. Update your composer requirement to `vitexsoftware/fluentpdo`
3. Optionally add `declare(strict_types=1)` to your files for better performance

The core API and functionality remain unchanged, so your existing code should work without modifications.