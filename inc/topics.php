<?php
/*
 * Data gathering for requested or involved topic
*/
function bbp_api_topics_info( $topic_id, $scope="public" ) {
	$all_topic_data = array();
	if ( $topic_id ) {
		$all_topic_data['id'] = $topic_id;
		$all_topic_data['title'] = bbp_get_topic_title( $topic_id );
		$all_topic_data['reply_count'] = bbp_get_topic_reply_count( $topic_id );
		$all_topic_data['permalink'] = bbp_get_topic_permalink( $topic_id );
		$all_topic_data['tags'] = bbp_get_topic_tag_list( $topic_id,
			array ('before' => '') );
		$all_topic_data['last_reply'] = bbp_get_topic_last_reply_id( $topic_id );
	}
	if ( empty( $all_topic_data ) ) {
		return null;
	}
	return $all_topic_data;
}
/*
 * /bbp-api/topics
*/
function bbp_api_topics() {
	// Prepared for future use
	$all_topic_data = array();
	if ( empty( $all_topic_data ) ) {
		return null;
	}
	return $all_topic_data;
}
/*
 * /bbp-api/topics/<id>
*/
function bbp_api_topics_one( $data ) {
	$all_topic_data = bbp_api_topics_info( $data['id'] );
	return $all_topic_data;
}
/*
 * Setting up POST for new topics via API.
 * Example code in BBPress here: includes/core/update.php
 * Function code here: /includes/topics/functions.php
 * array data: submitted information from POST requested
 * required args - content, title, forum_id, email
 * return string reply_id: id number for accepted post
*/
function bbp_api_topics_post( $data ) {
  //required fields in POST data
	$all_topic_data = bbp_api_topics_info( $data['id'] );
	$all_topic_data['content'] = $data['content'];
	$all_topic_data['title'] = $data['title'];
	$all_topic_data['forum_id'] = $data['forum_id'];
	$all_topic_data['email'] = $data['email'];
	$myuser = get_user_by( "email", $data['email'] );
	$reply_id = bbp_insert_topic(
    array(
			'post_parent'  => $all_topic_data['forum_id'],
      'post_title'   => $all_topic_data['title'],
      'post_content' => $all_topic_data['content'],
			'post_author' => $myuser->ID,
    ),
    array(
      'forum_id'     => $all_topic_data['forum_id'],
    )
  );
	return $reply_id;
}
