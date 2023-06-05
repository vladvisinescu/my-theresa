### How to run the project
- clone the repository
- create a database and add the db name, user and password to the `.env` file
  - DB_DATABASE=my_theresa
  - DB_USERNAME=root
  - DB_PASSWORD=password
- run `composer install`
- the requested endpoint is located at `[GET] http://localhost/products` and the available params are `category` and `priceLessThan`
- for running tests type `php artisan test` (this will use the `sqlite` database located at `/database/database.sqlite`)

### Assumptions
- I have assumed that all discounts that are stored in the database are active and valid.
- I have assumed the website's administration area includes a form to create new discounts and a table to view them.

### Decisions
- I have included `.env` and `database.sqlite` files in the repository to make it easier to run the project. I wouldn't do that normally.
- I have decided to use the Laravel framework because it is the framework I am most familiar with, and it has a lot of built-in functionality that I can use to speed up development.
- I have over-engineered a separate `product_prices` table to keep track of the price history of a product in case it is necessary AND/OR the system could also have multiple prices for the same product (e.g. different currencies, price parity adjustments etc.).
- I have created logic for both percentage and fixed discounts, thinking that the system could have both types of discounts active at the same time.
- I have used Pest to create basic tests because I prefer its syntax over PHPUnit's.
- I took the liberty to create a separate `ProductCategory` model and table because I think it's easier to add features to the system in the future this way. For example, if we want to add a `description` field to the category, we can do it without modifying the `products` table.
- I have used a polymorphic One-To-Many relation between Products/Categories and Discounts because I think the relation separates the concerns better. Queries and code are then easier to read, write and understand.

### 'Nice to have'-s
- use memcached / Redis to cache the computed prices in order to avoid querying the database and using memory for calculations on every API request.
- suggested additional discounts table properties: `valid_from`, `valid_until` (datetime) OR `active` (boolean)
- validate `category` parameter actually exists in the database and `priceLessThen` is a positive integer

### Models
- Product -mf
    - id - index
    - sku - string
    - name - string
    - category_id - belongsTo:product_categories
    - [priceData] - has_one:product_prices
    - price - computed
    - created_at - dateTime
    - updated_at - dateTime
- ProductPrice -mf
    - id - index
    - product_id - belongsTo:products
    - original - integer
    - currency - string (USD, EUR, GBP) - default: EUR
    - created_at - dateTime
    - updated_at - dateTime
- PriceDiscount -m
    - id - index
    - type - string (percentage, fixed)
    - target_type - string (product, category)
    - target_id - integer
    - amount - integer
    - created_at - dateTime
    - updated_at  - dateTime
