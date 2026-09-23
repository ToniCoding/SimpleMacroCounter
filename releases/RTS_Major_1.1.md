# v1.1 (Production planned: 2026-09-24) - SMC

**Release description**

In this release we have made a lot of internal changes to the code infrastructure such as decoupling the front-end from the back-end, fully implementing JWT and its adoption, increased logged information and more.

**New**

- **[Critical]** SMC REST API.
- **[Critical]** Fully implemented JWT token.
- **[Critical]** User registration and login redo.
- **[Critical]** Symfony now generates a JWT through the PHP session from the home page.
- **[Critical]** Full front-end and back-end decoupling.
- New DTOs for the API adoption.
- Complete adoption of API with JWT stateless authentication:
    - Home page.
    - Modify macros manually.
    - History.
    - Register foods.
    - Register intake.
    - Settings.
- Implemented a new unit test suite that tests all the four actual services of SMC.
- Implemented navigation bar as a Twig component.

**Changed**

- Monolog is now a production package, matching production.
- Deprecated `payloadCreator.js` for the new `payloadManager.js`.
- The JavaScript scripts are now better organized.
- Changed the endpoint for adding food intakes. Previous `addFood` --- Updated `add-food`.
- Redesign of the desktop navigation bar.
- Converted navigation bar to a common component in `templates/partails/NavigationBar.twig.html`.
- Minor changes to login form Twig template.
- Deleted unused code:
    - AppAuthenticator.
    - Previous logger.
    - Database scripts.
    - Deprecated security controller.

**Fixed**

- Manually changing the macros with commas as delimiter for decimal values resulted in `401 Bad Request` error.
- Fixed the risk algorithm showing on very high risk every monday.
- Desktop and mobile navigation bars were showing at the same time with under 767px widths.

**Documentation**

- Combined requests and response contracts into a single document per endpoint.
- Created `flows` to start adding documentation relative to every defined flow.