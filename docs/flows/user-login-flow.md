# User Login Flow Documentation - SimpleMacroCounter (SMC)

## 1. Overview & Objectives

This document outlines the authentication and request-handling lifecycle in **SimpleMacroCounter (SMC)**, detailing the process from credential submission to session creation, JWT generation, API rate limiting, and global exception management across both stateful web views and decoupled REST endpoints.

---

## 2. Authentication Architecture

SMC implements a hybrid security architecture using Symfony's security framework:

* **Web Access (Stateful):** Managed via standard sessions initialized through form submissions, protected by CSRF tokens and handled under the `main` firewall.
* **API Access (Stateless):** Secured via JSON Web Tokens (JWT). Tokens are validated across all protected `/api/v1/` routes governed by custom listeners, request payload mapping, and rate-limiting rules.

---

## 3. Authentication & Request Flow Phasing

### Phase 1: Request Submission & Payload Mapping

* **Web Requests:** Clients submit form credentials or data payloads handled through Symfony form types and controllers (e.g., `SettingsPageController`, `MacroUpdateController`, `FoodsPageController`).
* **API Requests:** Payloads are ingested via JSON, mapped to strongly-typed DTOs using Symfony's serializer or `#[MapRequestPayload]`, and validated via `ValidatorInterface`.

### Phase 2: Rate Limiting & Security Interception (`ApiRateLimiterListener`)

* Incoming requests prefixed with `/api/` are intercepted by `ApiRateLimiterListener`.
* The listener evaluates whether the requester is authenticated (using `TokenStorageInterface`) or anonymous:
* **Authenticated Users:** Evaluated against the user-specific rate limiter (`apiUserLimiter`).
* **Anonymous Users:** Evaluated against the IP-based rate limiter (`apiIpLimiter`).


* If thresholds are exceeded, a `429 Too Many Requests` JSON response is returned along with standard rate-limiting headers (`X-RateLimit-Limit`, `X-RateLimit-Remaining`, `Retry-After`).

### Phase 3: Credential Verification & Session/Token Generation

* Submitted credentials or tokens are verified against database user entities (`App\Entity\User`).
* Passwords leverage Symfony's secure hashing configurations (`password_hashers`).
* Successful authentication establishes the stateful session or provisions the appropriate access token context for subsequent API interactions.

### Phase 4: Global Exception & Error Handling (`GlobalExceptionListener`)

* Uncaught exceptions or security violations during the request lifecycle are caught by `GlobalExceptionListener` (priority `-10`).
* **Security Exceptions:** Authentication or access denied exceptions log the error, flash a session notice, and trigger a redirect to the login route.
* **Domain Exceptions:** Specific application exceptions (such as `FoodAlreadyRegistered` or intake limits) map to designated user feedback loops and redirect responses.
* **Unhandled Exceptions:** Logged with trace details while rendering a safe user-facing error response via `app_error`.

---

## 4. Error Handling & HTTP Status Codes

* **`200 OK`**: Successful request execution or token provisioning.
* **`302 Found`**: Redirection triggered by state changes, security requirements, or domain exception flows (e.g., redirecting back to `/foods` or `/home`).
* **`400 Bad Request`**: Triggered by invalid JSON payloads, serialization failures, or DTO validation violations.
* **`401 Unauthorized`**: Returned when credentials fail or when invalid/expired tokens are presented to protected API routes.
* **`429 Too Many Requests`**: Enforced by `ApiRateLimiterListener` when request thresholds are breached.
* **`500 Internal Server Error`**: Fallback status for unexpected processing errors during intake or persistence operations.

---

## 5. Security & Rate Limiting Configuration

* **Password Hashing:** Uses Symfony's secure hashing framework for `App\Entity\User` entities.
* **Firewall Isolation:**
* **API Firewall:** Enforces stateless rules and token verification across `/api/v1/` endpoints.
* **Main Firewall:** Protects stateful web views with form login and explicit CSRF protection.


* **Access Control:** Public access is restricted to login and public API registration endpoints; protected endpoints require explicit user roles (`ROLE_USER` / `IS_AUTHENTICATED_FULLY`).

---

## 6. Request Lifecycle Summary

1. The client sends a request (Web `GET`/`POST` or API `/api/v1/`).
2. `ApiRateLimiterListener` inspects `/api/` paths and enforces consumption limits.
3. Controllers (`FoodsPageController`, `MacroUpdateController`, `SettingsPageController`, etc.) process payloads via DTO mapping and delegate business logic to dedicated services (`FoodRegistry`, `MacroIntakeUpdater`, `DailyIntakeRecordService`).
4. Any thrown exceptions are caught and processed by `GlobalExceptionListener` to ensure uniform logging, session flashes, and safe HTTP redirects or JSON error responses.
