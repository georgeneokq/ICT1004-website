## Introduction
Pet website for ICT1004.

## Installation
Clone this repository, create .env file with reference to .example.env file.

Composer is required to install dependencies. Run `composer install` to install dependencies.

## For local development
Run `php -S localhost:8000` in the root folder to start a local development server.

## Deployment for shared hosting
1. Transfer the public files (/css, /js, index.php) into shared hosting's public folder.
2. Transfer the rest of the files into a separate folder that is placed in the shared hosting's root folder.
3. Edit the constant SERVER_ROOT_PATH in **bootstrap/app.php** according to where you placed the non-public files.