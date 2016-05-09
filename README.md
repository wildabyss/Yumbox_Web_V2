# Yumbox Web Application #

The front-end and back-end of the server-side Yumbox application for home cooking sharing.

## Requirements ##

- PHP 5.6 or higher
- MySQL 5.6 or higher
- Apache or IIS

## Application Setup ##

1. Set document root to /public
2. Create MySQL user 'yumbox'@'localhost'
3. Source /application/database/build_database.sql; modify the database name from yumbox_dev to production name if needed.
4. Run composer on /composer.json
5. Create /application/config/secret_config.php with the following and fill in the values:

```php
<?php

/** This file should not be checked into the code source or be made public **/

$config['base_url'] 			= 'https://localhost/';
$config["database_username"] 	= "yumbox";
$config["database_password"] 	= string;
$config['encryption_key'] 		= string;

$config['facebook_app_id']		= string;
$config['facebook_secret']		= string;

$config['google_client_id']		= string;
$config['google_client_secret']	= string;

$config['wechat_app_id']		= string;
$config['wechat_secret']		= string;

$config['stripe_secret_key']	= string;
$config['stripe_public_key']	= string;

// featured dishes
$config['featured_rush_id']		= int;
$config['featured_explore_id']	= int;
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

