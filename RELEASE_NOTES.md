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
│   └── dummy
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

## Project Versions

### v0.6.0 (*Unreleased*) - SMC's Way

**NVM (Next Version Mandatory)**
- Migrate all logic at Controller layer to Service layer.
- Implementation of security voters to avoid repetitive security control over the endpoints.

**Changes**
- Implement property promotion on all DTOs (WIP).

**Fixed bugs**
- [SMC-B#001] Users must be over 15 or under 100 years old in registration.
- [SMC-B#002] It is no longer possible to add or reduce more than 400 of any macro-nutrient in one intake.

### v0.5.0 (04/02/2026) - The Symfony of SMC

**Release description**
In this release, SMC receives Symfony, Doctrine and some added functionality. From now on, SMC is *Symfony* based. Using Doctrine a ORM is also implemented in the application.

Features added:
- History system: You can now check the registered intakes from the last 7 days. For now the last days is only configurable via parameter adding to the URL: *?lastDays=N* where N is the number of days.
- Modifying the intake: Macros intake is now modifiable via their respective form accesible from the menu.
- Calorie calculation: Now you can check your calories at home page and the reached % of your goal.
- Responsiveness: SMC is now adapted to mobile devices.

Infrastructure:
- Added frameworks like Symfony and Doctrine (ORM).
- Security.

**Features added**
- **[CRITICAL]** Database interaction is now implemented via Doctrine.
- **[CRITICAL]** User session is now implemented via Symfony Security.
- **[CRITICAL]** Configured Symfony Security firewalls to avoid unauthorized accesses.
- **[CRITICAL]** Added history system.
- **[CRITICAL]** Added functionality to add and reduce macro-nutrient intake.
- **[CRITICAL]** Added Symfony Forms and dynamic pages via Twig.


**New endpoints**\
`/`\
`/login`\
`/register`\
`/modifymacros`\
`/reducemacros`\
`/history`

**Changed**
- Deleted unused code or code that was replaced by Symfony or Doctrince frameworks.
- Users are now identified by Symfony Security access token.

**Infrastructure**
- **[CRITICAL]** Symfony implemented.
- **[CRITICAL]** Symfony security implemented.
- **[CRITICAL]** Doctrine implemented.
- **[CRITICAL]** Composer implemented.

**Known issues and improvements**
- **[Issue]**[SMC-B#003] SMC does not work with user localtime but with UTC.
- **[Issue]**[SMC-B#001] User can register with any age.
- **[Issue]**[SMC-B#002] User can register unlimited macro-nutrient (e.g. 30000g of protein).
- **[Improvement]**[SMC-I#001] Combine `modifyMacros` and `reduceMacros` endpoints into one using URL parameters.

### v0.4.0 (06-10-2025) - SMC general enhancement
**Features added**
- Added custom exceptions directory.
- Added custom exception `ExceededMacroLimitException` that will be thrown if the user tries to add more than 500 for any macro-nutrient.
- **[CRITICAL]** Implemented namespaces.
- Added new excepcion `NoRecordFoundException`.

**Changed**
- Cleaner path router.
- New users now have general macro-nutrient goals setted by default.
- Improved the logging system and added `WARN` and `ERROR` type messages.
- Implemented more logging across *controllers* to keep better trace.
- Length exception is now thrown if the user input is longer than 3 characters for the add macro-nutrient functionality.
- Exceeded macro exception is now thrown if the user input is higher than 400 for any macro-nutrient.
- Changed the condition for the exception in change macro-nutrient goal, before user could input 4 number long numbers.
- Code refactorization that included:
  - Replace unnecesary `"` with `'`.
  - Authenticatation tokens were moved in some cases.
- Improved the action handling in `ModGoalsPage`;
- Renamed `imag` directory to `img`.
- Included favicon in all templates.
- Logger now records the file the logged message comes from.
- A `NoRecordFoundException` is thrown if there are no records in database.

**Fixed issues**
- Fixed an issue that came from the refactor phase. Logger was writing `\n` explicitly.

**To do**
- Streaks user flow.

### v0.3.0 (27-09-2025) - Routing and data management
**Features Added**
- Implemented a PHP path router.
- Added new Apache configurations via `.htaccess`.
- Added dedicated public pages:
  - Registration form.
  - Login form.
  - 404 Not Found page.
- Added a separate PHP file to handle both registration and login form submissions.
- Added security to the registration form against resubmissions and CSRF attacks.
- Added new table `user_goals` to SMC database.
- Added database repositories for the following tables:
  - User metrics.  
  - User daily calorie intake.
  - User macro-nutrients goals.
- Added a favicon.
- Initialized user metrics and goals automatically upon registration.
- SMC is now able to process all the three macro-nutrients thanks to `CombinedMacros` integration.
- Implemented a new service container for macro management.
- Added integration for dynamic and efficent view for macros consumed and their goals.
- Added the option for the users to change their macro goals and consumed macros in the current day.
- Users can now see the calories they consumed in a day.

**Changed**  
- Apache now ignores all favicon requests to serve it as a static file.
- The table `kcals_daily` now have macronutrients columns added.
- Complete refactor for `MacrosCounter` to correctly represent a macro and its controller and view class.
- `Macro` class now works alogside with `CombinedMacros` to represent and manage all three macro-nutrients with a single controller.
- SMC now checks if the user has registered data in `kcals_daily` in the current date and if not, one registry is inserted.

**Fixed Issues**  
- Fixed duplicated database connections by caching the PDO instance within the same request.
- Fixed duplicate GET requests caused by browsers requesting a missing favicon.
- Fixed the issue where `auth_token` was not being cleared after logout.

**Next version**
- [UPGRADE] Combine register and login page into one.
- [UPGRADE] Avoid showing both change macronutrient goal and add amount, only show one.
- [UPGRADE] Write a function that allows to set the form cookies to avoid duplicated requests.
- [UPGRADE] Write a cleaner PHP router.
- [UPGRADE] Show a message when the user does not add amounts to any macronutrient.
- [UPGRADE] Auto-logging in if the authenticate token is setted instead of showing the login page.
- [BUG] Catch length exception in *"modGoalsForm > handleModGoalsData > More than 4 length input exception"*.
- [BUG] Throw and catch exceptin in *"modGoalsForm > handleModGoalsData > Add macronutrient amount"*.
- [BUG] Check and throw exception for empty amount in modify goals form.

### v0.2.0 (31-08-2025) - User registration and administration
**Features Added:**
- Implemented a service container.
- Implemented a bootstrap service.
- Implemented user registration and login system.
- Implemented user tokenization session.
- Improved code documentation.
- Improved app constants access and file.
- Replaced the plain text file `nextSteps.txt` with the better option `RELEASE_NOTES.md`.
- Deleted `nextSteps.txt`.

**Known issues:**
- Multiple database connections are being made. It is needed to change that to hold one lazy connection to it.

**Next version:**
- Fix the multiple connections to database.
- First user forms for register and login.
- More use of the logging module to control what's happening in the app.
- Implement repositories for the rest of the database tables.
- Consistent file and classes naming.
- Document all the code.
- Improve `README.md` and `RELEASE_NOTES.md` more.

### v0.1.0 (29-07-2025) - Initial release with basic macro tracking
**Features Added:**
- Generic macro is added.
- Macro calorie calculation.
- Construction of project structure.

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
