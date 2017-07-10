<?php
/*
Plugin Name: bbPress API
Description: A bbPress REST API.
Plugin URI: https://wordpress.org/plugins/bbp-api/
Author: Pascal Casier
Author URI: http://casier.eu/wp-dev/
Text Domain: bbp-api
Version: 1.0.10
License: GPL2
*/

// No direct access
if ( !defined( 'ABSPATH' ) ) exit;

define ('BBPAPI_VERSION' , '1.0.10');

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

add_action( 'rest_api_init', function() {
	// FORUMS LIST
	$args = array(
		'methods' => WP_REST_Server::READABLE,
		'callback' => 'bbp_api_forums',
	);
	// register both forum slug as /forums
	register_rest_route( 'bbp-api/v1', '/' . bbp_get_forum_slug() . '/', $args );
	if ( bbp_get_forum_slug() != 'forums' )
		register_rest_route( 'bbp-api/v1', '/forums/', $args );
	
	// FORUM One specific forum with meta data and topics
	$args = array(
		array(
			'methods' => WP_REST_Server::READABLE,
			'callback' => 'bbp_api_forums_one',
		),
		array(
			'args' => array(
				'id' => array(
					'validate_callback' => function( $param, $request, $key ) {
						return is_numeric( $param );
					}
				),
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
				'email' => array(
					'required' => True,
					'description' => 'Email address of the thread author.',
					'type' => 'string',
				),
			),
			'methods' => WP_REST_Server::CREATABLE,
			'callback' => 'bbp_api_newtopic_post',
		),
	);
	// register both forum-slug as well as /forums
	register_rest_route( 'bbp-api/v1', '/' . bbp_get_forum_slug() . '/(?P<id>\d+)', $args );
	if ( bbp_get_forum_slug() != 'forums' )
		register_rest_route( 'bbp-api/v1', '/forums/(?P<id>\d+)', $args );
	
	// TOPICS
	$args = array(
		array(
			'methods' => WP_REST_Server::READABLE,
			'callback' => 'bbp_api_topics',
		),
	);
	// register both topic-slug as well as /topics
	register_rest_route( 'bbp-api/v1', '/' . bbp_get_topic_slug() . '/', $args );
	if ( bbp_get_topic_slug() != 'topics' )
		register_rest_route( 'bbp-api/v1', '/topics/', $args );
	
	// TOPIC One specific topic with meta data and replies
	// The POST request is for a reply to this topic
	$args = array(
		array(
			'methods' => WP_REST_Server::READABLE,
			'callback' => 'bbp_api_topics_one',
		),
		array(
			'args' => array(
				'id' => array(
					'validate_callback' => function( $param, $request, $key ) {
						return is_numeric( $param );
					}
				),
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
			'callback' => 'bbp_api_replytotopic_post',
		),
	);
	register_rest_route( 'bbp-api/v1', '/' . bbp_get_topic_slug() . '/(?P<id>\d+)', $args );
	if ( bbp_get_topic_slug() != 'topics' )
		register_rest_route( 'bbp-api/v1', '/topics/(?P<id>\d+)', $args );
	
	// REPLIES
	$args = array(
		'methods' => WP_REST_Server::READABLE,
		'callback' => 'bbp_api_replies',
	);
	// register both reply-slug as well as /replies
	register_rest_route( 'bbp-api/v1', '/' . bbp_get_reply_slug() . '/', $args );
	if ( bbp_get_reply_slug() != 'replies' )
		register_rest_route( 'bbp-api/v1', '/replies/', $args );
	
	// REPLIES One specific reply with meta data
	// The POST request is for a reply to this reply
	$args = array(
		array(
			'methods' => WP_REST_Server::READABLE,
			'callback' => 'bbp_api_replies_one',
		),
		array(
			'args' => array(
				'id' => array(
					'validate_callback' => function( $param, $request, $key ) {
						return is_numeric( $param );
					}
				),
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
			'callback' => 'bbp_api_replytoreply_post',
		),
	);
	// register both reply-slug as well as /replies
	register_rest_route( 'bbp-api/v1', '/' . bbp_get_reply_slug() . '/(?P<id>\d+)', $args );
	if ( bbp_get_reply_slug() != 'replies' )
		register_rest_route( 'bbp-api/v1', '/replies/(?P<id>\d+)', $args );

	// TOPICTAGS
	$args = array(
		'methods' => WP_REST_Server::READABLE,
		'callback' => 'bbp_api_topic_tags',
	);
	// register both reply-slug as well as /replies
	register_rest_route( 'bbp-api/v1', '/' . bbp_get_topic_tag_tax_slug() . '/', $args );
	if ( bbp_get_reply_slug() != 'topic-tags' )
		register_rest_route( 'bbp-api/v1', '/topic-tags/', $args );
	
	// STATS
	register_rest_route( 'bbp-api/v1', '/stats/', array(
		'methods' => WP_REST_Server::READABLE,
		'callback' => 'bbp_api_stats',
	) );
} );
