CREATE DATABASE IF NOT EXISTS `smc` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `smc`;

CREATE TABLE IF NOT EXISTS `users` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    user_alias VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    age INT NOT NULL,
    status VARCHAR(10) NOT NULL,
    created_time DATETIME NOT NULL,
    last_login DATETIME NULL,
    UNIQUE KEY uniq_username (username),
    UNIQUE KEY uniq_email (email)
);

CREATE TABLE IF NOT EXISTS `access_token` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    value VARCHAR(255) NOT NULL UNIQUE,
    user_id INT NOT NULL,
    expires_at DATETIME NOT NULL,
    CONSTRAINT fk_access_token_user FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS `kcals_daily` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    date DATETIME NOT NULL,
    kcals INT NOT NULL,
    protein INT NOT NULL,
    carbs INT NOT NULL,
    fats INT NOT NULL,
    CONSTRAINT fk_kcals_user FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS `products` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_name VARCHAR(75) NOT NULL,
    market VARCHAR(50) NOT NULL,
    kcal SMALLINT NOT NULL,
    protein DECIMAL(5,2) NOT NULL,
    carbs DECIMAL(5,2) NOT NULL,
    fats DECIMAL(5,2) NOT NULL,
    fiber DECIMAL(5,2) NOT NULL
);

CREATE TABLE IF NOT EXISTS `user_goals` (
    user_id INT PRIMARY KEY,
    protein INT NOT NULL,
    carbs INT NOT NULL,
    fats INT NOT NULL,
    date_time DATETIME NOT NULL,
    CONSTRAINT fk_user_goals_user FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS `user_metrics` (
    user_id INT PRIMARY KEY,
    creatine_streak INT NOT NULL,
    experience INT NOT NULL,
    CONSTRAINT fk_user_metrics_user FOREIGN KEY (user_id) REFERENCES users(id)
);
