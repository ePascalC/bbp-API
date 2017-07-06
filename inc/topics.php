<?php

/*

 * /bbp-api/topics

*/

function bbp_api_topics() {

	// Prepared for future use

	$all_topic_data = array();

	$all_topic_data['text'] = 'No data to deliver. To see topics, refer to documentation and use /forums/ID.';

	

	if ( empty( $all_topic_data ) ) {

		return null;

	}

	return $all_topic_data;

}

/*

 * /bbp-api/topics/<id>

*/

function bbp_api_topics_one( $data ) {

	$all_topic_data = array();

	$topic_id = bbp_get_topic_id( $data['id'] );

	if ( $topic_id ) {

		$per_page = !isset($_GET['per_page']) ? 20 : $_GET['per_page'];

		if ($per_page > 100) $per_page = 100;

		$page = !isset($_GET['page']) ? 1 : $_GET['page'];

		// $user_id = $_GET['user_id'];

		// $all_topic_data['subscribed'] = bbp_is_user_subscribed_to_topic (  $user_id,  $topic_id);

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



		if ( ( $per_page * $page ) > $all_topic_data['reply_count'] ) {

			// This is the last page

			$all_topic_data['next_page'] = 0;

		} else {

			$all_topic_data['next_page'] = $page + 1;

			$all_topic_data['next_page_url'] = get_site_url() . '/wp-json/bbp-api/v1/topics/' . $forum_id . '?page=' . $all_topic_data['next_page'] . '&per_page=' . $per_page;

		}

		

		$i = 0;

		if ( bbp_has_replies ( array( 'orderby' => 'date', 'order' => 'ASC', 'posts_per_page' => $per_page, 'paged' => $page, 'post_parent' => $topic_id ) ) );

		while ( bbp_replies() ) : bbp_the_reply();

			$reply_id = bbp_get_reply_id();

			if ($reply_id != $topic_id) {

				// not sure why in the list the topic is seen as a reply too, so this 'if' should remove it

				$all_topic_data['replies'][$i]['id'] = $reply_id;

				$all_topic_data['replies'][$i]['title'] = bbp_get_reply_title( $reply_id );

				$all_topic_data['replies'][$i]['permalink'] = bbp_get_reply_permalink( $reply_id );

				$all_topic_data['replies'][$i]['author_name'] = bbp_get_reply_author_display_name( $reply_id );

				$all_topic_data['replies'][$i]['author_avatar'] = bbp_get_reply_author_avatar( $reply_id );

				$all_topic_data['replies'][$i]['post_date'] = bbp_get_reply_post_date( $reply_id );

				$all_topic_data['replies'][$i]['content'] = bbp_get_reply_content( $reply_id );

				$i++;

			}

		endwhile;



		}

	if ( empty( $all_topic_data ) ) {

		return null;

	}

	// Set Page count headers
	$total_pages = ceil($all_topic_data['reply_count'] /$per_page);
	header("X-WP-Total: " . $all_topic_data['reply_count'] );
	header("X-WP-TotalPages: " . $total_pages);

	return new WP_REST_Response($all_topic_data, 200);  

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

			'post_author'  => $myuser->ID,

		),

		array(

			'forum_id'     => $all_topic_data['forum_id'],

		)

	);

	$reply = [];

	$reply['id'] = $reply_id;

	$reply['title'] = bbp_get_reply_title( $reply_id );

	$reply['permalink'] = bbp_get_reply_permalink( $reply_id );

	$reply['author_name'] = bbp_get_reply_author_display_name( $reply_id );

	$reply['author_avatar'] = bbp_get_reply_author_avatar( $reply_id );

	$reply['post_date'] = bbp_get_reply_post_date( $reply_id );

	$reply['content'] = bbp_get_reply_content( $reply_id );

	return new WP_REST_Response($reply, 200);  

	return $reply_id;

}