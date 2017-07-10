<?php
/*
 * /bbp-api/topic-tags
*/
function bbp_api_topic_tags() {
	// https://codex.wordpress.org/Function_Reference/get_tags
	$all_ttags_data = get_terms( array(
			'orderby'  => 'count',
			'order'    => 'DESC',
			'taxonomy' => 'topic-tag',
		) );

	if ( empty( $all_ttags_data ) ) {
		return null;
	}

	return $all_ttags_data;
}
