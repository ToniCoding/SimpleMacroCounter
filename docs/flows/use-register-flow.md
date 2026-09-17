# User Registration Flow Documentation - SimpleMacroCounter (SMC)

## 1. Overview & Objectives

This document outlines the user registration and onboarding lifecycle in **SimpleMacroCounter (SMC)**, detailing the complete process from initial account data submission, payload validation, secure password hashing, database persistence, and post-registration session or token initialization across web and API channels.

---

## 2. Registration Architecture

SMC handles user onboarding through a robust backend architecture leveraging Symfony components:

* **Payload Validation:** Incoming registration data is mapped to strongly-typed Data Transfer Objects (DTOs) or form types, ensuring data integrity before touching persistence layers.
* **Security & Hashing:** Passwords are processed securely using Symfony's automatic password hashing framework (`password_hashers`) to prevent plain-text exposure.
* **Exception & Constraint Management:** Database duplicate checks (such as unique email constraints) are safely intercepted and translated into user-friendly feedback via the global exception handling layer.

---

## 3. Registration Flow Phasing

### Phase 1: Request Submission & Payload Mapping

* **Web Registration:** Users submit account details (e.g., username, email, password) via a dedicated registration form processed through Symfony Form Types and controllers.
* **API Registration:** Payloads are received as JSON, mapped to DTO structures using the Symfony Serializer or request mapping attributes, and filtered for validation.

### Phase 2: Data Validation & Constraints

* The application validates incoming parameters against strict constraints (e.g., valid email format, minimum password length, and unique identifier checks).
* If validation fails, standard error responses or form re-renders are dispatched with specific validation messages.

### Phase 3: Password Hashing & Entity Persistence

* Validated data initializes a new `App\Entity\User` instance.
* The raw password is securely hashed via Symfony's password hasher service before the entity is persisted to the database.
* Database-level constraints (such as unique email or username violations) are caught by repository operations or global exception listeners (`UniqueConstraintViolationException`), returning clean error feedback.

### Phase 4: Post-Registration Response & Onboarding

* Upon successful persistence, the system handles post-registration actions:
* **Web Flows:** Automatically establishes a stateful session or redirects the user to the login view with a success flash message.
* **API Flows:** Returns a confirmation payload or immediately provisions an initial authentication context/JWT token.



---

## 4. Error Handling & HTTP Status Codes

* **`201 Created` / `200 OK**`: Successful user account creation and persistence.
* **`302 Found`**: Redirection triggered upon successful web registration (e.g., redirecting to `/login` or `/home`).
* **`400 Bad Request`**: Triggered by malformed JSON payloads, missing fields, or validation constraint violations.
* **`422 Unprocessable Entity`**: Returned when registration data fails business rules or uniqueness checks (e.g., email already registered).
* **`500 Internal Server Error`**: Fallback status for unexpected database write exceptions or persistence failures.

---

## 5. Security & Configuration Rules

* **Password Hashing:** Configured in `config/packages/security.yaml` using Symfony's `auto` hasher algorithm for secure entity storage in `App\Entity\User`.
* **Access Control:** Public routing rules permit unauthenticated access exclusively to registration and login endpoints, safeguarding all internal profile and macro-tracking routes (`ROLE_USER` / `IS_AUTHENTICATED_FULLY`).

---

## 6. Request Lifecycle Summary

1. The client submits registration credentials via the web form (`POST /register`) or API endpoint.
2. The controller maps the payload into the registration DTO/Form and runs validation constraints.
3. The user entity is instantiated, the password is securely hashed, and Doctrine persists the record to the database.
4. `GlobalExceptionListener` catches any unique constraint violations (e.g., duplicate email) to prevent crash traces and supply friendly error notices.
5. The system dispatches a successful response—redirecting web users to login or returning API confirmation details for subsequent authentication.
