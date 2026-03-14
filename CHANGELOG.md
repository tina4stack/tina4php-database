# Changelog

## [2.0.22] - 2026-03-14

### Fixed
- NoSQLParser: Fixed in_array to array_key_exists for operator matching (4 occurrences)
- NoSQLParser: Added DELETE statement support

### Added
- NoSQLParser test suite (8 tests)
- DataError test suite (3 tests)
- phpunit.xml and CI workflow


## [2.0.22] - 2026-03-14

### Added
- Added DELETE support to NoSQLParser
- Added test suite (NoSQLParser + DataError tests)

### Changed
- Removed 9 unnecessary file entries from autoload (kept constants.php only)
- Removed redundant classmap autoloading (PSR-4 only)
- Added PHP >= 8.1 requirement to composer.json

### Fixed
- Fixed NoSQLParser in_array bug (changed to array_key_exists for operator lookup)
