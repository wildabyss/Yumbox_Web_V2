# Yumbox Web Application #

The front-end and back-end of the server-side Yumbox application for home cooking sharing.

## Requirements ##

- PHP 5.6 or higher
- MySQL 5.6 or higher
- Sass (install via rubygems)
- Sphinx Search

## Application Setup ##

1. Set upload_max_filesize = 20M in php.ini
2. Set document root to /public
3. Create /public/user_pics and /public/food_pics, make sure apache has write access to the both of them
4. Create MySQL user 'yumbox'@'localhost' and 'sphinx'@'localhost'
5. Source /application/database/build_database.sql; modify the database name from yumbox_dev to production name if needed.
6. Run composer on /composer.json
7. Create /application/config/secret_config.php with the following and fill in the values:

```php
<?php

/** This file should never be checked into the code source or be made public **/

// CodeIgniter configs
$config['base_url'] 			= 'https://yumbox.co/';
$config["database_username"] 	= "yumbox";
$config["database_password"] 	= "";
$config['encryption_key'] 		= '';

// Facebook app
$config['facebook_app_id']		= '';
$config['facebook_secret']		= '';

// Google oAuth2
$config['google_client_id']		= '';
$config['google_client_secret']	= '';

// Stripe payment
$config['stripe_secret_key']	= '';
$config['stripe_public_key']	= '';

// Google Map
$config['map_api_key']          = '';

// Mail queue
$config['queue_mail']				= true;
$config['queue_send_per_exe']	= 10;

// SMTP server
$config['website_email_address']    = 'info@yumbox.ca';
$config['website_email_name']       = 'Yumbox';
$config['website_replyto_address']  = '';
$config['website_replyto_name']     = '';
$config['smtp_host']                = 'smtp.office365.com';
$config['smtp_port']                = '587';
$config['smtp_authentication']      = '1';
$config['smtp_username']            = 'info@yumbox.ca';
$config['smtp_password']            = '';
$config['smtp_security']            = 'tls';

// tax rate
$config['tax_rate']				= 0.0;
// take rate (commission rate)
$config['take_rate']			= 0.0;
// take rate for vendors
$config['take_rate_vendor']		= 0.1;

// featured dishes
$config['featured_rush_id']		= 8;
$config['featured_explore_id']	= 9;
```

8. If this is the production environment, create file _prd.txt in root
9.1 Symlink the sphinx configuration file:

```bash
ln -s {PATH_TO_ROOT}/sphinx.conf /etc/sphinx/sphinx.conf
```

9.2 Download English dictionaries to /usr/local/share/sphinx/dicts/ from http://sphinxsearch.com/downloads/dicts/

9.3 Make sure searchd is started as a service:

```bash
service searchd start
chkconfig searchd on
```

9.4 Add the following into root level crontab:

```bash
# Sphinx indexer
*/15 * * * * indexer --rotate --all
```

10. Recommend setting queue_mail = true in secret_config.php to enable asynchronous mail notification.
Add the following to the root level crontab:

```bash
# Mail queue
*/2 * * * * php {PATH_TO_ROOT}/public/index.php mail_queue serve
```

## Server Setup ##

1. Make sure root password is secure
2. Disable unused ports on the server firewall
3. Configure nginx as per https://www.nginx.com/resources/wiki/start/topics/recipes/codeigniter/
4. If the MySQL server is local, add the following line to /etc/my.cnf:
  bind-address = 127.0.0.1
5. Configure SSL

The nginx config file should look as follows:

```bash
server {
	listen				80;
	server_name			yumbox.co;
	return				301 https://$server_name$request_uri;
}

server {
    listen				443;
    server_name			yumbox.co;

    ssl					on;
    ssl_certificate 	/etc/nginx/ssl/server.crt;
    ssl_certificate_key	/etc/nginx/ssl/server.key;

    root 				/var/www/dev.yumbox.co/public/;
    index 				index.html index.php;

    # set expiration of assets to MAX for caching
    location ~* \.(ico|css|js|gif|jpe?g|png)(\?[0-9]+)?$ {
        expires 		max;
        log_not_found 	off;
    }

    location / {
        # Check if a file or directory index file exists, else route it to index.php.
        try_files 		$uri $uri/ /index.php;
    }

    location ~* \.php$ {
        fastcgi_pass 127.0.0.1:9000;
        include fastcgi_params;
        fastcgi_param	SCRIPT_FILENAME		$document_root$fastcgi_script_name;
        fastcgi_param	QUERY_STRING		$query_string;

    }
}
```

## Development Notes ##

