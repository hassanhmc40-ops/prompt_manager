create database prompt_managger;
use prompt_manager;
CREATE TABLE users (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    username    VARCHAR(50) NOT NULL,
    email       VARCHAR(100) NOT NULL UNIQUE,
    password    VARCHAR(255) NOT NULL,
    role        ENUM('user', 'admin') DEFAULT 'user',
    created_at  DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE categories (
    id    INT AUTO_INCREMENT PRIMARY KEY,
    name  VARCHAR(50) NOT NULL UNIQUE,
    slug  VARCHAR(50) NOT NULL UNIQUE
);
-- // what slug is for? slug is a URL-friendly version of the category name, often used in web applications to create readable and SEO-friendly URLs. For example, if the category name is "Artificial Intelligence", the slug might be "artificial-intelligence". This allows users to easily understand the content of the URL and improves search engine optimization.

CREATE TABLE prompts (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    title        VARCHAR(150) NOT NULL,
    content      TEXT NOT NULL,
    user_id      INT NOT NULL,
    category_id  INT NOT NULL,
    created_at   DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at   DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (category_id) REFERENCES categories(id)
);
