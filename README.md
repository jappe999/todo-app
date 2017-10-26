# todo-app
A simple todo app for a school assignment

## Requirements
- PHP 7 or higher
- A database (duh)

## Setting her up
### Database
1. Import config/database.sql into your database.
2. Copy database.php.example to database.php
3. Configure database.php with your own credentials.

### Hosting
#### Windows server
This repository contains a web.config file for windows servers.
This file makes sure every URL is rewritten to public/index.php.

#### Apache
I need to write a .htaccess file for this one.

#### PHP server command
If you run ```php -S localhost:[your_port] -t public``` in the root directory of the repository, you'll get the full functionality of the application as well.
