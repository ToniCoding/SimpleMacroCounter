# User Login Flow Documentation

## 1. Overview & Objectives

This document outlines the authentication lifecycle in SMC, detailing the entire process from the initial user request to session creation, JWT generation, and full authentication across both the stateful web application and the stateless REST API.

## 2. Authentication Architecture

SMC employs a hybrid security architecture combining two complementary mechanisms managed through Symfony's security framework:

* **Web Access (Stateful):** Handled via standard sessions initialized upon successful form login. It is protected by CSRF tokens and managed by the `main` firewall.
* **API Access (Stateless):** Secured via JSON Web Tokens (JWT). Tokens are generated upon authentication and are mandatory for consuming protected `/api` endpoints behind the dedicated `api` firewall.

This hybrid approach bridges traditional server-rendered web applications with modern decoupled API consumption, balancing session security with fast, stateless data exchanges.

## 3. Login Flow Phasing

### Phase 1: Request & Form Validation
The client submits credentials via the login interface (`POST /login`). Symfony captures the request and maps it to the `LoggedUserDTO` through form validation constraints (`LoginUserType`).

### Phase 2: Credential Verification
The `UserHandler` verifies the submitted credentials against the database entity (`App\Entity\User`). Passwords are verified securely using Symfony's automatic hashing configuration (`password_hashers`).

### Phase 3: Session & Token Generation
Upon successful verification, the `UserAuthenticatorInterface` authenticates the user into the stateful session (`main` firewall). Simultaneously, the `AccessTokenHandler` provisions a JWT token context containing user metadata and expiration metrics.

### Phase 4: Response Dispatch & Token Storage
The backend completes the authentication sequence—either dispatching a session redirect to the home view or returning a JSON response containing the generated token and expiration details for client-side storage and subsequent API requests.

## 4. Error Handling & HTTP Status Codes

* **`200 OK`**: Successful authentication, returning session redirection or JSON payload with the JWT token.
* **`302 Found`**: Standard web redirection upon login state changes.
* **`401 Unauthorized`**: Returned when credentials are invalid, missing, or when an expired/invalid JWT is presented to protected API routes.

## 5. Security & Rate Limiting

The security layer is configured via `config/packages/security.yaml` with the following enforcement rules:

* **Password Hashing:** Uses Symfony's `auto` hasher algorithm for secure entity storage (`App\Entity\User`).
* **Firewall Isolation:**
  * `generate_jwt`: Stateless endpoint (`/api/v1/generate-jwt`) using JSON login handling.
  * `api`: Stateless firewall protecting all routes prefixed with `/api` using JWT authentication.
  * `main`: Stateful firewall protecting general routes (`/`) utilizing form login with CSRF protection explicitly enabled (`enable_csrf: true`).
* **Access Control Rules:**
  * Public access is granted to `/api/v1/generate-jwt` and login/register routes.
  * All other `/api` routes require full authentication (`IS_AUTHENTICATED_FULLY`).
  * Root and general application paths require `ROLE_USER`.

## 6. Sequence Diagram

1. The user loads the login page (`GET /login`) and submits credentials via the form (`POST /login`).
2. Symfony Security intercepts the request, and the `UserController` delegates credential validation to the `UserHandler`.
3. If credentials match the database entity, the user is authenticated, and a stateful session is established via `UserAuthenticatorInterface`.
4. The `AccessTokenHandler` generates the corresponding access token linked to the user badge.
5. The client receives the response, authenticating session access while client-side scripts capture and store the JWT for future API endpoint consumption.
