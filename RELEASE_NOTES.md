# SimpleMacroCounter Release Notes

## Description

SimpleMacroCounter is a simple macro and calorie tracker designed to help users monitor their fitness progress.

Currently, SMC allows you to:
1. Track daily calorie and macronutrient (protein, carbs, fats and fiber).
2. Set (WIP) and track daily macronutrient goals.
3. Add N quantities to each macronutrients and instantly view total calories.

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

## Project Roadmap

- **v0.1.0**: Initial release with basic macro tracking
- **v0.2.0**: User registration and administration
- **v0.3.0**: Routing and data management
- **v0.4.0**: SMC general enhancement
- **v0.5.0**: *(Special update) The Symfony of SMC*
- **v0.6.0**: The Dishe'd update

## Project Versions

### v0.6.0 (*Unreleased*) - The Dishe'd update

**Release description**
The intention of this release is to make possible for users to register dishes and new foods that will be available through new endpoints. The only thing the user needs to do, is to access the `Register intake` page, select the desired dish, complete the grams consumed and clic on "Register intake". The intake will be added to the daily macro-nutrient intake.

**Features added**
- Users can now register foods and dishes.
- Users can now register intake based on pre-registered foods.

**NVM (Next Version Mandatory)**
- Migrate all logic at Controller layer to Service layer.
- Implementation of security voters to avoid repetitive security control over the endpoints.
- Combine add and reduce macros in one page with in-form option selection to add or reduce macros.
- Add the option to change the history days in history page.
- Implement property promotion on all DTOs (WIP).
- Remove all magic method accessors.
- Remove all errors thrown in repositories to move them to the service layer.

**Fixed bugs**
- [SMC-B#001] Users must be over 15 or under 100 years old in registration.
- [SMC-B#002] It is no longer possible to add or reduce more than 400 of any macro-nutrient in one intake.
- [SMC-B#003] Users no longer can click fields into negative values in add and reduce forms.

**Other**
- Created `releases` directory containing RTS (Ready To Ship) that act as a release manifesto. From now on, the release notes will only contain the latest update.

## Project milestones

### Generic macros and software foundation.
Generic macro administration and calorie calculation is now possible following the project MVC pattern.\
Reached on version 0.1.0.\
Reach date: Tuesday, 29 July 2025.

### User creation and administration overhaul.
The user can now register through UI and can be administrated at database level.\
Reached on version 0.3.0\
Reach date: Monday, 15 September 2025.

### User registration and login.
Any user can now register and login through dedicated UI.\
Reached on version 0.3.0\
Reach date: Monday, 15 September 2025.

### User connected macros.
Any user can know their daily macros and calorie intake.\
Reached on version 0.3.0\
Reach date: Sunday, 21 September 2025.

### Streaks
Any user can know their current creatine and protein streak. The streak breaks if the user don't take it for more than 2 days.\
Reached on version ---\
Reach date: Not reached.

### Symfony and Doctrine implementation
Implement a professional framework like Symfony and impelment the database management through Doctrine entities and utilities.\
Reached on version 0.5.0\
Reach date: Monday, 06 October 2025.

### Unit and integration testing
Implement a test suite that can test the functionality of SMC.\
Reached on version ---\
Reach date: Not reached.

## Future improvements
*Implement project structure validation.*\
*Implement JWT for user tokens.*\
*Improve and extensive use of logging system.*\
*Improve the exception throwing and managing.*
