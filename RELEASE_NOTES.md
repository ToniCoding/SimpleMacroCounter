# SimpleMacroCounter Release Notes

---

## Project Description

**SimpleMacroCounter** is a lightweight application designed to help users track and manage their daily macronutrient intake. The project focuses on simplicity, ease of use, and providing essential features for nutrition tracking.

---

## Project Tree

```
SimpleMacroCounter/
├── app/
│   └── auth
│   └──  ─── Auth.php
│   └──  ─── AuthService.php 
│   └── controller
│   └──  ─── MacroCounterController.php
│   └──  ─── StreakController.php
│   └──  ─── UserController.php 
│   └── handlers
│   └──  ─── UserFormHandler.php
│   └── helpers
│   └──  ─── dateParser.php
│   └──  ─── htmlHelper.php
│   └──  ─── userInputs.php
│   └── invoker
│   └──  ─── UserFormInvoker.php
│   └── logging
│   └──  ─── Logger.php
│   └── model
│   └──  ─── Macro.php
│   └──  ─── MacrosCounter.php
│   └──  ─── Streak.php
│   └──  ─── User.php
│   └── repository
│   └──  ─── TableManagementRepository.php
│   └──  ─── UserRepository.php
│   └── view
│   └──  ─── MacroCounterView.php
├── config/
│   ├── db.php
│   ├── ObjectFactories.php
│   └── Services.php
├── db/
│   ├── setup_and_seed.sql
├── public/
│   ├── css
│   ├── html
├── .gitignore
├── AppConstants.php
├── bootstrap.php
└── index.php
└── LICENSE
└── README.md
└── RELEASE_NOTES.md
```

---

## Project Roadmap

- **v0.1.0**: Initial release with basic macro tracking.
- **v0.2.0**: User registration and administration.
- **v0.3.0**: Routing and data management.

---

## Project Versions

### v0.3.0 (Unreleased)
**Features added**
- Implemented PHP path router.
- Added new Apache configurations through .htaccess.
- Added new public dedicated pages.
    - Register form.
    - Login form.
    - Not found.
- Added additional authentication file to process the both register and login form data.

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

---

## Project milestones

### Generic macros and software foundation.
Generic macro administration and calorie calculation is now possible following the project MVC pattern.\
Reached on version 0.1.0.\
Reach date: Tuesday, 29 July 2025.\

### User creation and administration overhaul.
The user can now register through UI and can be administrated at database level.\
Reached on version ---\
Reach date: Not reached.

### User registration and login.
Any user can now register and login through dedicated UI.\
Reached on version ---\
Reach date: Not reached.

### User connected macros.
Any user can know their daily macros and calorie intake.\
Reached on version ---\
Reach date: Not reached.

### Streaks
Any user can know their current creatine and proteine streak. The streak breaks if the user don't take it for more than 2 days.\
Reached on version ---\
Reach date: Not reached.

## Future improvements
*Implement project structure validation.*\
*Implement JWT for user tokens.*\
*Implement Symfony or Laravel.*\
*Improve logging system.*
