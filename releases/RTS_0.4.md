### v0.4.0 (06-10-2025) - SMC general enhancement
**Features added**
- Added custom exceptions directory.
- Added custom exception `ExceededMacroLimitException` that will be thrown if the user tries to add more than 500 for any macro-nutrient.
- **[CRITICAL]** Implemented namespaces.
- Added new excepcion `NoRecordFoundException`.

**Changed**
- Cleaner path router.
- New users now have general macro-nutrient goals setted by default.
- Improved the logging system and added `WARN` and `ERROR` type messages.
- Implemented more logging across *controllers* to keep better trace.
- Length exception is now thrown if the user input is longer than 3 characters for the add macro-nutrient functionality.
- Exceeded macro exception is now thrown if the user input is higher than 400 for any macro-nutrient.
- Changed the condition for the exception in change macro-nutrient goal, before user could input 4 number long numbers.
- Code refactorization that included:
  - Replace unnecesary `"` with `'`.
  - Authenticatation tokens were moved in some cases.
- Improved the action handling in `ModGoalsPage`;
- Renamed `imag` directory to `img`.
- Included favicon in all templates.
- Logger now records the file the logged message comes from.
- A `NoRecordFoundException` is thrown if there are no records in database.

**Fixed issues**
- Fixed an issue that came from the refactor phase. Logger was writing `\n` explicitly.

**To do**
- Streaks user flow.
