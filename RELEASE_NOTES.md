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

## Current Project Version

### v0.7.0 (23-06-2026) - The Final Marks

**Release descritpion**
The intention of this release is to polish SMC as much as possible, integrate the OFF database to the products database and add minor functionality.

**Features added**
- Piggy Bank: The Piggy Bank helps users keep track of their weekly calorie goals. Based on an algorithm, users can see the risk of going over their weekly calorie goal.
- Extensive products database: We implemented the OpenFoodFacts public database into the SMC database, and it's ready to be used with more than 35K products.
- Product pagination: There are now pagination avalaible if there is more than 100 products that match with the search or shown by default.
- Brands: All products now have brands for better distinction.

**Changed**
- Merged add and reduce macros pages into one.

**Technical**
- To show the food catalog, SMC no longer uses the `Foods` table, now it uses the `Products` one.
- Added one more column to `Products` table (brands).
- Added several toString methods that allow more detailed logging.
- Adaptataion of JS, CSS and Twig for the new information about product branding.
- Implemented temporal mini-API for product search.
- Implemented FULLTEXT index for `product_name`, `market` and `brand` in `Products` table.
- Move burguer menu to a single JS file.

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
