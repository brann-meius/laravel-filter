# Changelog

All significant changes to `laravel-filter` will be documented in this file.

## [2.0.1]

### Fixed
- Fixed operation when no cache file is present.
- Fixed exception handling in routes defined with closures.

### Enhancements
- Removed unused dependencies.
- Added support for Laravel 12.x.

## [2.0.0]

### Changed
- **Refactor**: Removed the `Filterable` trait. Filters are now applied via middleware to simplify usage and enhance flexibility.
- **Configuration Update**: Revised configuration to allow specifying route groups for filter application, enabling targeted filter usage.
- **Aliases and Prefix**: Added support for filter aliases and customizable filter prefixes to streamline query parameters.

### Migration Steps
To upgrade to version 2.0.0, please follow these steps:
1. **Remove the `Filterable` trait** from any controllers.
2. **Publish the new configuration file** if necessary, using the command:  
   ```bash  
   php artisan vendor:publish --tag=filter-config  
   ```
3. **Update the `config/filter.php`** file to define which route groups should apply filters.

## [1.1.0]

### Enhancements
- **Refactor**: Filters now require a namespace and class name, replacing previous anonymous class usage, for better code organization.
- **New Features**: Introduced class variables and methods to provide more control over filter applications.
- **Performance Improvements**: Optimized core classes for better processing efficiency.
- **Code Cleanup**: Removed unique functionalities, including various filter directories, to streamline structure.
- **Logging Overhaul**: Removed internal logs, replacing them with exceptions to improve debugging.
- **Documentation Update**: Added a `CONTRIBUTING.md` to guide contributors on project standards.

## [1.0.1]

### Fixed
- Resolved an issue affecting the logging mechanism, ensuring correct application of logging within the filter process.

## [1.0.0]

### Initial Release
- Introduced the first stable version of `laravel-filter`, including essential dynamic filtering capabilities for Eloquent queries.
