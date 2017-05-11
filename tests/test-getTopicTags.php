<?php
/**
 * Class PostTopic
 *
 * @package Bbp_API
 */

 // Give access to common functions for tests.
 require_once( 'commonFunctions.php' );

/**
 * Testing the posting a topic.
 */
class PostTopic extends WP_UnitTestCase {
	/**
	* setting up the WP REST Server
	*/
	protected $prefix = "/bbp-api/v1/forums";
	protected $registeredRoutes = array(
		"/forum",
		"/topic",
		"/reply",
		"/topic-tags",
		"/stats"
	);

	function setUp() {

		parent::setUp();
		global $wp_rest_server;
		$testCommon = new Bbp_API_test_common();
		$testCommon->activateBBPress();
		$testCommon->activateBBPAPI();
		$this->server = $wp_rest_server = new WP_REST_Server;
		do_action( 'rest_api_init' );
	}
	/**
	 * A single example test.
	 */
	function testRouteRegistration() {
		$routes = $this->server->get_routes();
		print_r(array_keys($routes));
		foreach ($this->registeredRoutes as &$route) {
			$this->assertArrayHasKey( $this->prefix . $route, $routes );
		}
	}

	function tearDown() {
		parent::tearDown();
	}
}
