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
class GetReply extends WP_UnitTestCase {
	/**
	* setting up the WP REST Server
	*/
	protected $prefix = "/bbp-api/v1/forums";
	protected $registeredRoute = "/reply";

  protected $topic_data = array(
    "title" => "Test Topic.",
    "content" => "Initial Content.",
  );

  protected $reply_data = array(
    "title" => "Test reply.",
    "content" => "Second reply in the thread.",
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
    $this->newTopic = $testCommon->createBBPTopic( $this->newForum,
      $this->topic_data);
    $this->newReply = $testCommon->createBBPReply( $this->newForum,
      $this->newTopic,
      $this->reply_data );
	}
	/**
	 * A single example test.
	 */
	function testRouteRegistration() {
		$routes = $this->server->get_routes();
		$this->assertArrayHasKey( $this->prefix . $this->registeredRoute, $routes );
	}

  function testGetReply() {
    $replyRequest = new WP_REST_Request( "GET",
      $this->prefix . $this->registeredRoute . "/" . $this->newReply );
    $replyResponse = $this->server->dispatch( $replyRequest );
    $this->assertEquals( 200, $replyResponse->status );
    $this->assertEquals( $this->reply_data["title"],
      $replyResponse->data["title"] );
    $this->assertEquals( $this->reply_data["content"],
      $replyResponse->data["content"] );
  }

	function tearDown() {
		parent::tearDown();
	}
}
