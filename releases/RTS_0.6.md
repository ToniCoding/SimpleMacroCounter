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
- [SMC-NEF#000] Macro decimals are not taken into account.
- [SMC-NEF#000] Cannot reduce more macros than consumed thrown while reducing less macros than consumed.
- [SMC-NEF#000] On settings update, zeroes were not ignored.

**Other**
- Created `releases` directory containing RTS (Ready To Ship) that act as a release manifesto. From now on, the release notes will only contain the latest update.
