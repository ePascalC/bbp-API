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
class GetTopic extends WP_UnitTestCase {
	/**
	* setting up the WP REST Server
	*/
	protected $prefix = "/bbp-api/v1/forums";
	protected $registeredRoutes = array(
		"/topic",
	);
  protected $topic_data = array(
    "title" => "Test Topic.",
    "content" => "Initial Content.",
  );

	function setUp() {

		parent::setUp();
		global $wp_rest_server;
		$testCommon = new Bbp_API_test_common();
		$testCommon->activateBBPress();
		$testCommon->activateBBPAPI();
		$this->server = $wp_rest_server = new WP_REST_Server;
		do_action( 'rest_api_init' );
    $this->newForum = $testCommon->createBBPForum();
    $this->newTopic = $testCommon->createBBPTopic($this->newForum, $this->topic_data);
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

  function testGetTopic() {
    $routes = $this->server->get_routes();
    foreach ($this->registeredRoutes as &$route) {
      $request = new WP_REST_Request("GET", $this->prefix . $route . "/" . $this->newTopic);
      $response = $this->server->dispatch( $request );
      $this->assertEquals(200, $response->status);
      $this->assertEquals($this->topic_data["title"], $response->data["title"]);
    }
  }

	function tearDown() {
		parent::tearDown();
	}
}
