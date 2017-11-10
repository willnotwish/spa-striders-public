<?php
define('DB_NAME', $_SERVER['RDS_DB_NAME']);
define('DB_USER', $_SERVER['RDS_USERNAME']);
define('DB_PASSWORD', $_SERVER['RDS_PASSWORD']);
define('DB_HOST', $_SERVER['RDS_HOSTNAME']);
define('DB_CHARSET', 'utf8');
define('DB_COLLATE', '');
define('AUTH_KEY',         $_SERVER['AUTH_KEY']);
define('SECURE_AUTH_KEY',  $_SERVER['SECURE_AUTH_KEY']);
define('LOGGED_IN_KEY',    $_SERVER['LOGGED_IN_KEY']);
define('NONCE_KEY',        $_SERVER['NONCE_KEY']);
define('AUTH_SALT',        $_SERVER['AUTH_SALT']);
define('SECURE_AUTH_SALT', $_SERVER['SECURE_AUTH_SALT']);
define('LOGGED_IN_SALT',   $_SERVER['LOGGED_IN_SALT']);
define('NONCE_SALT',       $_SERVER['NONCE_SALT']);
$table_prefix  = 'wp_';
define('WP_DEBUG', false);


// S3 related - for storing uploads

define( 'S3_UPLOADS_BUCKET', 'striders-uploads' );
define( 'S3_UPLOADS_KEY', 'AKIAI7UKXNGER2SIMOBQ' );
define( 'S3_UPLOADS_SECRET', '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' );
define( 'S3_UPLOADS_REGION', 'eu-west-1' ); // the s3 bucket region, required for Frankfurt, Beijing & Sydney.



if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');


require_once(ABSPATH . 'wp-settings.php');
