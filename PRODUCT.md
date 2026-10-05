# Product

<!-- impeccable:product-schema 1 -->

## Platform

web

## Users

The primary user is a hands-on owner-manager of a small or midsize hotel in Colombia or Latin America. They oversee daily operations and administration, including reservations, stays, guests, rooms, payments, cash, and housekeeping status.

Front-desk staff may also carry out operational workflows in the system, but the owner-manager's need for reliable control of the property guides product decisions.

## Product Purpose

Hellen Suite is a property management system that simplifies the operational and administrative work of running a hotel. Success means the property can manage its core daily workflows reliably from one system, including when internet connectivity is unavailable.

## Positioning

Hellen Suite is an offline-first hotel operations system for small and midsize properties. Its local desktop operation keeps core work available under unstable connectivity while the same product is also available through the web.

## Operating Context

The product is used throughout the hotel's daily operating cycle: configuring properties and rooms, creating and managing reservations, checking guests in and out, tracking occupied rooms and housekeeping status, managing folios and payments, and controlling cash shifts and movements.

Installations may manage multiple hotels. The current product stores operational data locally in SQLite during desktop use.

## Capabilities and Constraints

- Maintain both browser delivery and the NativePHP/Electron desktop application.
- Preserve offline capability for core hotel operations.
- Support Spanish and English interfaces and configurable ISO 4217 currency.
- Support hotel, room type, room, guest, reservation, stay, folio, payment, refund, cash, cash-shift, and housekeeping workflows already present in the application.
- Keep desktop application secrets out of distributed bundles and preserve NativePHP's authenticated internal communication.
- The product is under development and is not yet production-ready.
- Premium plans, pricing, legal integrations, cloud services, and other roadmap items in `CARACTERISTICAS_PREMIUM.md` are proposals, not confirmed shipped capabilities or commercial claims.

## Brand Commitments

- Product name: Hellen Suite.
- Initial market focus: small and midsize hotels in Colombia and Latin America.
- Product language support: Spanish and English.

## Evidence on Hand

- `readme.md` documents the product purpose, supported platforms, technical stack, development status, and desktop security constraints.
- `routes/web.php` and `resources/js/Pages/Hotels/` show the implemented hotel operations and workflows.
- `CARACTERISTICAS_PREMIUM.md` analyzes current capabilities and possible future monetization, but its pricing, plans, roadmap, and future features are not confirmed product claims.
- No confirmed testimonials, customer logos, usage metrics, or production benchmarks are present in the repository and future work must not fabricate them.

## Product Principles

1. Keep essential hotel operations dependable without an internet connection.
2. Reduce fragmented administrative work by connecting the property's daily workflows in one system.
3. Prioritize clarity and operational control for hands-on owner-managers.
4. Fit the terminology, languages, and practical needs of small and midsize hotels in Colombia and Latin America.
5. Protect hotel and guest data in both browser and distributed desktop environments.
