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
				if ( $sublist = bbp_forum_get_subforums() ) {
					foreach ( $sublist as $sub_forum ) {
							$all_forums_ids[] = (int)$sub_forum->ID;
					}
				}
		} // while
		$i = 0;
		foreach ( $all_forums_ids as $forum_id ) {
			$all_forums_data[$i]['id'] = $forum_id;
			$all_forums_data[$i]['title'] = bbp_get_forum_title( $forum_id );
			$all_forums_data[$i]['parent'] = bbp_get_forum_parent_id( $forum_id );
			$all_forums_data[$i]['topic_count'] = bbp_get_forum_topic_count( $forum_id );
			$all_forums_data[$i]['reply_count'] = bbp_get_forum_reply_count( $forum_id );
			$all_forums_data[$i]['permalink'] = bbp_get_forum_permalink( $forum_id );
			$all_forums_data[$i]['content'] = bbp_get_forum_content( $forum_id );
			$all_forums_data[$i]['type'] = bbp_get_forum_type( $forum_id );
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
 *
 * per_page and page are following https://developer.wordpress.org/rest-api/using-the-rest-api/pagination/#pagination-parameters
 * including the 100 maximum records
*/
function bbp_api_forums_one( $data ) {
	$all_forum_data = array();
	$forum_id = bbp_get_forum_id( $data['id'] );
	if (!bbp_is_forum($forum_id)) {
		return new WP_Error( 'error', 'Parameter value ' . $data['id'] . ' is not an ID of a forum', array( 'status' => 404 ) );
	} else {
		$per_page = !isset($_GET['per_page']) ? 20 : $_GET['per_page'];
		if ($per_page > 100) $per_page = 100;
		$page = !isset($_GET['page']) ? 1 : $_GET['page'];

		$all_forum_data['id'] = $forum_id;
		$all_forum_data['title'] = bbp_get_forum_title( $forum_id );
		$all_forum_data['parent'] = bbp_get_forum_parent_id( $forum_id );
		$all_forum_data['topic_count'] = bbp_get_forum_topic_count( $forum_id );
		$all_forum_data['reply_count'] = bbp_get_forum_reply_count( $forum_id );
		$all_forum_data['permalink'] = bbp_get_forum_permalink( $forum_id );
		$all_forum_data['content'] = bbp_get_forum_content( $forum_id );
		$all_forum_data['type'] = bbp_get_forum_type( $forum_id );
		$all_forum_data['subforums'] = array();
		$subforums = bbp_forum_query_subforum_ids( $forum_id );
		$i = 0;
		foreach ($subforums as $subforum_id) {
			$all_forum_data['subforums'][$i]['id'] = $subforum_id;
			$all_forum_data['subforums'][$i]['title'] = bbp_get_forum_title( $subforum_id );
			$all_forum_data['subforums'][$i]['topic_count'] = bbp_get_forum_topic_count( $subforum_id );
			$all_forum_data['subforums'][$i]['reply_count'] = bbp_get_forum_reply_count( $subforum_id );
			$all_forum_data['subforums'][$i]['permalink'] = bbp_get_forum_permalink( $subforum_id );
			$all_forum_data['subforums'][$i]['content'] = bbp_get_forum_content( $subforum_id );
			$all_forum_data['subforums'][$i]['type'] = bbp_get_forum_type( $subforum_id );
			$i++;
		}
		
		if ( ( $per_page * $page ) > $all_forum_data['topic_count'] ) {
			// This is the last page
			$all_forum_data['next_page'] = 0;
		} else {
			$all_forum_data['next_page'] = $page + 1;
			$all_forum_data['next_page_url'] = get_site_url() . '/wp-json/bbp-api/v1/forums/' . $forum_id . '?page=' . $all_forum_data['next_page'] . '&per_page=' . $per_page;
		}
		
		$i = 0;
		if ( bbp_has_topics ( array( 'orderby' => 'date', 'order' => 'DESC', 'posts_per_page' => $per_page, 'paged' => $page, 'post_parent' => $forum_id ) ) );
		while ( bbp_topics() ) : bbp_the_topic();
			$topic_id = bbp_get_topic_id();
			$all_forum_data['topics'][$i]['id'] = $topic_id;
			$all_forum_data['topics'][$i]['title'] = bbp_get_topic_title( $topic_id );
			$all_forum_data['topics'][$i]['reply_count'] = bbp_get_topic_reply_count( $topic_id );
			$all_forum_data['topics'][$i]['permalink'] = bbp_get_topic_permalink( $topic_id );
			$all_forum_data['topics'][$i]['author_name'] = bbp_get_topic_author_display_name( $topic_id );
			$all_forum_data['topics'][$i]['author_avatar'] = bbp_get_topic_author_avatar( $topic_id );
			$all_forum_data['topics'][$i]['post_date'] = bbp_get_topic_post_date( $topic_id );
			$i++;
		endwhile;
	}
	if ( empty( $all_forum_data ) ) {
		return null;
	}
	return $all_forum_data;
}