# Wolf shop coding assessment - (PHP)

### Dear Reviewer
Thank you for the opportunity of take-home coding assessment. It was indeed a challenging and rewarding experience. I have completed all tasks as per the requirements, and I look forward to discussing my approach and solutions further in the next stage. I'm excited to continue with the process!

### Project Overview

This project manages inventory updates based on specific rules for items with `SellIn` and `Quality` values. Key features include:

- Daily decrease of `SellIn` and `Quality` for all items.
- Once the sell-by date passes, `Quality` decreases twice as fast.
- Constraints ensure `Quality` is never negative or above 50 (except for legendary items).
- Special rules apply for items like "Apple AirPods," "Samsung Galaxy S23," "Apple iPad Air," and "Xiaomi Redmi Note 13" to handle quality differently.
  
The system is optimized to handle edge cases and new item types while preserving core functionality.

## Installation

To set up the project, you can follow the commands outlined below. While I could have written a `script.sh` or `docker-compose.yml` for automated execution, I opted to provide the commands directly due to time constraints.

1. **Install Composer dependencies:**

   ```bash
   composer install
   ```

2. **Setup .env file and it's variables:**
    
    ```bash
    cp .env.example .env
    ```
   
3. **Clear the old cache:**

   ```bash
   php artisan clear-compiled
   ```

4. **Recreate the application cache:**

   ```bash
   php artisan optimize
   ```

5. **Run database migrations:**

   ```bash
   php artisan migrate --force
   ```

6. **Run the database seeder:**

   ```bash
   php artisan db:seed 
   ```
   or 
   ```bash
   php artisan db:seed --class=ProductRuleSeeder
   php artisan db:seed --class=UserSeeder
   ```

Now, you will run the application locally:

- **Web server:**

   ```bash
   php artisan serve
   ```

- **Processes Jobs:**

   ```bash
   php artisan queue:work
   ```

The web server will be accessible at `localhost:8000` or `http://127.0.0.1:8000/` by default.

### Commands
These commands can be executed on the host.

#### Import from URL
Import items from a specified URL:

```bash
php artisan app:import-inventory
```

#### Update All Items in the Database by 1 Day (scheduled daily at midnight) 
Update all products/items in the database to reflect a day passing:

```bash
php artisan app:update-inventory
```

Make sure to run these commands in your terminal from the project's root directory. This process will prepare your environment and ensure that the application is up to date and ready for use.

## API

You can import the following API endpoints using Postman or cURL to interact with the application.

### Products Listing API

To retrieve a list of products, you can use the following cURL command:

```bash
curl --location --request GET 'http://127.0.0.1:8000/api/products?page=2' \
--header 'Accept: application/json' \
--header 'Content-Type: application/json' \
--header 'Authorization: Basic am9obmRvZUBleGFtcGxlLmNvbTpwYXNzd29yZA==' \
--data-raw '{
    "email": "johndoe@example.com",
    "password": "password"
}'
```

### Upload Image (Update Product)

To upload an image for a specific product, use the following cURL command. Replace `{product}` with the actual product ID:

```bash
curl --location 'http://127.0.0.1:8000/api/products/3752a0a3-b707-4ad4-8c0d-b9e1989c34ca?_method=PUT' \
--header 'Accept: application/json' \
--header 'Authorization: Basic am9obmRvZUBleGFtcGxlLmNvbTpwYXNzd29yZA==' \
--form 'image=@"/home/talha/Downloads/profile.jpeg"'
```

Make sure to replace the base url and image path with the actual path.
