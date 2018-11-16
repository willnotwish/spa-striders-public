<?php
define('DB_NAME',     $_ENV["PUBLIC_WP_DB_NAME"]);
define('DB_USER',     $_ENV["PUBLIC_WP_DB_USER"]);
define('DB_PASSWORD', $_ENV["PUBLIC_WP_DB_PASSWORD"]);
define('DB_HOST',     $_ENV["PUBLIC_WP_DB_HOST"]);

define('DB_CHARSET', 'utf8');
define('DB_COLLATE', '');

define('AUTH_KEY',         $_ENV['AUTH_KEY']);
define('SECURE_AUTH_KEY',  $_ENV['SECURE_AUTH_KEY']);
define('LOGGED_IN_KEY',    $_ENV['LOGGED_IN_KEY']);
define('NONCE_KEY',        $_ENV['NONCE_KEY']);
define('AUTH_SALT',        $_ENV['AUTH_SALT']);
define('SECURE_AUTH_SALT', $_ENV['SECURE_AUTH_SALT']);
define('LOGGED_IN_SALT',   $_ENV['LOGGED_IN_SALT']);
define('NONCE_SALT',       $_ENV['NONCE_SALT']);

$table_prefix  = 'wp_';
define('WP_DEBUG', $_ENV["WP_DEBUG"]);

// define('WP_DEBUG',         true);
// define('WP_DEBUG_LOG',     true);
// define('WP_DEBUG_DISPLAY', false);

// Use the HTTP_X_FORWARDED_PROTO (the de facto standard) to set
// the relevant variables, in case wer are behind a reverse proxy
if ( $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' )
{
    $_SERVER['HTTPS']       = 'on';
    $_SERVER['SERVER_PORT'] = 443;
}


// S3 related - for storing uploads
define('S3_UPLOADS_BUCKET',     $_ENV['PUBLIC_WP_S3_UPLOADS_BUCKET']);
define('S3_UPLOADS_KEY',        $_ENV['PUBLIC_WP_S3_UPLOADS_KEY']);
define('S3_UPLOADS_SECRET',     $_ENV['PUBLIC_WP_S3_UPLOADS_SECRET']);
define('S3_UPLOADS_REGION',     $_ENV['PUBLIC_WP_S3_UPLOADS_REGION']);
define('S3_UPLOADS_BUCKET_URL', $_ENV['PUBLIC_WP_S3_UPLOADS_BUCKET_URL']);
define('S3_UPLOADS_ENDPOINT',   $_ENV['PUBLIC_WP_S3_UPLOADS_ENDPOINT']);

if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

require_once(ABSPATH . 'wp-settings.php');

// We don't want auto update. Don't turn this on without good reason.
define( 'WP_AUTO_UPDATE_CORE', false );
