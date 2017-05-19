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
    $request = new WP_REST_Request( "POST",
      $this->prefix . $this->registeredRoute );
    $request->set_body_params( array(
      "title" => $this->topic_data["title"],
      "content" => $this->topic_data["content"],
      "forum_id" => $this->newForum,
      "email" => $this->user_email,
    ));
    $response = $this->server->dispatch( $request );
    $this->assertEquals( 200, $response->status );
    //check content tied to new topic
    $replyRequest = new WP_REST_Request( "GET",
      $this->prefix . "/reply/" . $response->data );
    $replyResponse = $this->server->dispatch( $replyRequest );
    $this->assertEquals( 200, $response->status );
    $this->assertEquals( $this->topic_data["title"],
      $replyResponse->data["title"] );
    $this->assertEquals( $this->topic_data["content"],
      $replyResponse->data["content"] );
  }

  function testPostTopicNoTitle() {
    //post new topic
    $request = new WP_REST_Request( "POST",
      $this->prefix . $this->registeredRoute );
    $request->set_body_params( array(
      "content" => $this->topic_data["content"],
      "forum_id" => $this->newForum,
      "email" => $this->user_email,
    ));
    $response = $this->server->dispatch( $request );
    $this->assertNotEquals( 200, $response->status );
    $this->assertEquals( "rest_missing_callback_param",
      $response->data["code"] );
    $this->assertContains( "title", $response->data["data"]["params"] );
  }

  function testPostTopicNoContent() {
    //post new topic
    $request = new WP_REST_Request( "POST",
      $this->prefix . $this->registeredRoute );
    $request->set_body_params( array(
      "title" => $this->topic_data["title"],
      "forum_id" => $this->newForum,
      "email" => $this->user_email,
    ));
    $response = $this->server->dispatch( $request );
    $this->assertNotEquals( 200, $response->status );
    $this->assertEquals( "rest_missing_callback_param",
      $response->data["code"] );
    $this->assertContains( "content", $response->data["data"]["params"] );
  }

  function testPostTopicNoForumID() {
    //post new topic
    $request = new WP_REST_Request( "POST",
      $this->prefix . $this->registeredRoute );
    $request->set_body_params( array(
      "title" => $this->topic_data["title"],
      "content" => $this->topic_data["content"],
      "email" => $this->user_email,
    ));
    $response = $this->server->dispatch( $request );
    $this->assertNotEquals(200, $response->status);
    $this->assertEquals( "rest_missing_callback_param",
      $response->data["code"] );
    $this->assertContains( "forum_id", $response->data["data"]["params"] );
  }

  function testPostTopicNoEmail() {
    //post new topic
    $request = new WP_REST_Request( "POST",
      $this->prefix . $this->registeredRoute );
    $request->set_body_params( array(
      "title" => $this->topic_data["title"],
      "content" => $this->topic_data["content"],
      "forum_id" => $this->newForum,
    ));
    $response = $this->server->dispatch( $request );
    $this->assertNotEquals( 200, $response->status );
    $this->assertEquals( "rest_missing_callback_param",
      $response->data["code"] );
    $this->assertContains( "email", $response->data["data"]["params"] );
  }

  function testPostTopicBadInput() {
    //POST with bad email
    $badEmailRequest = new WP_REST_Request( "POST",
      $this->prefix . $this->registeredRoute );
    $badEmailRequest->set_body_params( array(
      "title" => $this->topic_data["title"],
      "content" => $this->topic_data["content"],
      "forum_id" => $this->newForum,
      "email" => 1234,
    ));
    $badEmailResponse = $this->server->dispatch( $badEmailRequest );
    $this->assertEquals( 400, $badEmailResponse->status );
    $this->assertEquals( "rest_invalid_param", $badEmailResponse->data["code"] );

    //POST with bad title
    $badTitleRequest = new WP_REST_Request( "POST",
      $this->prefix . $this->registeredRoute );
    $badTitleRequest->set_body_params( array(
      "title" => 1234,
      "content" => $this->topic_data["content"],
      "forum_id" => $this->newForum,
      "email" => $this->user_email,
    ));
    $badTitleResponse = $this->server->dispatch( $badTitleRequest );
    $this->assertEquals( 400, $badTitleResponse->status );
    $this->assertEquals( "rest_invalid_param", $badTitleResponse->data["code"] );

    //POST with bad content
    $badContentRequest = new WP_REST_Request( "POST",
      $this->prefix . $this->registeredRoute );
    $badContentRequest->set_body_params( array(
      "title" => $this->topic_data["title"],
      "content" => 1234,
      "forum_id" => $this->newForum,
      "email" => $this->user_email,
    ));
    $badContentResponse = $this->server->dispatch( $badContentRequest );
    $this->assertEquals( 400, $badContentResponse->status );
    $this->assertEquals( "rest_invalid_param", $badContentResponse->data["code"] );

    //POST with bad forum ID
    $badForumIdRequest = new WP_REST_Request( "POST",
      $this->prefix . $this->registeredRoute );
    $badForumIdRequest->set_body_params( array(
      "title" => $this->topic_data["title"],
      "content" => $this->topic_data["content"],
      "forum_id" => "stringvaluegoeshere",
      "email" => $this->user_email,
    ));
    $badForumIdResponse = $this->server->dispatch( $badForumIdRequest );
    $this->assertEquals( 400, $badForumIdResponse->status );
    $this->assertEquals( "rest_invalid_param", $badForumIdResponse->data["code"] );
  }
	function tearDown() {
		parent::tearDown();
	}
}
