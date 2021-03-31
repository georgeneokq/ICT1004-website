## Introduction
Pet website for ICT1004.

## Installation
Clone this repository, create .env file with reference to .example.env file.

Composer is required to install dependencies. Run `composer install` to install dependencies.

## For local development
Run `php -S localhost:8000` in the root folder to start a local development server.

Install the chrome extension (or any other tools) to allow CORS, if testing the API from a different domain.

## Deployment for shared hosting
1. Transfer the public files (/css, /js, index.php) into shared hosting's public folder.
2. Transfer the rest of the files into a separate folder that is placed in the shared hosting's root folder.
3. Edit the constant SERVER_ROOT_PATH in **bootstrap/app.php** according to where you placed the non-public files.

## Deployment on Linux apache2 server

# Enable URL rewriting for Slim to do routing
Run the following commands to enable URL rewriting:

`sudo a2enmod rewrite` to enable mod_rewrite module

`sudo service apache2 restart` to apply changes

Add the following code into `/etc/apache2/apache2.conf` file (Assuming your public files are hosted in `/var/www/html` directory):

```
<Directory "/var/www/html">
        AllowOverride All
        Order allow,deny
        Allow from all
</Directory>
```

# Modify php.ini to handle large file uploads
Uploading files may result in a very large payload. It is recommended to increase the `upload_max_filesize` and `post_max_size` values in `php.ini` file.

```
; Maximum allowed size for uploaded files.
upload_max_filesize = 100M

; Must be greater than or equal to upload_max_filesize
post_max_size = 105M
```

When moving uploaded files in Linux, retrieve files from `$_FILES` superglobal and use `move_uploaded_file()` function to do move the files. Even though the `uploadedFile` objects returned by `$request->getUploadedFiles()` provide a convenient`moveTo()` method, it doesn't work in Linux (after spending a whole day tinkering around with user groups and permissions, this method still didn't work).

# Change your server machine's timezone
For timestamps to be accurate, do remember to change your timezone accordingly.