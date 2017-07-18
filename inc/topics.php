<?php
/*
 * /bbp-api/topics
*/
function bbp_api_topics() {
	$all_topic_data = array();
	$bbp = bbpress();
	
	$per_page = !isset($_GET['per_page']) ? 20 : (int)$_GET['per_page'];
	if ($per_page > 100) $per_page = 100;
	$page = !isset($_GET['page']) ? 1 : (int)$_GET['page'];	
	
	$i = 0;
	$all_topic_data['total_topics'] = 0;
	$all_topic_data['total_pages'] = 0;
	$all_topic_data['current_page'] = 0;
	$all_topic_data['next_page'] = 0;
	$all_topic_data['next_page_url'] = '';
	$all_topic_data['prev_page'] = 0;
	$all_topic_data['prev_page_url'] = '';

	if ( bbp_has_topics ( array( 'orderby' => 'date', 'order' => 'DESC', 'posts_per_page' => $per_page, 'paged' => $page ) ) ) {
		$all_topic_data['current_page'] = $page;
		$all_topic_data['total_topics'] = (int)$bbp->topic_query->found_posts;
		$all_topic_data['total_pages'] = ceil($all_topic_data['total_topics'] / $per_page);

		$root_url = get_site_url() . '/wp-json/bbp-api/v1/topics/';
		
		if ( ( $per_page * $page ) >= $all_topic_data['total_topics'] ) {
			// This is the last page
		} else {
			// Not the last page
			$all_topic_data['next_page'] = $page + 1;
			$all_topic_data['next_page_url'] = $root_url . '?page=' . $all_topic_data['next_page'] . '&per_page=' . $per_page;
		}
		if ( $page > 1 ) {
			// Other page than first
			$all_topic_data['prev_page'] = $page - 1;
			$all_topic_data['prev_page_url'] = $root_url . '?page=' . $all_topic_data['prev_page'] . '&per_page=' . $per_page;
		}
		while ( bbp_topics() ) : bbp_the_topic();
			$topic_id = bbp_get_topic_id();
			$all_topic_data['topics'][$i]['id'] = $topic_id;
			$all_topic_data['topics'][$i]['title'] = bbp_get_topic_title( $topic_id );
			$all_topic_data['topics'][$i]['reply_count'] = bbp_get_topic_reply_count( $topic_id );
			$all_topic_data['topics'][$i]['permalink'] = bbp_get_topic_permalink( $topic_id );
			$all_topic_data['topics'][$i]['author_name'] = bbp_get_topic_author_display_name( $topic_id );
			$all_topic_data['topics'][$i]['author_avatar'] = bbp_get_topic_author_avatar( $topic_id );
			$all_topic_data['topics'][$i]['post_date'] = bbp_get_topic_post_date( $topic_id );
			$all_topic_data['topics'][$i]['forum_id'] = bbp_get_topic_forum_id( $topic_id );
			$all_topic_data['topics'][$i]['forum_title'] = bbp_get_forum_title( $all_topic_data['topics'][$i]['forum_id'] );
			$i++;
		endwhile;
	} else {
		return new WP_Error( 'error', 'No latest topics found.', array( 'status' => 404 ) );
	}
	
	return $all_topic_data;
}
/*
 * /bbp-api/topics/<id>
*/
function bbp_api_topics_one( $data ) {
	$all_topic_data = array();
	$bbp = bbpress();

	$topic_id = bbp_get_topic_id( $data['id'] );
	if ( $topic_id == 0 ) {
		return new WP_Error( 'error', 'Parameter value of ID for a topic should not be 0', array( 'status' => 404 ) );
	}
	if ( !bbp_is_topic( $topic_id ) ) {
		return new WP_Error( 'error', 'Parameter value ' . $data['id'] . ' is not an ID of a topic', array( 'status' => 404 ) );
	} else {
		$per_page = !isset($_GET['per_page']) ? 20 : (int)$_GET['per_page'];
		if ($per_page > 100) $per_page = 100;
		$page = !isset($_GET['page']) ? 1 : (int)$_GET['page'];
		$show_reply_content = !isset($_GET['_embed']) ? false : true;

		$all_topic_data['id'] = $topic_id;
		$all_topic_data['title'] = bbp_get_topic_title( $topic_id );
		$all_topic_data['reply_count'] = bbp_get_topic_reply_count( $topic_id );
		$all_topic_data['permalink'] = bbp_get_topic_permalink( $topic_id );
		$all_topic_data['tags'] = bbp_get_topic_tag_list( $topic_id, array('before' => '') );
		$all_topic_data['last_reply'] = bbp_get_topic_last_reply_id( $topic_id );
		$all_topic_data['author_name'] = bbp_get_topic_author_display_name( $topic_id );
		$all_topic_data['author_avatar'] = bbp_get_topic_author_avatar( $topic_id );
		$all_topic_data['post_date'] = bbp_get_topic_post_date( $topic_id );
		$all_topic_data['content'] = bbp_get_topic_content( $topic_id );
		$all_topic_data['forum_id'] = bbp_get_topic_forum_id( $topic_id ); 
		$all_topic_data['forum_title'] = bbp_get_forum_title( $all_topic_data['forum_id'] );
		
		$root_url = get_site_url() . '/wp-json/bbp-api/v1/topics/' . $topic_id;

		if ( ( $per_page * $page ) >= $all_topic_data['reply_count'] ) {
			// This is the last page
			$all_topic_data['next_page'] = 0;
			unset($all_topic_data['next_page_url']);
		} else {
			$all_topic_data['next_page'] = $page + 1;
			$all_topic_data['next_page_url'] =  $root_url . '?page=' . $all_topic_data['next_page'] . '&per_page=' . $per_page;
			if ( $show_reply_content )
				$all_topic_data['next_page_url'] = $all_topic_data['next_page_url'] . '&_embed';
		}
		
		$i = 0;
		$all_topic_data['total_topics'] = 0;
		$all_topic_data['total_pages'] = 0;
		$all_topic_data['current_page'] = 0;
		$all_topic_data['next_page'] = 0;
		$all_topic_data['next_page_url'] = '';
		$all_topic_data['prev_page'] = 0;
		$all_topic_data['prev_page_url'] = '';

		if ( bbp_has_replies ( array( 'orderby' => 'date', 'order' => 'DESC', 'posts_per_page' => $per_page, 'paged' => $page, 'post_parent' => $topic_id ) ) ) {
			$all_topic_data['current_page'] = $page;
			$all_topic_data['total_replies'] = (int)$bbp->reply_query->found_posts;
			$all_topic_data['total_pages'] = ceil($all_topic_data['total_replies'] / $per_page);
		
			if ( ( $per_page * $page ) > $all_topic_data['total_replies'] ) {
				// This is the last page
			} else {
				// Not the last page
				$all_topic_data['next_page'] = $page + 1;
				$all_topic_data['next_page_url'] = $root_url . '?page=' . $all_topic_data['next_page'] . '&per_page=' . $per_page;
			}
			if ( $page > 1 ) {
				// Other page than first
				$all_topic_data['prev_page'] = $page - 1;
				$all_topic_data['prev_page_url'] = $root_url . '?page=' . $all_topic_data['prev_page'] . '&per_page=' . $per_page;
			}
			while ( bbp_replies() ) : bbp_the_reply();
				$reply_id = bbp_get_reply_id();
				if ( $reply_id != $topic_id ) {
					// not sure why in the list the topic is seen as a reply too, so this 'if' should remove it
					$all_topic_data['replies'][$i]['id'] = $reply_id;
					$all_topic_data['replies'][$i]['title'] = bbp_get_reply_title( $reply_id );
					$all_topic_data['replies'][$i]['permalink'] = bbp_get_reply_permalink( $reply_id );
					$all_topic_data['replies'][$i]['author_name'] = bbp_get_reply_author_display_name( $reply_id );
					$all_topic_data['replies'][$i]['author_avatar'] = bbp_get_reply_author_avatar( $reply_id );
					$all_topic_data['replies'][$i]['post_date'] = bbp_get_reply_post_date( $reply_id );
					if ( $show_reply_content ) $all_topic_data['replies'][$i]['content'] = bbp_get_reply_content( $reply_id );
					$i++;
				}
			endwhile;
		} else {
			// No replies
			$all_topic_data['total_replies'] = 0;
			$all_topic_data['total_pages'] = 0;
			$all_topic_data['next_page'] = 0;
		}
	}
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
function bbp_api_newtopic_post( $data ) {
	$return = array();
	//required fields in POST data
	$forum_id = bbp_get_forum_id( $data['id'] );
	if ( $forum_id == 0 ) {
		return new WP_Error( 'error', 'Parameter value of ID for a forum should not be 0', array( 'status' => 404 ) );
	}
	if (!bbp_is_forum($forum_id)) {
		return new WP_Error( 'error', 'Parameter value ' . $data['id'] . ' is not an ID of a forum', array( 'status' => 404 ) );
	}
	if (bbp_is_forum_category($forum_id)) {
		return new WP_Error( 'error', 'Forum with ID ' . $data['id'] . ' is a category, so no topics allowed', array( 'status' => 404 ) );
	}
	
	$content = $data['content'];
	$title = $data['title'];
	$email = $data['email'];
	$myuser = get_user_by( "email", $data['email'] );
	$author_id = $myuser->ID;
	$new_topic_id = bbp_insert_topic(
		array(
			'post_parent'  => $forum_id,
			'post_title'   => $title,
			'post_content' => $content,
			'post_author'  => $author_id,
		),
		array(
			'forum_id'     => $forum_id,
		)
	);
	
	$return['id'] = $new_topic_id;
	$return['forum_id'] = $forum_id;
	$return['author_id'] = $author_id;
	
	return $return;
}

/*
 * Setting up POST for new reply to a topic via API.
 * array data: submitted information from POST requested
 * required args - content, email
 * return string reply_id: id number for accepted post
*/
function bbp_api_replytotopic_post( $data ) {
	$return = array();

	//required fields in POST data
	$topic_id = bbp_get_topic_id( $data['id'] );
	if ( !bbp_is_topic( $topic_id ) ) {
		return new WP_Error( 'error', 'Parameter value ' . $data['id'] . ' is not an ID of a topic', array( 'status' => 404 ) );
	}
	
	$forum_id = bbp_get_topic_forum_id( $topic_id );
	$title = 'RE: ' . bbp_get_topic_title( $topic_id );
	$content = $data['content'];
	$email = $data['email'];
	$myuser = get_user_by( "email", $email );
	$author_id = $myuser->ID;
	$new_reply_id = bbp_insert_reply(
		array(
			'post_parent'  => $topic_id,
			'post_title'   => $title,
			'post_content' => $content,
			'post_author'  => $author_id,
		),
		array(
			'forum_id'     => $forum_id,
			'topic_id'     => $topic_id,
		)
	);
	
	$return['id'] = $new_reply_id;
	$return['topic_id'] = $topic_id;
	$return['forum_id'] = $forum_id;
	$return['author_id'] = $author_id;
	
	return $return;
}