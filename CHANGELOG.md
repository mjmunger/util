# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]
- Planning an upgrade to support php 8.x.
## [1.2.1]
### Changed
- Updated Readme
- Added docblocks for PeopleTime
- Updated Changelog.

## [1.2.0]
### Added
- PeopleTime
- 
## [1.0.0] - 2022-07-13
### Added
- IniUploadManager - A class for reading the php.ini file and determining what the max_upload_size and max_post_sizes are. It can read raw integer values or the human readable shortcuts as defined [here](https://www.php.net/manual/en/ini.core.php#ini.post-max-size)
- ByteValueInterpreter - A class for translating human readable values from the ini file (like '8M') and converting it to the integer value.
- CHANGELOG.md

### Changed
- RandomGenerator::bearerToken was marked `@codeCoverageIgnore` because the function has untestable items. (`random_bytes()`);
- Updated README.md
- 
### Removed
- `JsonErrorDecoder` was removed in favor of [json_last_error_msg](https://www.php.net/manual/en/function.json-last-error-msg.php), which was probably always the solution.
- `TableLog` was removed in favor of [Climate](https://climate.thephpleague.com/), which already has [table output](https://climate.thephpleague.com/terminal-objects/table/).
- `WonderQueryBuilder` was removed in favor of building true search with keywords and indexing.
- `dbUnit` was removed because it is not longer supported by phpUnit.

