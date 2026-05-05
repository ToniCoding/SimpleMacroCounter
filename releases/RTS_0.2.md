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
