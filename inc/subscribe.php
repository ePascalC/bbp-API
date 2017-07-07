<?php
/**

	Routes:
	- /bbp-api/forum/subscribe
	- /bbp-api/forum/unsubscribe
		Params:
		- user_id 
		- forum_id

	- /bbp-api/topic/subscribe
	- /bbp-api/topic/unsubscribe
		Params:
		- user_id 
		- topic_id
*/

/*
	POST
 * /bbp-api/forum/subscribe

*/

function bbp_api_subscribe_forum ($data){

	$user_id = $data['user_id'];

	$forum_id = $data['forum_id'];

	$result  = false;

	$result = bbp_add_user_forum_subscription (  $user_id,  $forum_id );

	return new WP_REST_Response($result, 200);  

}

/*
	POST
 * /bbp-api/topic/subscribe

*/

function bbp_api_subscribe_topic ($data){

	$user_id = $data['user_id'];

	$topic_id = $data['topic_id'];

	$result  = false;
	
	$result = bbp_add_user_topic_subscription (  $user_id,  $topic_id );

	return new WP_REST_Response($result, 200);  

}


/*
	POST
 * /bbp-api/forum/unsubscribe

*/

function bbp_api_unsubscribe_forum ($data){

	$user_id = $data['user_id'];

	$forum_id = $data['forum_id'];

	$result  = false;

	$result = bbp_remove_user_forum_subscription (  $user_id,  $forum_id );

	return new WP_REST_Response($result, 200);  

}

/*
	POST
 * /bbp-api/topic/unsubscribe

*/

function bbp_api_unsubscribe_topic ($data){

	$user_id = $data['user_id'];

	$topic_id = $data['topic_id'];

	$result  = false;
	
	$result = bbp_remove_user_topic_subscription (  $user_id,  $topic_id );

	return new WP_REST_Response($result, 200);  

}

