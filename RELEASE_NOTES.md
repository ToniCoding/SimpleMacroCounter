# SimpleMacroCounter Release Notes

## Description

SimpleMacroCounter is a simple macro and calorie tracker designed to help users monitor their fitness progress.

Currently, SMC allows you to:
1. Track daily calorie and macronutrient (protein, carbs and fats).
2. Set and track daily macronutrient goals.
3. Add N quantities to each macronutrients and instantly view total calories.

## Project Tree

```
SimpleMacroCounter/
├── app/
│   └── auth
│   └── containers
│   └── controller
│   └── exceptions
│   └── handlers
│   └── helpers
│   └── invoker
│   └── logging
│   └── model
│   └── repository
│   └── view
├── config/
├── db/
├── public/
│   └── css
│   └── imag
│   └── pages
├── bootstrap.php
├── index.php
└── README.md
```

## Project Roadmap

- **v0.1.0**: Initial release with basic macro tracking
- **v0.2.0**: User registration and administration
- **v0.3.0**: Routing and data management
- **v0.4.0**: SMC general enhancement
- **v0.5.0**: *(Special update) The Symfony of SMC*

## Project Versions

### v0.5.0 (*Unreleased*)

**Features added**
- New way to interact with the database thanks to *Doctrine*.

**Infrastructure**
- Symfony framework implementation.
- Composer implementation.

### v0.4.0 (06-10-2025)
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

### v0.3.0 (27-09-2025)
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

### v0.2.0 (31-08-2025)
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

### v0.1.0 (29-07-2025)
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

## Future improvements
*Implement project structure validation.*\
*Implement JWT for user tokens.*\
*Implement Symfony.*\
*Improve and extensive use of logging system.*\
*Improve the exception throwing and managing.*
