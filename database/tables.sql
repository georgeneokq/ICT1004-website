CREATE DATABASE IF NOT EXISTS pet_db;

USE pet_db;

-- Admins for managing the website through a content management page
CREATE TABLE IF NOT EXISTS admins (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    admin_username VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL
) CHARACTER SET=utf8;

-- The general users of the website
CREATE TABLE IF NOT EXISTS users (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    first_name VARCHAR(40),
    last_name VARCHAR(40),
    biography TEXT(1000),
    profile_image_url VARCHAR(255),
    favourite_pets VARCHAR(255),
    activated INT(1) DEFAULT 1 -- By default, mark as activated.
) CHARACTER SET=utf8;

-- Table of users following one another
CREATE TABLE IF NOT EXISTS followers (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT(11) UNSIGNED, -- User following
    following_user_id INT(11) UNSIGNED, -- User being followed
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (following_user_id) REFERENCES users(id) ON DELETE CASCADE
) CHARACTER SET=utf8;

-- Posts to be made by users
CREATE TABLE IF NOT EXISTS posts (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT(11) UNSIGNED,
    content TEXT(2000),
    category VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) CHARACTER SET=utf8;

-- Sales
CREATE TABLE IF NOT EXISTS sales (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT(11) UNSIGNED,
    for_adoption INT(1) NOT NULL,
    location VARCHAR(255),
    category VARCHAR(255) NOT NULL,
    approved INT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
) CHARACTER SET=utf8;

-- Images that belong to a post. Links to an image in some external storage
CREATE TABLE IF NOT EXISTS post_images (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    post_id INT(11) UNSIGNED,
    post_image_url VARCHAR(255) NOT NULL,
    caption TEXT(255),
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE
) CHARACTER SET=utf8;

-- Post likes made by users
CREATE TABLE IF NOT EXISTS post_likes (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    post_id INT(11) UNSIGNED,
    user_id INT(11) UNSIGNED,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) CHARACTER SET=utf8;

-- Post comments made by users
CREATE TABLE IF NOT EXISTS post_comments (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    post_id INT(11) UNSIGNED,
    user_id INT(11) UNSIGNED,
    content TEXT(500) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) CHARACTER SET=utf8;

-- Store the user session tokens that will be refreshed with every request
CREATE TABLE IF NOT EXISTS user_sessions (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT(11) UNSIGNED,
    token VARCHAR(255) NOT NULL UNIQUE,
    expires_at TIMESTAMP NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) CHARACTER SET=utf8;

-- Store admin session tokens
CREATE TABLE IF NOT EXISTS admin_sessions (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    admin_id INT(11) UNSIGNED,
    token VARCHAR(255) NOT NULL UNIQUE,
    expires_at TIMESTAMP NOT NULL,
    FOREIGN KEY (admin_id) REFERENCES admins(id) ON DELETE CASCADE
) CHARACTER SET=utf8;