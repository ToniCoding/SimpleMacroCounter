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
- **v1.1**: The Internal Frame *(current)*
    - **v1.1.1**: SMC #2 HotFix

## Current Project Version

**Release description**

This release serves as a crucial hotfix addressing several production issues, alongside important code refactoring, infrastructure optimizations, and bug fixes to stabilize core features such as user registration, settings management, and authentication pathways.

**New**

- Integrated `MapRequestPayload` across API controllers to streamline request mapping.

**Changed**

- Replaced Symfony form helpers in `SettingsTemplate.twig.html` with native HTML elements to resolve Twig `TemplateWrapper` unwrap errors.
- Cleaned up the Settings controller by removing unused POST logic and residual rendering code.
- Updated `config/packages/framework.yaml` to explicitly enable the serializer and configure native `flock` file locking.
- Optimized Monolog logging strategies and channels in `config/packages/monolog.yaml` across development, test, and production environments.
- Refactored API DTOs using property promotion and `readonly` properties, removing redundant getters and setters.

**Fixed**

- Fixed frontend user registration by correctly positioning `registerNewUser.js` in `public/js/utils/` and properly configuring form submission with `preventDefault` and the `shouldFollowRedirection` flag.
- Resolved a bug where registering with an already existing email address incorrectly returned a `200 OK` status.
- Corrected a namespace and configuration typo in service definitions for EventListeners.
- Fixed Lexik JWT authentication setup in `config/packages/lexik_jwt_authentication.yaml` by updating private and public key paths using `%kernel.project_dir%` and defining an explicit pass phrase to prevent environment variable resolution failures in production.
- Wired up the settings API endpoint in the Twig template while preserving exact element IDs, names, and CSS classes.

**Documentation**

- No specific documentation updates included in this hotfix release.

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

In this section, we describe the future improvements that are on the SMC roadmap.

- *SMC Redesign.*
- *Implement project structure and file permissons validation.*
