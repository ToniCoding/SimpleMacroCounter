**Release description**
First SMC hotfixing fixing some critical bugs observed after deployment to production environment, only bug fixing.

**Fixed**
- Added missing parameter for maximum days shown in history.
- Added new SVG icons replacing previous ones.
- Calorie calculator is now a class.
- Global handler now processes the previous unhandled exception for already registrated products.
- Product registering is now working with `Products` table instead of deprecated `Foods`.
- After goal settings change, the app now redirects to the home instead of staying in the settings page.
