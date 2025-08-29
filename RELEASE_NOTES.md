# SimpleMacroCounter Release Notes

---

## Project Description

**SimpleMacroCounter** is a lightweight application designed to help users track and manage their daily macronutrient intake. The project focuses on simplicity, ease of use, and providing essential features for nutrition tracking.

---

## Project Tree

```
SimpleMacroCounter/
├── app/
│   └── controller
│   └── handlers
│   └── helpers
│   └── invoker
│   └── logging
│   └── model
│   └── repository
│   └── view
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
├── config.php
└── index.php
└── LICENSE
└── nextSteps.txt
└── README.md
└── RELEASE_NOTES.md
```

---

## Project Roadmap

- **v0.1.0**: Initial release with basic macro tracking.
- **v0.2.0**: User registration and administration.

---

## Project Versions

### v0.2.0 (Unreleased)
**Features Added:**
- User profile creation through UI.
- Better code documentation.
- Replaced the plain text file 'nextSteps' with the better option 'RELEASE_NOTES.md'.
- Created a service container.

### v0.1.0 (29-07-2025)
**Features Added:**
- Generic macro is added.
- Macro calorie calculation.
- Construction of project structure.

---

## Project milestones

### Generic macros and software foundation.
Generic macro administration and calorie calculation is now possible following the project MVC pattern.<br/>
Reached on version 0.1.0.<br/>
Reach date: Tuesday, 29 July 2025.

### User creation and administration overhaul.
The user can now register through UI and can be administrated at database level.<br/>
Reached on version ---<br/>
Reach date: Not reached.

### User registration and login.
Any user can now register and login through dedicated UI.
Reached on version ---<br/>
Reach date: Not reached.

### User connected macros.
Any user can know their daily macros and calorie intake.
Reached on version ---<br/>
Reach date: Not reached.

### Streaks
Any user can know their current creatine and proteine streak. The streak breaks if the user don't take it for more than 2 days.
Reached on version ---<br/>
Reach date: Not reached.
