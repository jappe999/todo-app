# todo-app
A simple todo app for a school assignment

## Requirements
- PHP 7 or higher
- A database (duh)
- NPM (only if you want to edit the front-end)

## Reading the docs
If you want to read the docs of the code, you can download PHPDocumenter and run the following from the root directory of the project:  
```
php phpDocumenter.phar -d ./ -t ./public/docs
```
If you visit [your_URL]/docs/index.html, you can read the docs. :)

## Setting her up
### Database
1. Import config/database.sql into your database.
2. Copy database.php.example to database.php
3. Configure database.php with your own credentials.

### NPM
If you want to edit the front-end of this project, you must run
` npm install ` and ` npm run watch `

### Hosting
#### Windows server
This repository contains a web.config file for windows servers.  
This file makes sure every URL is rewritten to public/index.php.

#### Apache
I need to write a .htaccess file for this one.

#### PHP server command
If you run ```php -S localhost:[your_port] -t public``` in the root directory of the project, you'll get the full functionality of the application as well.
