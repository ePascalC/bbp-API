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

/*
 * Register all routes
*/

add_action( 'rest_api_init', function () {
	register_rest_route( 'bbp-api/v1', '/forums/', array(
		'methods' => WP_REST_Server::READABLE,
		'callback' => 'bbp_api_forums',
	) );
	register_rest_route( 'bbp-api/v1', '/forums/(?P<id>\d+)', array(
		array(
		'methods' => WP_REST_Server::READABLE,
		'callback' => 'bbp_api_forums_one',
		),
		array(
			'methods' => WP_REST_Server::CREATABLE,
			'callback' => 'bbp_api_forums_post',
		),
	) );
	register_rest_route( 'bbp-api/v1', '/topics/', array(
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
	register_rest_route( 'bbp-api/v1', '/topics/(?P<id>\d+)', array(
			'methods' => WP_REST_Server::READABLE,
			'callback' => 'bbp_api_topics_one',
	) );
	register_rest_route( 'bbp-api/v1', '/replies/', array(
		'methods' => WP_REST_Server::READABLE,
		'callback' => 'bbp_api_replies',
	) );
	register_rest_route( 'bbp-api/v1', '/replies/(?P<id>\d+)', array(
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
	register_rest_route( 'bbp-api/v1', '/topic-tags/', array(
		'methods' => WP_REST_Server::READABLE,
		'callback' => 'bbp_api_topic_tags',
	) );
	register_rest_route( 'bbp-api/v1', '/stats/', array(
		'methods' => WP_REST_Server::READABLE,
		'callback' => 'bbp_api_stats',
	) );
} );

//helper functions
/*
 * Ensure that required fields are filled out.
 * array fields: fields to check
 * array submission_data: submitted data to iterate over
 * return $response: WP_REST_Response in case of bad field, otherwise zero.
*/
function bbp_api_filter_input($fields, $submission_data) {
	$bad_fields = "";
	foreach($fields as $field) {
		if(empty($submission_data[$field])) {
			$bad_fields .= $field . " ";
		}
	}
	if (!empty($bad_fields)) {
		$msg = "Missing fields:";
		$response = new WP_REST_Response( array($msg, $bad_fields) );
		$response->set_status( 422 );
		return $response;
	}
	return 0;
}
