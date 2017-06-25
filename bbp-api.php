<?php
/*
Plugin Name: bbPress API
Description: A bbPress REST API.
Plugin URI: https://wordpress.org/plugins/bbp-api/
Author: Pascal Casier
Author URI: http://casier.eu/wp-dev/
Text Domain: bbp-api
Version: 1.0.4
License: GPL2
*/

// No direct access
if ( !defined( 'ABSPATH' ) ) exit;

define ('BBPAPI_VERSION' , '1.0.4');

if(!defined('BBPAPI_PLUGIN_DIR'))
	define('BBPAPI_PLUGIN_DIR', dirname(__FILE__));
if(!defined('BBPAPI_URL_PATH'))
	define('BBPAPI_URL_PATH', plugin_dir_url(__FILE__));

foreach ( glob( BBPAPI_PLUGIN_DIR . "/inc/*.php" ) as $endpoint) {
	include $endpoint;
}

/*
 * Register all routes
*/

add_action( 'rest_api_init', function () {
	register_rest_route( 'bbp-api/v1', '/' . bbp_get_forum_slug() . '/', array(
		'methods' => WP_REST_Server::READABLE,
		'callback' => 'bbp_api_forums',
	) );
	register_rest_route( 'bbp-api/v1', '/' . bbp_get_forum_slug() . '/(?P<id>\d+)', array(
		array(
		'methods' => WP_REST_Server::READABLE,
		'callback' => 'bbp_api_forums_one',
		),
		array(
			'methods' => WP_REST_Server::CREATABLE,
			'callback' => 'bbp_api_forums_post',
		),
	) );
	register_rest_route( 'bbp-api/v1', '/' . bbp_get_topic_slug() . '/', array(
		array(
			'methods' => WP_REST_Server::READABLE,
			'callback' => 'bbp_api_topics',
		),
		array(
			'args' => array(
				'content' => array(
					'required' => True,
					'description' => 'Content for the initial post in the new topic.',
					'type' => 'string',
				),
				'title' => array(
					'required' => True,
					'description' => 'Title for the new topic.',
					'type' => 'string',
				),
				'forum_id' => array(
					'required' => True,
					'description' => 'ID of the forum to create the new topic within.',
					'type' => 'integer',
				),
				'email' => array(
					'required' => True,
					'description' => 'Email address of the thread author.',
					'type' => 'string',
				),
			),
			'methods' => WP_REST_Server::CREATABLE,
			'callback' => 'bbp_api_topics_post',
		),
	) );
	register_rest_route( 'bbp-api/v1', '/' . bbp_get_topic_slug() . '/(?P<id>\d+)', array(
			'methods' => WP_REST_Server::READABLE,
			'callback' => 'bbp_api_topics_one',
	) );
	register_rest_route( 'bbp-api/v1', '/' . bbp_get_reply_slug() . '/', array(
		'methods' => WP_REST_Server::READABLE,
		'callback' => 'bbp_api_replies',
	) );
	register_rest_route( 'bbp-api/v1', '/' . bbp_get_reply_slug() . '/(?P<id>\d+)', array(
		array(
			'methods' => WP_REST_Server::READABLE,
			'callback' => 'bbp_api_replies_one',
		),
		array(
			'args' => array(
				'content' => array(
					'required' => True,
					'description' => 'Content for the reply.',
					'type' => 'string',
				),
				'email' => array(
					'required' => True,
					'description' => 'Email address of the reply author.',
					'type' => 'string',
				),
			),
			'methods' => WP_REST_Server::CREATABLE,
			'callback' => 'bbp_api_replies_post',
		),
	) );
	register_rest_route( 'bbp-api/v1', '/' . bbp_get_topic_tag_tax_slug() . '/', array(
		'methods' => WP_REST_Server::READABLE,
		'callback' => 'bbp_api_topic_tags',
	) );
	register_rest_route( 'bbp-api/v1', '/stats/', array(
		'methods' => WP_REST_Server::READABLE,
		'callback' => 'bbp_api_stats',
	) );
} );
