# 🏋️ SimpleMacroCounter - The app that tracks your diet for you

## About SMC

SMC is an application that allows users to register the foods and quantities they have eaten during the day. This helps users track their daily calorie intake, making it easier to achieve their goals.

## Key concept

SMC focuses on simplifying macro tracking by automatically calculating daily intake from food entries and manual adjustments.

## Tech stack

- Symfony: Backend framework (REST APIs, services, business logic).
- Doctrine + MySQL: Database layer and ORM.
- Twig + JavaScript + CSS: Server-rendered UI with dynamic interactions.

## Planned tech stack

Symfony • Doctrine • React

## Features

- Database management with Doctrine.
- User registration and authentication.
- Register new dishes with their respective macro-nutrients.
- Register food intakes, allowing users to automatically track daily calories and macro-nutrients.
- Manually register macro-nutrient intakes, allowing users to add or subtract macro-nutrients.
- View daily intake history.
- Configure daily macro-nutrient goals.

## Architecture

SMC follows a layered architecture:
- Controllers
- Services
- DTOs
- Repositories
- Entities
- Frontend modules

## Roadmap
1. User registration and login.\
 *Finished*
2. Functionality to add one or more foods.\
 *Finished*
3. Configurable macro-nutrient goals.\
 *Finished*
4. Manually register macro-nutrient intakes.\
 *Finished*
5. Intake history.\
 *Finished*
6. Foods database.\
 *Finished*
7. Register new foods.\
 *Finished*
8. Add foods from the database.\
 *Finished*
9. Unit, integration and end-to-end testing.
10. Advanced user management.
11. Leveling system.
12. Friend system.
13. Challenges system.
14. User roles implementation.
15. Administration panel.
16. REST API implementation.

## Versions

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

## Project Tree

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
│   └── HomePageTemplate.twig
│   └── RegisterPageTemplate.twig
└── README.md
└── RELEASE_NOTES.md
```
