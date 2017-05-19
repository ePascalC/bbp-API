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
	protected $registeredRoute = "/topic";
  protected $topic_data = array(
    "title" => "Posted Test Topic.",
    "content" => "Posted Initial Content.",
  );
  protected $user_email = "admin@example.org";

	function setUp() {

		parent::setUp();
		global $wp_rest_server;
		$testCommon = new Bbp_API_test_common();
		$testCommon->activateBBPress();
		$testCommon->activateBBPAPI();
		$this->server = $wp_rest_server = new WP_REST_Server;
		do_action( 'rest_api_init' );
    $this->newForum = $testCommon->createBBPForum();
	}

  function testPostTopic() {
    $request = new WP_REST_Request("POST", $this->prefix . $this->registeredRoute);
    $request->set_body_params( array(
      "title" => $this->topic_data["title"],
      "content" => $this->topic_data["content"],
      "forum_id" => $this->newForum,
      "email" => $this->user_email,
    ));
    //print_r($request);
    $response = $this->server->dispatch( $request );
    print_r($response);
  }

	function tearDown() {
		parent::tearDown();
	}
}
