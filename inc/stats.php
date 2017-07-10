<?php
/*
 * /bbp-api/stats
*/
function bbp_api_stats() {
	$all_stats_data = bbp_get_statistics();
	
	if ( empty( $all_stats_data ) ) {
		return null;
	}

	return $all_stats_data;
}
