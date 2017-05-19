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
    //post new topic
    $request = new WP_REST_Request("POST", $this->prefix . $this->registeredRoute);
    $request->set_body_params( array(
      "title" => $this->topic_data["title"],
      "content" => $this->topic_data["content"],
      "forum_id" => $this->newForum,
      "email" => $this->user_email,
    ));
    $response = $this->server->dispatch( $request );
    $this->assertEquals(200, $response->status);
    //check content tied to new topic
    $replyRequest = new WP_REST_Request("GET", $this->prefix . "/reply/" . $response->data);
    $replyResponse = $this->server->dispatch( $replyRequest );
    $this->assertEquals(200, $response->status);
    $this->assertEquals($this->topic_data["title"], $replyResponse->data["title"]);
    $this->assertEquals($this->topic_data["content"], $replyResponse->data["content"]);
  }

  function testPostTopicNoTitle() {
    //post new topic
    $request = new WP_REST_Request("POST", $this->prefix . $this->registeredRoute);
    $request->set_body_params( array(
      "content" => $this->topic_data["content"],
      "forum_id" => $this->newForum,
      "email" => $this->user_email,
    ));
    $response = $this->server->dispatch( $request );
    $this->assertNotEquals(200, $response->status);
    $this->assertEquals("rest_missing_callback_param", $response->data["code"]);
    $this->assertContains("title", $response->data["data"]["params"]);
  }

  function testPostTopicNoContent() {
    //post new topic
    $request = new WP_REST_Request("POST", $this->prefix . $this->registeredRoute);
    $request->set_body_params( array(
      "title" => $this->topic_data["title"],
      "forum_id" => $this->newForum,
      "email" => $this->user_email,
    ));
    $response = $this->server->dispatch( $request );
    $this->assertNotEquals(200, $response->status);
    $this->assertEquals("rest_missing_callback_param", $response->data["code"]);
    $this->assertContains("content", $response->data["data"]["params"]);
  }

  function testPostTopicNoForumID() {
    //post new topic
    $request = new WP_REST_Request("POST", $this->prefix . $this->registeredRoute);
    $request->set_body_params( array(
      "title" => $this->topic_data["title"],
      "content" => $this->topic_data["content"],
      "email" => $this->user_email,
    ));
    $response = $this->server->dispatch( $request );
    $this->assertNotEquals(200, $response->status);
    $this->assertEquals("rest_missing_callback_param", $response->data["code"]);
    $this->assertContains("forum_id", $response->data["data"]["params"]);
  }

  function testPostTopicNoEmail() {
    //post new topic
    $request = new WP_REST_Request("POST", $this->prefix . $this->registeredRoute);
    $request->set_body_params( array(
      "title" => $this->topic_data["title"],
      "content" => $this->topic_data["content"],
      "forum_id" => $this->newForum,
    ));
    $response = $this->server->dispatch( $request );
    $this->assertNotEquals(200, $response->status);
    $this->assertEquals("rest_missing_callback_param", $response->data["code"]);
    $this->assertContains("email", $response->data["data"]["params"]);
  }

	function tearDown() {
		parent::tearDown();
	}
}
