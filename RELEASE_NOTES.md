# SimpleMacroCounter Release Notes

## Description

This are the release notes for the latest version of SMC where the new features, changes and bug fixing made within the release is described. For more information about the SMC web application and its purposes, check the README file.



## Project Roadmap

- **v0.1.0**: Initial release with basic macro tracking
- **v0.2.0**: User registration and administration
- **v0.3.0**: Routing and data management
- **v0.4.0**: SMC general enhancement
- **v0.5.0**: *(Special update) The Symfony of SMC*
- **v0.6.0**: The Dishe'd update

## Project Versions

### v0.6.0 (12-05-2026) - The Dishe'd update

**Release description**
The intention of this release is to make possible for users to register dishes and new foods that will be available through new endpoints. The only thing the user needs to do, is to access the `Register intake` page, select the desired dish, complete the grams consumed and clic on "Register intake". The intake will be added to the daily macro-nutrient intake.

**Features added**
- Users can now register foods and dishes.
- Users can now register intake based on pre-registered foods.
- Settings where the user can modify the goals.
- User is now able to change the history last days through the UI.
- Mobile adoption is finished.

**Changed**
- Macros now take into account decimals.
- Database changes relative to foods and macro-nutrient taking into account up to two decimals.

**Technical**
- Removed logic at controller layer down to the service layer.
- Removed magic accessors and implemented the correct way to get the entity managers.
- Implemented property promotion in all the application.

**NVM (Next Version Mandatory)**
- Implementation of security voters to avoid repetitive security control over the endpoints.
- Combine add and reduce macros in one page with in-form option selection to add or reduce macros.
- Remove all errors thrown in repositories to move them to the service layer.

**Fixed bugs**
- [SMC-NEF#000] Users must be over 15 or under 100 years old in registration.
- [SMC-NEF#000] It is no longer possible to add or reduce more than 400 of any macro-nutrient in one intake.
- [SMC-NEF#000] Users no longer can click fields into negative values in add and reduce forms.
- [SMC-NEF#017] Macro decimals are not taken into account.
- [SMC-NEF#018] Cannot reduce more macros than consumed thrown while reducing less macros than consumed.
- [SMC-NEF#019] On settings update, zeroes were not ignored.

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
