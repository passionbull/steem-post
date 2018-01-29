<?php

/*
Plugin Name: Steem Post
Description: When you write post on wordpress, this post is updated to steem.
Plugin URI: 
Version: 1.0
Author: Jacob yu
Author URI: http://steemit.com/@jacobyu
*/

require_once dirname( __FILE__ ) . '/class.steem-post-changes.php';
#require_once dirname( __FILE__ ) . '/class.wp-steem.php';
#require_once dirname( __FILE__ ) . '/class.wp-steem-post.php';


function Steem_post_changes_action_links( $links ) {
	#wp_register_script( $handle, $src, $deps, $ver, $in_footer );
	#wp_register_script( $handle, $src, $deps, $ver, $in_footer );

	array_unshift( $links, '<a href="options-general.php?page=' . Steem_Post_Changes::ADMIN_PAGE . '">' . __( 'Settings' ) . "</a>" );
	return $links;
}

add_action( 'init', array( 'Steem_Post_Changes', 'init' ) );
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'Steem_post_changes_action_links' );