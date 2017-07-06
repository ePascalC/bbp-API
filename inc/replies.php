<?php
/*
 * /bbp-api/replies
*/
function bbp_api_replies() {
	// Prepared for future use
	$all_replies_data = array();
	$all_replies_data['text'] = 'No data to deliver yet using this route. To see replies, refer to documentation and use /topics/ID.';

	if ( empty( $all_replies_data ) ) {
		return null;
	}
	return $all_replies_data;
}
/*
 * /bbp-api/replies/<id>
*/
function bbp_api_replies_one( $data ) {
	$all_reply_data = array();
	$reply_id = bbp_get_reply_id( $data['id'] );
	if ( !bbp_is_reply( $reply_id ) ) {
		return new WP_Error( 'error', 'Parameter value ' . $data['id'] . ' is not an ID of a reply', array( 'status' => 404 ) );
	} else {
		$all_reply_data['id'] = $reply_id;
		$all_reply_data['title'] = bbp_get_reply_title( $reply_id );
		$all_reply_data['permalink'] = bbp_get_reply_permalink( $reply_id );
		$all_reply_data['topic_id'] = bbp_get_reply_topic_id( $all_reply_data['id'] );
		$all_reply_data['topic_title'] = bbp_get_topic_title( $all_reply_data['topic_id'] );
		$all_reply_data['forum_id'] = bbp_get_topic_forum_id( $all_reply_data['topic_id'] );
		$all_topic_data['forum_title'] = bbp_get_topic_forum_title( $all_reply_data['topic_id'] ); 
		$all_reply_data['tags'] = bbp_get_topic_tag_list( $all_reply_data['topic_id'], array ('before' => '') );
		$all_reply_data['content'] = bbp_get_reply_content( $reply_id );

	}
	if ( empty( $all_reply_data ) ) {
		return null;
	}
	return $all_reply_data;
}
/*
 * Setting up POST for new replies via API.
 * Example code in BBPress here: includes/core/update.php
 * Function code here: /includes/replies/functions.php
 * array data: submitted information from POST requested
 * required args - content, email
 * return string reply_id: id number for accepted post
*/
function bbp_api_replytoreply_post( $data ) {
	//required fields in POST data
	$reply_id = bbp_get_reply_id( $data['id'] );
	if ( !bbp_is_reply( $reply_id ) ) {
		return new WP_Error( 'error', 'Parameter value ' . $data['id'] . ' is not an ID of a reply', array( 'status' => 404 ) );
	} else {
		$topic_id = bbp_get_reply_topic_id( $reply_id );
		$title = bbp_get_reply_title( $reply_id );
		$forum_id = bbp_get_topic_forum_id( $topic_id );
		$content = $data['content'];
		$email = $data['email'];
		$myuser = get_user_by( "email", $email );
		$reply_id = bbp_insert_reply(
			array(
				'post_parent'  => $reply_id,
				'post_title'   => $title,
				'post_content' => $content,
				'post_author'  => $myuser->ID,
			),
			array(
				'forum_id'     => $forum_id,
				'topic_id'     => $topic_id,
			)
		);
		return $reply_id;
	}
}
