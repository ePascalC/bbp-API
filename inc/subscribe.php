<?php
/**

	Routes:
	- /bbp-api/subscribe/forum
	- /bbp-api/unsubscribe/forum
		Params:
		- user_id 
		- forum_id

	- /bbp-api/subscribe/topic
	- /bbp-api/unsubscribe/topic
		Params:
		- user_id 
		- topic_id
*/

/*
	POST
 * /bbp-api/subscribe/forum

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
 * /bbp-api/subscribe/topic

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
 * /bbp-api/unsubscribe/forum

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
 * /bbp-api/unsubscribe/topic

*/

function bbp_api_unsubscribe_topic ($data){

	$user_id = $data['user_id'];

	$topic_id = $data['topic_id'];

	$result  = false;
	
	$result = bbp_remove_user_topic_subscription (  $user_id,  $topic_id );

	return new WP_REST_Response($result, 200);  

}

