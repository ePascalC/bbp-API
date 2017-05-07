<?php
/**
 * Class PostTopic
 *
 * @package Bbp_API
 */

/**
 * Testing the posting a topic.
 */
class PostTopic extends WP_UnitTestCase {
	/**
	* setting up the WP REST Server
	*/
	protected $prefix = "/bbp-api/v1";
	protected $registeredRoutes = array(
		"/forums",
		"/topics",
		"/replies",
		"/topic-tags",
		"/stats"
	);

	function setUp() {
		parent::setUp();
		global $wp_rest_server;
		$this->server = $wp_rest_server = new WP_REST_Server;
		do_action( 'rest_api_init' );

	}
	/**
	 * A single example test.
	 */
	function testRouteRegistration() {
		$routes = $this->server->get_routes();
		foreach ($this->registeredRoutes as &$route) {
			$this->assertArrayHasKey( $this->prefix . $route, $routes );
		}
	}
}
