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
