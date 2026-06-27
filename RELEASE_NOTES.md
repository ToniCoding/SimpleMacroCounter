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
- **v0.6.1**: PHP Unite'd
- **v0.7.0**: The Final Marks
- **v1.0.0**: *(Special update) SMC*
    - **v1.0.1**: SMC #1 HotFix

## Current Project Version

**Release description**
First SMC hotfixing fixing some critical bugs observed after deployment to production environment, only bug fixing.

**Fixed**
- Added missing parameter for maximum days shown in history.
- Added new SVG icons replacing previous ones.
- Calorie calculator is now a class.
- Global handler now processes the previous unhandled exception for already registrated products.
- Product registering is now working with `Products` table instead of deprecated `Foods`.
- After goal settings change, the app now redirects to the home instead of staying in the settings page.

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
Reached on version 0.6.1\
Reach date: Sunday, 30 May 2026.

## Future improvements
*Implement project structure validation.*\
*Implement JWT for user tokens.*\
*Improve and extensive use of logging system.*\
*Improve the exception throwing and managing.*
