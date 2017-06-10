<?php
/*
Plugin Name: bbP API
Description: A bbp REST API.
Plugin URI: https://wordpress.org/plugins/bbp-api/
Author: Pascal Casier
Author URI: http://casier.eu/wp-dev/
Text Domain: bbp-api
Version: 1.0.2
License: GPL2
*/

// No direct access
if ( !defined( 'ABSPATH' ) ) exit;

define ('BBPAPI_VERSION' , '1.0.2');

if(!defined('BBPAPI_PLUGIN_DIR'))
	define('BBPAPI_PLUGIN_DIR', dirname(__FILE__));
if(!defined('BBPAPI_URL_PATH'))
	define('BBPAPI_URL_PATH', plugin_dir_url(__FILE__));


include(BBPAPI_PLUGIN_DIR . '/inc/forums.php');
include(BBPAPI_PLUGIN_DIR . '/inc/topics.php');
include(BBPAPI_PLUGIN_DIR . '/inc/replies.php');
include(BBPAPI_PLUGIN_DIR . '/inc/topic-tags.php');
include(BBPAPI_PLUGIN_DIR . '/inc/stats.php');
include(BBPAPI_PLUGIN_DIR . '/inc/users.php');

/*
 * Register all routes
*/

add_action( 'rest_api_init', function () {
	register_rest_route( 'bbp-api/v1', '/forums/', array(
		'methods' => 'GET',
		'callback' => 'bbp_api_forums',
	) );
	register_rest_route( 'bbp-api/v1', '/forums/(?P<id>\d+)', array(
		'methods' => 'GET',
		'callback' => 'bbp_api_forums_one',
	) );
	register_rest_route( 'bbp-api/v1', '/topics/', array(
		'methods' => 'GET',
		'callback' => 'bbp_api_topics',
	) );
	register_rest_route( 'bbp-api/v1', '/topics/(?P<id>\d+)', array(
		'methods' => 'GET',
		'callback' => 'bbp_api_topics_one',
	) );
	register_rest_route( 'bbp-api/v1', '/replies/', array(
		'methods' => 'GET',
		'callback' => 'bbp_api_replies',
	) );
	register_rest_route( 'bbp-api/v1', '/replies/(?P<id>\d+)', array(
		'methods' => 'GET',
		'callback' => 'bbp_api_replies_one',
	) );
	register_rest_route( 'bbp-api/v1', '/topic-tags/', array(
		'methods' => 'GET',
		'callback' => 'bbp_api_topic_tags',
	) );
	register_rest_route( 'bbp-api/v1', '/stats/', array(
		'methods' => 'GET',
		'callback' => 'bbp_api_stats',
	) );
	register_rest_route( 'bbp-api/v1', '/users/(?P<id>\d+)', array(
		'methods' => 'GET',
		'callback' => 'bbp_api_users_one',
	) );
} );
