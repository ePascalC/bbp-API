<?php
/*
 * /bbp-api/users
*/
function bbp_api_users() {
	// Prepared for future use
	$all_users_data = array();

	if ( empty( $all_users_data ) ) {
		return null;
	}

	return $all_users_data;
}

/*
 * /bbp-api/users/<id>
*/
function bbp_api_users_one($data) {
	$all_user_data = array();

	$user_id = $data['id'];
	if ($user_id) {

		global $wpdb;
		$user_data = $wpdb->get_row( "SELECT * FROM wp_users WHERE ID = $user_id ");

		$all_user_data['display_name'] = $user_data->display_name;
		$all_user_data['nicename'] = $user_data->user_nicename;
		$all_user_data['avatar'] = get_avatar( $user_id, 32 ) ;
		$all_user_data['registered_date'] = $user_data->user_registered;

	}
	if ( empty( $all_user_data ) ) {
		return null;
	}

	return $all_user_data;
}
