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
