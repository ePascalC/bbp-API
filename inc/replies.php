<?php

/*

 * Data gathering for requested or involved reply

*/

function bbp_api_replies_info( $reply_id ) {

	$all_reply_data = array();

	if ($reply_id) {

		$all_reply_data['id'] = $reply_id;

		$all_reply_data['title'] = bbp_get_reply_title( $reply_id );

		$all_reply_data['permalink'] = bbp_get_reply_permalink( $reply_id );

		$all_reply_data['topic_id'] = bbp_get_reply_topic_id( $all_reply_data['id'] );

		$all_reply_data['forum_id'] = bbp_get_topic_forum_id( $all_reply_data['topic_id'] );

		$all_reply_data['tags'] = bbp_get_topic_tag_list( $all_reply_data['topic_id'], array ('before' => '') );

		$all_reply_data['content'] = bbp_get_reply_content( $reply_id );

	}
	return new WP_REST_Response($all_reply_data, 200);  

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

function bbp_api_replies_one( $data ) {

	$all_reply_data = bbp_api_replies_info( $data['id'] );
	return new WP_REST_Response($all_reply_data, 200);  

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

function bbp_api_replies_post( $data ) {

	//required fields in POST data


	$all_reply_data = bbp_api_replies_info( $data['id'] )->data;

	$all_reply_data['content'] = $data['content'];

	$all_reply_data['email'] = $data['email'];

	$myuser = get_user_by( "email", $data['email'] );

	$reply_id = bbp_insert_reply(

    array(

      'post_parent'  => $all_reply_data['topic_id'],

      'post_title'   => $all_reply_data['title'],

      'post_content' => $all_reply_data['content'],

			'post_author' => $myuser->ID,

    ),

    array(

      'forum_id'     => $all_reply_data['forum_id'],

      'topic_id'     => $all_reply_data['topic_id'],

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