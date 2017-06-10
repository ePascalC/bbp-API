<?php
/*
 * /bbp-api/forums
*/
function bbp_api_forums() {
	$all_forums_data = $all_forums_ids = array();
	if ( bbp_has_forums() ) {
		// Get root list of forums
		while ( bbp_forums() ) {
			bbp_the_forum();
			$forum_id = bbp_get_forum_id();
			$all_forums_ids[] = $forum_id;
				if ($sublist = bbp_forum_get_subforums()) {
					foreach ( $sublist as $sub_forum ) {
							$all_forums_ids[] = (int)$sub_forum->ID;
					}
				}
		} // while

		$i = 0;
		foreach ($all_forums_ids as $forum_id) {
			$all_forums_data[$i]['id'] = $forum_id;
			$all_forums_data[$i]['title'] = bbp_get_forum_title($forum_id);
			$all_forums_data[$i]['parent'] = bbp_get_forum_parent_id($forum_id);
			$all_forums_data[$i]['topic_count'] = bbp_get_forum_topic_count($forum_id);
			$all_forums_data[$i]['reply_count'] = bbp_get_forum_reply_count($forum_id);
			$all_forums_data[$i]['permalink'] = bbp_get_forum_permalink($forum_id);
			$all_forums_data[$i]['content'] = bbp_get_forum_content($forum_id);
			$all_forums_data[$i]['type'] = bbp_get_forum_type($forum_id);
			$i++;
		}

	} // if()

	if ( empty( $all_forums_data ) ) {
		return null;
	}

	return $all_forums_data;
}

/*
 * /bbp-api/forums/<id>
*/
function bbp_api_forums_one($data) {
	$all_forum_data = array();
	
	$forum_id = bbp_get_forum_id($data['id']);
	if ($forum_id) {
		$all_forum_data['id'] = $forum_id;
		$all_forum_data['title'] = bbp_get_forum_title($forum_id);
		$all_forum_data['parent'] = bbp_get_forum_parent_id($forum_id);
		$all_forum_data['topic_count'] = bbp_get_forum_topic_count($forum_id);
		$all_forum_data['reply_count'] = bbp_get_forum_reply_count($forum_id);
		$all_forum_data['permalink'] = bbp_get_forum_permalink($forum_id);
		$all_forum_data['content'] = bbp_get_forum_content($forum_id);
		$all_forum_data['type'] = bbp_get_forum_type($forum_id);

		$all_forum_data['subforums'] = array();
		$subforums = bbp_forum_query_subforum_ids($forum_id);
		$i = 0;
		foreach ($subforums as $subforum_id) {
			$all_forum_data['subforums'][$i]['id'] = $subforum_id;
			$all_forum_data['subforums'][$i]['title'] = bbp_get_forum_title($subforum_id);
			$all_forum_data['subforums'][$i]['topic_count'] = bbp_get_forum_topic_count($subforum_id);
			$all_forum_data['subforums'][$i]['reply_count'] = bbp_get_forum_reply_count($subforum_id);
			$all_forum_data['subforums'][$i]['permalink'] = bbp_get_forum_permalink($subforum_id);
			$all_forum_data['subforums'][$i]['type'] = bbp_get_forum_type($subforum_id);
			$i++;
		}
		
		$i = 0;
		if ( bbp_has_topics ( array( 'orderby' => 'date', 'order' => 'DESC', 'posts_per_page' => 20, 'post_parent' => $forum_id ) ) );
		while ( bbp_topics() ) : bbp_the_topic();
			$topic_id = bbp_get_topic_id();
			$all_forum_data['topics'][$i]['id'] = $topic_id;
			$all_forum_data['topics'][$i]['title'] = bbp_get_topic_title($topic_id);
			$all_forum_data['topics'][$i]['reply_count'] = bbp_get_topic_reply_count($topic_id);
			$all_forum_data['topics'][$i]['permalink'] = bbp_get_topic_permalink($topic_id);
			$i++;
		endwhile;
	}
	
	if ( empty( $all_forum_data ) ) {
		return null;
	}

	return $all_forum_data;
}
