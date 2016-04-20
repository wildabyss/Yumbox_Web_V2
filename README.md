# Yumbox Web Application #

The front-end and back-end of the server-side Yumbox application for home cooking sharing.

## Requirements ##

- PHP 5.4 or higher
- MySQL 5.1 or higher
- Apache or IIS

## Application Setup ##

1. Set document root to /public
2. Source /application/database/build_database.sql
3. Modify /application/database/create_user.sql for the webuser password. Source the script.
4. Run composer on /composer.json
5. Create /application/config/secret_config.php with the following:

```php
<?php

/** This file should not be checked into the code source or be made public **/

$config['base_url'] 			= 'http://localhost/';
$config["database_username"] 	= 'yumbox';
$config["database_password"] 	= '';
$config['encryption_key'] 		= '';

$config['facebook_app_id']		= '';
$config['facebook_secret']		= '';

$config['google_client_id']		= '';
$config['google_client_secret']	= '';
```

## Server Setup ##

1. Make sure root password is secure
2. Disable unused ports on the server firewall
3. If the MySQL server is local, add the following line to /etc/my.cnf:
  bind-address = 127.0.0.1
4. Configure SSL

## Development Notes ##

