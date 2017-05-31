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
class PostReply extends WP_UnitTestCase {
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
    $this->newTopic = $testCommon->createBBPTopic($this->newForum,
      $this->topic_data);
	}
	/**
	 * A single example test.
	 */
	function testPostReply() {
    //get initial reply on test thread
    $topicRequest = new WP_REST_Request( "GET",
      $this->prefix . "/topic/" . $this->newTopic );
    $topicResponse = $this->server->dispatch( $topicRequest );
    //POST a reply to the returned reply id
    $replyRequest = new WP_REST_Request( "POST",
      $this->prefix . $this->registeredRoute . "/" . $topicResponse->data["last_reply"] );
    $replyRequest->set_body_params( array(
      "content" => $this->reply_data["content"],
      "email" => $this->user_email,
    ));
    $replyResponse = $this->server->dispatch( $replyRequest );
    $this->assertEquals( 200, $replyResponse->status );
    //retrieve the POSTed content.
    $verifyRequest = new WP_REST_Request( "GET",
      $this->prefix . $this->registeredRoute . "/" . $replyResponse->data );
    $verifyResponse = $this->server->dispatch( $verifyRequest );
    $this->assertEquals( $this->reply_data["content"],
      $verifyResponse->data["content"] );
	}

  function testPostReplyNoContent() {
    //get initial reply on test thread
    $topicRequest = new WP_REST_Request( "GET",
      $this->prefix . "/topic/" . $this->newTopic );
    $topicResponse = $this->server->dispatch( $topicRequest );
    //POST a reply to the returned reply id
    $replyRequest = new WP_REST_Request( "POST",
      $this->prefix . $this->registeredRoute . "/" . $topicResponse->data["last_reply"] );
    $replyRequest->set_body_params( array(
      "email" => $this->user_email,
    ));
    $replyResponse = $this->server->dispatch( $replyRequest );
    $this->assertNotEquals( 200, $replyResponse->status );
    $this->assertEquals( "rest_missing_callback_param", $replyResponse->data["code"] );
    $this->assertContains( "content", $replyResponse->data["data"]["params"] );
  }

  function testPostReplyNoEmail() {
    //get initial reply on test thread
    $topicRequest = new WP_REST_Request( "GET",
      $this->prefix . "/topic/" . $this->newTopic );
    $topicResponse = $this->server->dispatch( $topicRequest );
    //POST a reply to the returned reply id
    $replyRequest = new WP_REST_Request( "POST",
      $this->prefix . $this->registeredRoute . "/" . $topicResponse->data["last_reply"] );
    $replyRequest->set_body_params( array(
      "content" => $this->reply_data["content"],
    ));
    $replyResponse = $this->server->dispatch( $replyRequest );
    $this->assertNotEquals( 200, $replyResponse->status );
    $this->assertEquals( "rest_missing_callback_param", $replyResponse->data["code"] );
    $this->assertContains( "email", $replyResponse->data["data"]["params"] );
  }

  function testPostReplyBadInput() {
    //get initial reply on test thread
    $topicRequest = new WP_REST_Request( "GET",
      $this->prefix . "/topic/" . $this->newTopic );
    $topicResponse = $this->server->dispatch( $topicRequest );
    //POST with bad content
    $badContentRequest = new WP_REST_Request( "POST",
      $this->prefix . $this->registeredRoute . "/" . $topicResponse->data["last_reply"] );
    $badContentRequest->set_body_params( array(
      "content" => 1234,
      "email" => $this->user_email,
    ));
    $badContentResponse = $this->server->dispatch( $badContentRequest );
    $this->assertEquals( 400, $badContentResponse->status );
    $this->assertEquals( "rest_invalid_param", $badContentResponse->data["code"] );
    //POST with bad email
    $badEmailRequest = new WP_REST_Request( "POST",
      $this->prefix . $this->registeredRoute . "/" . $topicResponse->data["last_reply"] );
    $badEmailRequest->set_body_params( array(
      "content" => $this->reply_data["content"],
      "email" => 1234,
    ));
    $badEmailResponse = $this->server->dispatch( $badEmailRequest );
    $this->assertEquals( 400, $badEmailResponse->status );
    $this->assertEquals( "rest_invalid_param", $badEmailResponse->data["code"] );
	}

	function tearDown() {
		parent::tearDown();
	}
}
