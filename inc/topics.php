<?php
/*
 * /bbp-api/topics
*/
function bbp_api_topics() {
	// Prepared for future use
	$all_topics_data = array();

	if ( empty( $all_topics_data ) ) {
		return null;
	}

	return $all_topics_data;
}

/*
 * /bbp-api/topics/<id>
*/
function bbp_api_topics_one($data) {
	$all_topic_data = array();
	
	$topic_id = bbp_get_topic_id($data['id']);
	if ($topic_id) {
		$all_topic_data['id'] = $topic_id;
		$all_topic_data['title'] = bbp_get_topic_title($topic_id);
		$all_topic_data['reply_count'] = bbp_get_topic_reply_count($topic_id);
		$all_topic_data['permalink'] = bbp_get_topic_permalink($topic_id);
		$all_topic_data['tags'] = bbp_get_topic_tag_list($topic_id, array ('before' => ''));
	}
	if ( empty( $all_topic_data ) ) {
		return null;
	}

	return $all_topic_data;
}
