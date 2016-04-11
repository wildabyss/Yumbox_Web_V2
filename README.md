# Yumbox Web Application #

The front-end and back-end of the server-side Yumbox application for home cooking sharing.

## Requirements ##

- PHP 5.4 or higher
- MySQL 5.1 or higher
- Apache or IIS

## Installation ##

1. Set document root to /public
2. Rename /application/config/config.php~ and /application/config/database.php~ to .php files
3. Modify /application/config/database.php accordingly
4. Modify /application/config/config.php for:
  1. $config['base_url']
  2. $config['encryption_key']
5. Source /application/database/build_database.sql
6. Modify /application/database/create_user.sql for the webuser password. Source the script.
7. Run composer on /composer.json

## Server Setup Notes ##

1. Make sure root password is secure
2. Disable unused ports on the server firewall
3. If the MySQL server is local, add the following line to /etc/my.cnf:
  bind-address = 127.0.0.1
4. Configure SSL

## Development Notes ##

- Do not enter passwords or the encryption key into config.php~ and database.php~