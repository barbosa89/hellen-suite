# Desktop backup and restore specification

**Status:** Proposed for review; not implemented.

## Purpose and scope

Let a desktop user manually export a complete local backup to one file and later restore it. The backup must include the active NativePHP SQLite database and application-owned files needed to use its records, including hotel images and payment support documents. Both operations must work offline. A successful restore replaces the current local dataset rather than merging records.

This first version excludes scheduled backups, cloud synchronization, selective restore, web deployments, and cross-database-engine support. It does not promise compatibility with arbitrary future application versions or recovery from a damaged backup.

## Current project context

- The application has separate web, NativePHP and test database contexts; the implementation must use the active NativePHP database rather than hard-code the web database path. Confirm the packaged application's actual paths at runtime.
- Payment support files are stored on the `public` disk under `payments`. Hotel images are written under `hotels` on the default disk, which defaults to `public` but is configurable. Resolve the actual configured disks and include every application-owned persistent file referenced by the database, rather than assuming a single hard-coded directory.
- The application includes sensitive guest and payment information. A backup is sensitive even when saved outside the application's storage.
- No integrated backup or restore workflow was found during the preliminary review. The availability of the SQLite backup mechanism, archive library and native dialogs in the packaged runtime must be verified before implementation decisions are finalized.

## User experience

Provide a desktop-only backup and restore entry point in application settings, using the existing authorization and localization conventions. Display clear progress, cancellation where safe, and actionable error messages in English and Spanish.

For export, the user chooses a destination with a native save dialog. If the dialog is cancelled, nothing changes. The application creates one backup file, reports success only after it is complete, and advises the user to store it securely and separately from the computer. Never silently overwrite an existing file: require explicit confirmation.

For restore, the user chooses a backup file with a native open dialog. Before making changes, show its creation date, application version and an explicit warning that current local data and attachments will be replaced. Require confirmation. Warn that credentials or encrypted values might not be portable to another installation unless their encryption keys are available. A cancelled or rejected restore must not alter current data. On success, restart or otherwise safely reinitialize the desktop application before allowing further use.

## Backup format and creation

Use a versioned archive containing a manifest, a consistent SQLite snapshot, and attachment files. The manifest records the format version, creation time in UTC, application version, database schema/migration information, and a list of included files with sizes and cryptographic checksums. Archive paths are relative logical paths, not absolute host paths. Exclude logs, caches, temporary files, environment files, keys and unrelated machine data. Include no secrets in the manifest.

Before snapshotting, acquire an application-wide exclusive maintenance lock and drain or block all application writes to the database and included files, including background jobs and other desktop processes. Hold the lock until the database snapshot and attachment staging are complete. If exclusive access cannot be established, fail without creating a claimed-success backup. This is necessary because a SQLite snapshot alone does not make the separate attachment files consistent with it.

Create the database snapshot with a SQLite-aware mechanism, preferably `VACUUM INTO` if supported by the packaged SQLite runtime; evaluate SQLite's Online Backup API as an alternative. Do not copy a live database file alone, especially in WAL mode. Verify the snapshot using SQLite integrity checks and verify all staged files against the manifest. An unreadable, missing or changing required attachment must fail the operation rather than produce a silently incomplete backup.

Build the archive in a private temporary location, then write it to a temporary file alongside the chosen destination and publish it only after verification. Handle insufficient space, permissions, interruption and an unavailable destination without replacing a pre-existing valid backup with a partial file. Remove temporary data on failure or cancellation where possible; report cleanup failures without leaking file contents.

## Restore and recovery

Treat every selected archive as untrusted. Stage it in a private location without changing the live dataset. Validate the archive structure, supported format and application/schema compatibility, manifest, checksums and SQLite integrity before asking for final confirmation. Reject absolute paths, traversal, duplicate or unexpected entries, links, special files, excessive file counts and decompressed sizes; enforce documented implementation limits and available disk-space checks. Do not extract archive entries directly into live locations.

Use an exclusive maintenance lock that blocks new requests, jobs and other application instances; close or drain existing work and database connections before replacing data. Stage the complete replacement database and all attachment trees on their respective destination filesystems. Retain a recoverable copy of the current database and attachment trees until the replacement is confirmed healthy. Replace the complete dataset, including removal of files absent from the backup; do not leave obsolete attachments mixed into the restored dataset.

Database and attachment replacement cannot be assumed atomic across multiple paths. Use a durable restore journal with explicit phases and a recovery procedure on next startup. A crash or failure at any phase must prevent normal application use until recovery has either completed the replacement or restored the previous complete dataset. Never report success if the application may contain a mixture of old and restored data. Verify the restored database and attachments, reopen connections, and only then discard the recovery copy and journal. Surface errors without exposing guest information or secret values in logs.

SQLite journal/WAL files and open connections must be handled through the selected SQLite-aware shutdown and replacement procedure; never pair a restored database with stale WAL files. Validate the procedure against the actual packaged runtime and Windows filesystem semantics.

## Security and compatibility

The backup is unencrypted in this version: explicitly warn users that anyone with access to it can read sensitive information. Do not include the application's encryption key or `.env` in the archive. Before declaring cross-installation restore supported, inventory encrypted database fields and key usage, and define and test a secure key recovery or re-entry workflow. If encrypted data cannot be recovered on the target installation, block that restore or clearly identify affected functionality before confirmation; never claim a fully functional restore without verification.

Support backups from the same application/schema version first. Reject newer or incompatible formats and schemas with a clear message; expand compatibility only after defining and testing migrations for restored data. All file selection and paths must be scoped to the desktop environment, and operations must require the same authorization level as other sensitive settings.

## Acceptance criteria and verification

1. A user can export a single backup and restore it offline on the same supported desktop installation; records, hotel images and payment documents match the snapshot, including files added, changed or removed after export.
2. A cancelled operation, invalid archive, unsupported version, missing attachment, insufficient space or permission error never reports success or silently damages the existing dataset or an existing backup.
3. Concurrent database and attachment writes are blocked or drained during snapshot and replacement; another application instance cannot bypass the maintenance lock.
4. Interrupted restore is tested at every journal phase. On restart, recovery yields either the complete previous dataset or the complete restored dataset, never a mixture.
5. Tests reject malformed archives, path traversal, links, duplicate entries, checksum mismatches and oversized payloads. SQLite integrity and compatibility failures are covered.
6. Automated tests cover the services, authorization, error handling and both locales. Desktop integration tests on the packaged Windows x64 build exercise real dialogs, file permissions, SQLite snapshot and restore, startup recovery, and attachment paths. Other desktop platforms require equivalent validation before release.
7. Verify the behavior of encrypted fields and installation-specific keys during same-installation restore; do not advertise cross-installation restore until its key workflow is implemented and tested.

## References

- [SQLite Online Backup API and `VACUUM INTO`](https://www.sqlite.org/backup.html)
- [SQLite write-ahead logging](https://www.sqlite.org/wal.html)
- [NativePHP desktop databases](https://nativephp.com/docs/desktop/2/digging-deeper/databases)
- [NativePHP desktop dialogs](https://nativephp.com/docs/desktop/2/the-basics/dialogs)
