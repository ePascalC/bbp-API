<?php
/*
 * Data gathering for requested or involved reply
*/
function bbp_api_replies_info($reply_id) {
	$all_reply_data = array();
	if ($reply_id) {
		$all_reply_data['id'] = $reply_id;
		$all_reply_data['title'] = bbp_get_reply_title($reply_id);
		$all_reply_data['permalink'] = bbp_get_reply_permalink($reply_id);
		$all_reply_data['topic_id'] = bbp_get_reply_topic_id($all_reply_data['id']);
		$all_reply_data['forum_id'] = bbp_get_topic_forum_id($all_reply_data['topic_id']);
		$all_reply_data['tags'] = bbp_get_topic_tag_list($all_reply_data['topic_id'], array ('before' => ''));

	}
	if ( empty( $all_reply_data ) ) {
		return null;
	}
	return $all_reply_data;
}

/*
 * /bbp-api/replies
*/
function bbp_api_replies() {
	// Prepared for future use
	$all_replies_data = array();

	if ( empty( $all_replies_data ) ) {
		return null;
	}

	return $all_replies_data;
}

/*
 * /bbp-api/replies/<id>
*/
function bbp_api_replies_one($data) {
	$all_reply_data = bbp_api_replies_info($data['id']);
	return $all_reply_data;
}

/*
 * Setting up POST for new replies via API.
 * Example code in BBPress here: includes/core/update.php
 * requires content and email fields in the POST data to work.
 * array data: submitted information from POST requested
 * return string reply_id: id number for accepted post
*/
function bbp_api_replies_post($data) {
	$all_reply_data = bbp_api_replies_info($data['id']);
	$all_reply_data['content'] = $data['content'];
	$myuser = get_user_by("email", $data['email']);
	$reply_id = bbp_insert_reply(
    array(
      'post_parent'  => $all_reply_data['topic_id'],
      'post_title'   => $all_reply_data['title'],
      'post_content' => $all_reply_data['content'],
			'post_author' => $myuser->id,
    ),
    array(
      'forum_id'     => $all_reply_data['forum_id'],
      'topic_id'     => $all_reply_data['topic_id'],
    )
  );
	return $reply_id;
}
