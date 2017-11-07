<?php
/**
 * Front to the WordPress application. This file doesn't do anything, but loads
 * wp-blog-header.php which does and tells WordPress to load the theme.
 *
 * @package WordPress
 */

/**
 * Tells WordPress to load the WordPress theme and output it.
 *
 * @var bool
 */
define('WP_USE_THEMES', true);

/*
	Added by Nick Nov 2017

	is_ssl() doesn't work as expected when behind a proxy or load balancer. It only looks at $_SERVER['HTTPS']
	AWS load balancers set X-Forwarded-Proto
*/

$_SERVER['HTTPS'] = !empty($_SERVER['X-FORWARDED-PROTO']) ? $_SERVER['X-FORWARDED-PROTO'] : 0;



/* This might be needed. Leave commented out for now
	$_SERVER['REMOTE_ADDR'] = $_SERVER['X-Forwarded-For'];
*/


/** Loads the WordPress Environment and Template */
require( dirname( __FILE__ ) . '/wp-blog-header.php' );
