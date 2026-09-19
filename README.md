<p align="center">
  <img src="./public/img/smc_icon_text.png" />
</p>

<p align="center"> <i>Powered by</i>
    <br><br>
    <img src="https://img.shields.io/badge/Doctrine-%23326CE5?style=flat&logo=doctrine&logoColor=white"></img>&nbsp;&nbsp;&nbsp;
    <img src="https://img.shields.io/badge/symfony-%23000000.svg?style=flat&logo=symfony&logoColor=white"></img>&nbsp;&nbsp;&nbsp;
    <img src="https://img.shields.io/badge/MariaDB-003545?style=flat&logo=mariadb&logoColor=white"></img>
</p>

# 🏋️ Simple Macro Counter

SMC is the app that helps you track your daily calorie and macro-nutrient intake based on the goals you set.

## Table of Contents

- [Simple Macro Counter](#-simple-macro-counter)
- [Installation](#installation)
  - [Before starting](#before-starting)
  - [Dependencies](#dependencies)
    - [Modifying the dependencies](#modifying-the-dependencies)
  - [Environment variables](#environment-variables)
  - [Post dependencies installation](#post-dependencies-installation)
- [Use](#use)
- [Roadmap](#roadmap)
  - [Initiatives list](#initiatives-list)
    - [REST API](#rest-api)
    - [User roles & administration panel](#user-roles--administration-panel)
    - [Enhanced UX](#enhanced-ux)
    - [Friends system](#friends-system)
- [Versions](#versions)
- [Project information](#project-information)
  - [Project architecture](#project-architecture)
  - [Project tree](#project-tree)

## Installation

To self-host the web application you need to clone the project with:
```bash
git clone git@github.com:ToniCoding/SimpleMacroCounter.git
cd SimpleMacroCounter
```

### Before starting
Before installing packages and everything else, you'll need the following software previously installed:

1. PHP (if it's not obvious enough) - The core language.
2. Composer - Dependency manager.
3. Docker - The database is expected to be in a container and listen on port 3307 (to avoid overlapping port 3306 if you already have a database manager installed).

### Dependencies
The core dependencies for the web application to work can be installed by simply running the following command in the project root:

```bash
composer install
```

#### Modifying the dependencies
Running the previous command will install the dependencies listed in `composer.json`, but you can modify the versions whenever you want. Just keep in mind that the project is developed and tested with the `composer.lock` packages.

### Environment variables

You need to set several environment variables to make SMC work. The critical ones are:
- `APP_ENV`: Defines the application environment (dev, prod, test).
- `APP_DEBUG`: Enables or disables debug mode (1 for enabled, 0 for disabled).
- `APP_SECRET`: A private key used for cryptographic operations (e.g., CSRF, session).
- `MYSQL_USER`: The MySQL database username.
- `MYSQL_PASSWORD`: The MySQL database user password.
- `DATABASE_URL`: The full database connection DSN (e.g., mysql://user:pass@host:port/dbname).

### Post dependencies installation
After installing the dependencies, go to the project root and run the following command to start the embedded Symfony PHP server:

```bash
symfony serve
```

If you want to access the application through other devices on the same local network:

```bash
symfony serve --allow-all-ip
```

## Use

If you're running SMC, unless you changed the config, go to the following URL: `http://localhost:8000/`.

You'll be redirected to the login page where you have the option to register a new user. If you already have an account, log in with your credentials.

After logging in, you'll be redirected to the home page where you can see your daily calorie and macro-nutrient intake along with the goals you've set.

On the home page you'll have the following options:
- **Manual macros**: Allows you to manually add macro-nutrients. Calories are automatically calculated and added.
- **History**: Allows you to see previously registered days.
- **Register foods**: Register a new dish with nutritional information.
- **Register intake**: Register an intake automatically by selecting it from the catalog.
- **Settings**: Allows you to modify your goals. This section has an upcoming update.
- **Logout**: Exits the session.

## Roadmap

SMC has a defined roadmap that is followed **most** of the time. Sometimes an initiative gets prioritized because of the development of another one.

### Initiatives list

#### REST API

An implementation that allows frontend and backend decoupling by adding an intermediate layer between them. Previously, pages were served with data passed directly by Symfony to Twig.

By adding an API, future initiatives will be easier to develop, like the friends system, leveling, streaks, and more.

One of the biggest motivations for this API was the frontend and backend decoupling to make it easier to implement React.

This initiative includes JWT implementation.

##### Information about the initiative

- Planned version implementation: SMC_Ver_1.1.0
- Status: In development.

#### User roles & administration panel

Implement user roles to differentiate between normal and administrator users. This allows administrators to access the admin panel where they can see some reserved statistics.

##### Information about the initiative

- Planned version implementation: SMC_Ver_1.2.0
- Status: Planned.

#### Enhanced UX

Implement small enhancements. The planned enhancements are:

- If the user is logged in and tries to go to `/login`, redirect back to home.
- "Remember me" toggle button at login.
- "Forgot password" button at login.
- Change the action select in the manual macros form to radio buttons.

##### Information about the initiative

- Planned version implementation: SMC_Ver_1.2.1
- Status: Planned.

#### Friends system

Implement a friends system where users can add and delete friends. Adding them is possible through their email or alias.

##### Information about the initiative

- Planned version implementation: SMC_Ver_1.3.1
- Status: Planned.

## Versions

SMC has gone through several versions. Here's a comprehensive list of the **finished** versions:

Version 1.0.1 - SMC #1 HotFix
- Release date: 27/06/2026.
- Backend added/modied: Yes. See release notes.
- Frontend: Switched form PNG to SVG icons.

Version 1.0.0 - SMC
- Release date: 26/06/2026.
- Backend added/modified: Yes.
- Frontend: Remains the same.

Version 0.7.0 - The Final Marks
- Release date: 23/06/2026.
- Backend added/modified: Yes.
- Frontend: Remains the same.

Version 0.6.0 - The Dishe'd 
- Release date: 12/05/2026.
- Backend added/modified: Yes.
- Frontend: Twig, CSS and JavaScript.

Version 0.5.0 - SMC transition to frameworks.
- Release date: 04/02/2026.
- Backend added/modified: Yes.
- Frontend: Plain CSS.

Version 0.4.0 - SMC General Enhancement.
- Release date: 06-10-2025.
- Backend added/modified: Yes.
- Frontend: Implementation in progress.

Version 0.3.0 - Routing and data juggling
- Release date: 27-09-2025.
- Backend added/modified: Yes.
- Frontend: Implementation in progress.

Version 0.2.0 - The user experience overhaul
- Release date: 31-08-2025.
- Backend added/modified: Yes.
- Frontend: Not implemented.

Version 0.1.0 - The Core Foundation
- Release date: 29-07-2025.
- Backend added/modified: Yes.
- Frontend: Not implemented.

## Project information

### Project architecture
SMC follows a layered architecture:
- Controllers
- Services
- DTOs
- Repositories
- Entities
- Frontend modules

### Project tree

```text
SimpleMacroCounter/
├── bin/
├── config/
├── db/
├── docker/
├── migrations/
├── public/
│   └── css
│   └── img
│   └── js
│   └── ttf
│   └── index.php
├── src/
│   └── Config
│   └── Controller
│   └── DTO
│   └── Entity
│   └── Exceptions
│   └── Form
│   └── Handlers
│   └── Helpers
│   └── Logging
│   └── Model
│   └── Repository
│   └── Security
│   └── Service
│   └── Kernel.php
├── templates/
│   └── modifyData
│   └── security
│   └── HomePageTemplate.twig.html
│   └── RegisterPageTemplate.twig.html
└── README.md
└── RELEASE_NOTES.md
```

###### For further information, check the docs and release notes.
