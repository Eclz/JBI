# JBI University System Gap Analysis

Reviewed on 24 August 2026 against the current `main` branch and production deployment.

## Critical

1. **Production email cannot deliver.** The configured Hostinger SMTP identity uses the non-resolving `propertiopms.com` domain. Hostinger rejects that envelope sender, and it also rejects `info@jbiuniversity.com` because the authenticated mailbox does not own it. Configure a real `@jbiuniversity.com` mailbox and keep a queue worker running for queued admission mail.
2. **Production dependency/runtime mismatch.** The web runtime reports PHP 8.2, while the current `composer.lock` contains Symfony 8.1 packages requiring PHP 8.4.1 or newer. The existing vendor tree still runs, but a clean production `composer install` cannot succeed until either the web runtime is upgraded or dependencies are constrained for PHP 8.2.
3. **Production was maintained as a dirty Git working tree.** This prevents safe pull-based deployments and makes rollback difficult. Move to immutable releases or commit server changes upstream, then deploy artifacts while preserving only `.env` and writable storage.

## High

1. **The original realistic `DatabaseSeeder` is broken on a fresh database.** `FacultyFactory` writes a `website` column that the `faculties` table does not define. The new `DemoDatabaseSeeder` avoids this path and is validated independently, but the original full-volume seeder still needs repair before it can be trusted.
2. **Several views reference route names that do not exist.** Verified examples include `student.evoting.announcements`, `student.evoting.positions`, `student.evoting.candidacy.apply`, `admin.faculty.store`, and the `admin.schools.*` route group. These pages can throw route-generation exceptions when rendered.
3. **Automated coverage is minimal.** The suite contains only two example assertions. Authentication, admissions, fees, enrollment, grades, e-voting, permissions, email, and demo reset behavior lack regression tests.
4. **Support requests are not persisted or delivered.** `SupportController` only writes requests to the application log while telling users the request was submitted. A ticket table or a working support mailbox is required.

## Medium

1. **Mail delivery mixes synchronous and queued sending.** Queued admissions mail depends on a persistent queue worker; there is no production supervisor configuration in the repository.
2. **Brand/contact data remains duplicated.** Although public-facing stale values were corrected, institution details are still hard-coded across templates and seeders. Move them behind one configuration/service source.
3. **External avatar generation is a runtime dependency.** Several screens call `ui-avatars.com`; profile displays degrade when that third-party service is unavailable or blocked.
4. **Demo credentials are intentionally public.** `password123` is acceptable only while the portal is explicitly operating as a demo. The reset feature is therefore guarded by `DEMO_RESET_ALLOWED` and should never be enabled on a real-data environment.

## Recommended order

1. Provision and verify official SMTP credentials.
2. Align production PHP with the lock file and establish repeatable releases.
3. Repair missing routes and add feature tests for each role.
4. Implement persistent support tickets.
5. Centralize institution branding/contact settings.
