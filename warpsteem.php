<?php

/*
Plugin Name: WarpSteem
Description: When you write post on wordpress, this post is updated to steemit.
Plugin URI: 
Version: 1.0.2
Author: Jacob yu
Author URI: http://busy.org/@jacobyu
*/

require_once dirname( __FILE__ ) . '/class.steem-post-updates.php';

function Steem_post_updates_action_links( $links ) {
	#wp_register_script( $handle, $src, $deps, $ver, $in_footer );
	#wp_register_script( $handle, $src, $deps, $ver, $in_footer );

	array_unshift( $links, '<a href="options-general.php?page=' . Steem_Post_Updates::ADMIN_PAGE . '">' . __( 'Settings' ) . "</a>" );
	return $links;
}

add_action( 'init', array( 'Steem_Post_Updates', 'init' ) );
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'Steem_post_updates_action_links' );
