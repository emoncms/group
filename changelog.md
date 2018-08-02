# Group module version changelog
Given a version number MAJOR.MINOR.PATCH:

- MAJOR version MUST be incremented if any backwards incompatible changes are introduced. It MAY include minor and patch level changes. 
- MINOR version MUST be incremented if new, backwards compatible functionality is introduced to the public API. It MUST be incremented if any public API functionality is marked as deprecated. It MAY be incremented if substantial new functionality or improvements are introduced within the private code. It MAY include patch level changes.
- PATCH version MUST be incremented if only backwards compatible bug fixes are introduced. A bug fix is defined as an internal change that fixes incorrect behaviour.


## Commits for Version 1.0.1 - We are in prerelease state
 - Issue 39: full remove of user using delete_user method and hook
 - Issue 48: added send email to the edit modal. Also added input for email subject and body (in edit modal but also in createuserandaddtogroup
