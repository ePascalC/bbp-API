<?php
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
	$all_reply_data = array();
	
	$reply_id = bbp_get_reply_id($data['id']);
	if ($reply_id) {
		$all_reply_data['id'] = $reply_id;
		$all_reply_data['title'] = bbp_get_reply_title($reply_id);
		$all_reply_data['permalink'] = bbp_get_reply_permalink($topic_id);
	}
	if ( empty( $all_reply_data ) ) {
		return null;
	}

	return $all_reply_data;
}
