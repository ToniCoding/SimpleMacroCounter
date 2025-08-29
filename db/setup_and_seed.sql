--- DATABASE INITIAL DESIGN SMC ---

--- Create database and administrator user ---
CREATE USER 'op_user'@'localhost' IDENTIFIED BY '1234';
GRANT ALL PRIVILEGES ON SMC.* TO 'op_user'@'localhost';
FLUSH PRIVILEGES;

--- Users table ---
CREATE OR REPLACE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    alias VARCHAR(30) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    auth_token VARCHAR(255) NULL UNIQUE,
    age INT DEFAULT 18,
    status ENUM('active','inactive','banned') DEFAULT 'active',
    registered_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
    token_expires DATETIME NULL
);

--- Metrics table ---
CREATE OR REPLACE TABLE users_metrics (
  user_id INT PRIMARY KEY,
  creatine_streak INT DEFAULT 0,
  experience INT DEFAULT 0,
  
  FOREIGN KEY (user_id) REFERENCES users(id)
);

--- Calories per day table ---
CREATE OR REPLACE TABLE kcals_daily (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  date DATE NOT NULL,
  kcals SMALLINT NOT NULL DEFAULT 0,
  
  FOREIGN KEY (user_id) REFERENCES users(id),
  
  UNIQUE (user_id, date)
);

--- Products table (per 100g) ---
CREATE OR REPLACE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_name VARCHAR(75) NOT NULL,
    market VARCHAR(50) NOT NULL,
    kcal SMALLINT NOT NULL,
    protein DECIMAL(5,2) NOT NULL,
    carbohydrates DECIMAL(5,2) NOT NULL,
    fats DECIMAL(5,2) NOT NULL,
    fiber DECIMAL(5,2) NOT NULL,

    UNIQUE (product_name, market)
);

--- Test inserts ---

INSERT INTO users (username, alias, email, password, age, status)
VALUES
('fitmaria', 'maria_fit', 'maria@example.com', 'hashed_password_1', 25, 'active'),
('john_doe', 'johnny', 'john@example.com', 'hashed_password_2', 30, 'active');

INSERT INTO users_metrics (user_id, creatine_streak, experience)
VALUES
(1, 10, 1500),
(2, 5, 800);

INSERT INTO kcals_daily (user_id, date, kcals)
VALUES
(1, '2025-07-21', 2200),
(1, '2025-07-22', 2100),
(2, '2025-07-21', 2500);

INSERT INTO products (product_name, market, kcal, protein, carbohydrates, fats, fiber)
VALUES
('Chicken Breast', 'Mercadona', 165, 31.0, 0.0, 3.6, 0.0),
('Brown Rice', 'Carrefour', 112, 2.6, 23.0, 0.9, 1.8),
('Almonds', 'Lidl', 579, 21.1, 21.7, 49.9, 12.5);

--- Restart users ID autoincrement ---
ALTER TABLE smc.users AUTO_INCREMENT = 1;
