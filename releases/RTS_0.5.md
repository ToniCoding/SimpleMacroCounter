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