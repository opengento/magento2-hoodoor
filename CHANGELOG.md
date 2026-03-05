# Changelog

## [0.2.0]

### Added
- PHPDoc blocks on all previously undocumented methods across the module
- `#[\Override]` attributes on all overriding methods (PHP 8.3)
- Typed constants (`const string`) where applicable (PHP 8.3)
- Admin email template registration (`admin_login`) in `etc/email_templates.xml`
- Login URL is now built in PHP (`EmailProcessor`) and passed as `login_url` template variable

### Changed
- `RequestException` now extends `LocalizedException` instead of `\Exception`
- `AccountManagement` plugin throws `LocalizedException` instead of `\Exception` and uses `__()` instead of `_()`
- Admin emails use a dedicated `admin_login` template instead of the generic customer one
- Email templates use `{{var login_url}}` instead of `this.getUrl(...)` calls
- Removed unused catch variables (`$e`, `$exception`)

### Refactored
- Extracted shared `ProcessLogin` logic into `ProcessLoginTrait` — both frontend and admin controllers now use the trait and implement 3 abstract methods
- Extracted shared `RequestLogin` logic into `RequestLoginTrait` — both frontend and admin controllers now use the trait and implement 5 abstract methods
- Consolidated 3 identical `RemoveReAuthVerification` plugin classes into a single class using `Generic` type hint
- `PasswordVerification::remove()` signature changed from `AccountForm|UserMain|RoleInfo` to `Generic`

### Fixed
- PHP 8.4 compatibility: implicit nullable parameters now use explicit `?Type` syntax (`CreatePost`, `Model/Admin/User`)
- `AccountManagementTest` expectations updated to match `LocalizedException` change
