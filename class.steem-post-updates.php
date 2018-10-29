<?php


class Steem_Post_Updates {
	var $defaults;

	const ADMIN_PAGE = 'steem_post_updates';
	const OPTION_GROUP = 'steem_post_updates';
	const OPTION = 'steem_post_updates';

	static function init() {
		static $instance = null;

		if ( $instance )
			return $instance;

		$class = __CLASS__;
		$instance = new $class;
		return $instance;
	}

	function __construct() {
		$this->defaults = apply_filters( 'steem_post_updates_default_options', array(
			'enable'     => 1,
			'users'      => array(),
			'userinfo'     => array( get_option( 'admin_email' ) ),
			'userinfo_posting_key'     => array( get_option( 'admin_email' ) ),
			'userinfo_tags'     => array( get_option( 'admin_email' ) ),

			'post_types' => array( 'post', 'page' ),
			'drafts'     => 0,
		) );


		$options = $this->get_options();

		if ( $options['enable'] ) {
			// register script
			add_action('admin_enqueue_scripts', array($this, 'register_scripts'));
		}

		if ( current_user_can( 'manage_options' ) ) {
			add_action( 'admin_menu', array( $this, 'admin_menu' ), 115 );
		}
		if(is_admin()){
			add_action( 'load-post.php', array($this, 'init_metabox') );
			add_action( 'load-post-new.php', array($this, 'init_metabox') );
	
		}
	}
	
	public function init_metabox() {
        add_action( 'add_meta_boxes', array( $this, 'add_metabox'  )        );
        add_action( 'save_post',      array( $this, 'save_metabox' ), 10, 2 );
	}
	
    public function add_metabox() {
        add_meta_box(
            'my-meta-box',
            __( 'Steem Settings', 'textdomain' ),
            array( $this, 'render_metabox_2' ),
            'post',
            'advanced',
            'default'
        );
 
	}
	
	public function render_metabox_2($post){
		$options = $this->get_options();
		$current_steem_post_update = $options['enable'];
		$current_footer = "Hello"; 
		?>
		<div class='inside'>
	
			<h3><?php _e( 'Send post to steemit', 'textdomain' ); ?></h3>
			<p>
				<input type="checkbox" name="current_steem_post_update" value="1"<?php checked( $current_steem_post_update, 1 ); ?> /> ON/OFF
			</p>
	
			<h3><?php _e( 'Custom footer', 'textdomain' ); ?></h3>
			<p>
				<textarea rows="4" cols="22" name="current_footer" form="usrform"> <?php echo $current_footer; ?> </textarea>
			</p>
		</div>
		<?php		
	}

    /**
     * Renders the meta box.
     */
    public function render_metabox( $post ) {
        // Add nonce for security and authentication.
        wp_nonce_field( 'custom_nonce_action', 'custom_nonce' );
    }
    /**
     * Handles saving the meta box.
     *
     * @param int     $post_id Post ID.
     * @param WP_Post $post    Post object.
     * @return null
     */
    public function save_metabox( $post_id, $post ) {
        // Add nonce for security and authentication.
        $nonce_name   = isset( $_POST['custom_nonce'] ) ? $_POST['custom_nonce'] : '';
        $nonce_action = 'custom_nonce_action';
 
        // Check if nonce is set.
        if ( ! isset( $nonce_name ) ) {
            return;
        }
 
        // Check if nonce is valid.
        if ( ! wp_verify_nonce( $nonce_name, $nonce_action ) ) {
            return;
        }
 
        // Check if user has permissions to save data.
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }
 
        // Check if not an autosave.
        // if ( wp_is_post_autosave( $post_id ) ) {
        //     return;
        // }
 
        // Check if not a revision.
        // if ( wp_is_post_revision( $post_id ) ) {
        //     return;
		// }
		
		
    }
	

	function register_scripts($page) {
		global $post; 
	    if ( $page == 'post-new.php' || $page == 'post.php' ) {
	    		error_log("Post type ". $post->post_type);
	        if ( 'post' === $post->post_type && isset($_GET['message']) ) { 
				$message_id = absint( $_GET['message'] );
				$options = $this->get_options();
				$the_title = $post->post_title;
				$content = apply_filters('the_content', $post->post_content);
				$content = str_replace(']]>', ']]&gt;', $content);

				$post_tags = array_reverse(get_the_tags($post->ID));
				if ( $post_tags ) {
					foreach( $post_tags as $tag ) {
					// error_log($tag->name . ', ') ; 
					// $options['userinfo'][2] = $tag->name.','.$options['userinfo'][2];
					$options['userinfo_tags'][0] = $tag->name.','.$options['userinfo_tags'][0];

					}
				}
				// check whether post_name is korean or not
				error_log("post slug ". $post->post_name);
				error_log("post tags ".$options['userinfo_tags'][0]);
				error_log("post token ".$options['userinfo_posting_key'][0]);

				wp_register_script( 'steem.min', 'https://cdn.steemjs.com/lib/latest/steem.min.js' );
				wp_enqueue_script('test', plugins_url('/js/steem-post.js', __FILE__), array( 'jquery', 'steem.min' ), true);
				$data = array( 'ID' => $options['userinfo'][0],
							'Token' => $options['userinfo_posting_key'][0],
							'Tags' => $options['userinfo_tags'][0],
							'Title' => $the_title,
							'Content' => $content,
							'Message' => $message_id,
							'Post_ID' => $post->ID,
							'Slug' => $post->post_name
				);
				wp_localize_script( 'test', 'wpsePost', $data );
	        }
	    }
	}


	function get_post_types() {
		$post_types = get_post_types( array( 'public' => true ) );
		$_post_types = array();

		foreach ( $post_types as $post_type ) {
			if ( post_type_supports( $post_type, 'revisions' ) )
				$_post_types[] = $post_type;
		}

		return $_post_types;
	}

	function get_options( $just_defaults = false ) {
		if ( $just_defaults )
			return $this->defaults;

		$options = (array) get_option( 'steem_post_updates' );

		return wp_parse_args( $options, $this->defaults );
	}

    // when post is saved, it works.
    function post_saved($post_id, $post, $update)
    {
		error_log("post_saved ". $post_id);
    	}

	// The meat of the plugin
	function post_updated( $post_id, $post_after, $post_before ) {
		error_log("post_updated ". $post_id);
	}


	function get_post_type_label( $post_type ) {
		// 2.9
		if ( !function_exists( 'get_post_type_object' ) )
			return ucwords( str_replace( '_', ' ', $post_type ) );

		// 3.0
		$post_type_object = get_post_type_object( $post_type );
		if ( empty( $post_type_object->label ) )
			return ucwords( str_replace( '_', ' ', $post_type ) );
		return $post_type_object->label;
	}

	/* Admin */
	function admin_menu() {
		register_setting( self::OPTION_GROUP, self::OPTION, array( $this, 'validate_options' ) );

		add_settings_section( self::ADMIN_PAGE, __( 'WarpSteem settings' ), array( $this, 'settings_section' ), self::ADMIN_PAGE );
		add_settings_field( self::ADMIN_PAGE . '_enable', __( 'Enable' ), array( $this, 'enable_setting' ), self::ADMIN_PAGE, self::ADMIN_PAGE );
		// add_settings_field( self::ADMIN_PAGE . '_users', __( 'Users' ), array( $this, 'users_setting' ), self::ADMIN_PAGE, self::ADMIN_PAGE );
		add_settings_field( self::ADMIN_PAGE . '_userinfo', __( 'Steemit ID' ), array( $this, 'userinfo_setting' ), self::ADMIN_PAGE, self::ADMIN_PAGE );
		add_settings_field( self::ADMIN_PAGE . '_posting_key', __( 'Posting key' ), array( $this, 'userinfo_setting_posting_key' ), self::ADMIN_PAGE, self::ADMIN_PAGE );
		add_settings_field( self::ADMIN_PAGE . '_tags', __( 'Default tags' ), array( $this, 'userinfo_setting_tags' ), self::ADMIN_PAGE, self::ADMIN_PAGE );
		add_settings_field( self::ADMIN_PAGE . '_post_types', __( 'Post Types' ), array( $this, 'post_types_setting' ), self::ADMIN_PAGE, self::ADMIN_PAGE );
		add_settings_field( self::ADMIN_PAGE . '_drafts', __( 'Drafts' ), array( $this, 'drafts_setting' ), self::ADMIN_PAGE, self::ADMIN_PAGE );

		$hook = add_options_page( __( 'WarpSteem settings' ), __( 'WarpSteem settings' ), 'manage_options', self::ADMIN_PAGE, array( $this, 'admin_page' ) );
		add_action( "admin_head-$hook", array( $this, 'admin_page_head' ) );
	}

	// Used in validate_options to array_walk the list of email addresses
	function trim_email( &$email, $key ) {
		$email = trim( $email );
	}

	function validate_options( $options ) {
		if ( !$options || !is_array( $options ) )
			return $this->defaults;

		$return = array();

		$return['enable'] = ( empty( $options['enable'] ) ) ? 0 : 1;

		if ( empty( $options['users'] ) || !is_array( $options ) ) {
			$return['users'] = $this->defaults['users'];
		} else {
			$return['users'] = $options['users'];
		}

		if ( empty( $options['userinfo'] ) ) {
			if ( count( $return['users'] ) )
				$return['userinfo'] = array();
			else
				$return['userinfo'] = $this->defaults['userinfo'];
		} else {
			$_userinfo = is_string( $options['userinfo'] ) ? preg_split( '(\n|\r)', $options['userinfo'], -1, PREG_SPLIT_NO_EMPTY ) : array();
			$_userinfo = array_unique( $_userinfo );
			array_walk( $_userinfo, array( 'Steem_Post_Updates', 'trim_email' ) );
			$userinfo = array_filter( $_userinfo, 'is_email' );

			$invalid_userinfo = array_diff( $_userinfo, $userinfo );
			if ( $invalid_userinfo )
				$return['userinfo'] = $invalid_userinfo;

			// Don't store a huge list of invalid userinfo addresses in the option
			if ( isset( $return['invalid_userinfo'] ) && count( $return['invalid_userinfo'] ) > 200 ) {
				$return['invalid_userinfo'] = array_slice( $return['invalid_userinfo'], 0, 200 );
				$return['invalid_userinfo'][] = __( 'and many more not listed here' );
			}

			// Cap to at max 200 email addresses
			if ( count( $return['userinfo'] ) > 200 ) {
				$return['userinfo'] = array_slice( $return['userinfo'], 0, 200 );
			}
		}

		if ( empty( $options['userinfo_posting_key'] ) ) {
			if ( count( $return['users'] ) )
				$return['userinfo_posting_key'] = array();
			else
				$return['userinfo_posting_key'] = $this->defaults['userinfo_posting_key'];
		} else {
			$_userinfo = is_string( $options['userinfo_posting_key'] ) ? preg_split( '(\n|\r)', $options['userinfo_posting_key'], -1, PREG_SPLIT_NO_EMPTY ) : array();
			$_userinfo = array_unique( $_userinfo );
			array_walk( $_userinfo, array( 'Steem_Post_Updates', 'trim_email' ) );
			$userinfo = array_filter( $_userinfo, 'is_email' );

			$invalid_userinfo = array_diff( $_userinfo, $userinfo );
			if ( $invalid_userinfo )
				$return['userinfo_posting_key'] = $invalid_userinfo;

			// Don't store a huge list of invalid userinfo addresses in the option
			if ( isset( $return['invalid_userinfo'] ) && count( $return['invalid_userinfo'] ) > 200 ) {
				$return['invalid_userinfo'] = array_slice( $return['invalid_userinfo'], 0, 200 );
				$return['invalid_userinfo'][] = __( 'and many more not listed here' );
			}

			// Cap to at max 200 email addresses
			if ( count( $return['userinfo_posting_key'] ) > 200 ) {
				$return['userinfo_posting_key'] = array_slice( $return['userinfo_posting_key'], 0, 200 );
			}
		}

		if ( empty( $options['userinfo_tags'] ) ) {
			if ( count( $return['users'] ) )
				$return['userinfo_tags'] = array();
			else
				$return['userinfo_tags'] = $this->defaults['userinfo_tags'];
		} else {
			$_userinfo = is_string( $options['userinfo_tags'] ) ? preg_split( '(\n|\r)', $options['userinfo_tags'], -1, PREG_SPLIT_NO_EMPTY ) : array();
			$_userinfo = array_unique( $_userinfo );
			array_walk( $_userinfo, array( 'Steem_Post_Updates', 'trim_email' ) );
			$userinfo = array_filter( $_userinfo, 'is_email' );

			$invalid_userinfo = array_diff( $_userinfo, $userinfo );
			if ( $invalid_userinfo )
				$return['userinfo_tags'] = $invalid_userinfo;

			// Don't store a huge list of invalid userinfo addresses in the option
			if ( isset( $return['invalid_userinfo'] ) && count( $return['invalid_userinfo'] ) > 200 ) {
				$return['invalid_userinfo'] = array_slice( $return['invalid_userinfo'], 0, 200 );
				$return['invalid_userinfo'][] = __( 'and many more not listed here' );
			}

			// Cap to at max 200 email addresses
			if ( count( $return['userinfo_tags'] ) > 200 ) {
				$return['userinfo_tags'] = array_slice( $return['userinfo_tags'], 0, 200 );
			}
		}		

		if ( empty( $options['post_types'] ) || !is_array( $options ) ) {
			$return['post_types'] = $this->defaults['post_types'];
		} else {
			$post_types = array_intersect( $options['post_types'], $this->get_post_types() );
			$return['post_types'] = $post_types ? $post_types : $this->defaults['post_types'];
		}

		$return['drafts'] = ( empty( $options['drafts'] ) ) ? 0 : 1;

		do_action( 'steem_post_updates_validate_options', $this->get_options(), $return );

		return $return;
	}

	function admin_page_head() {
?>
<style>
.epc-registered-user-selection {
	overflow: auto;
	max-height: 300px;
	max-width: 40em;
	border: 1px solid #ccc;
	background-color: #fafafa;
	padding: 12px;
	box-sizing: border-box;
}
.epc-registered-user-selection ul {
	margin: 0;
	padding: 0;
}
.epc-additional-userinfo {
	width: 40em;
}
</style>
<?php
	}

	function admin_page() {
		$options = $this->get_options();
?>

<div class="wrap">
	<h2><?php _e( 'WarpSteem settings' ); ?></h2>
<?php	if ( !empty( $options['invalid_userinfo'] ) && $_GET['settings-updated'] ) : ?>
	<div class="error">
		<p><?php printf( _n( 'Invalid Email: %s', 'Invalid userinfo: %s', count( $options['invalid_userinfo'] ) ), '<kbd>' . join( '</kbd>, <kbd>', array_map( 'esc_html', $options['invalid_userinfo'] ) ) ); ?></p>
	</div>
<?php	endif; ?>

	<form action="options.php" method="post">
		<?php settings_fields( self::OPTION_GROUP ); ?>
		<?php do_settings_sections( self::ADMIN_PAGE ); ?>
		<p class="submit">
			<input type="submit" class="button-primary" value="<?php esc_attr_e( 'Save Changes' ); ?>" />
		</p>
	</form>
</div>
<?php
	}

	function settings_section() {} // stub

	function enable_setting() {
		$options = $this->get_options();
?>
		<p><label><input type="checkbox" name="steem_post_updates[enable]" value="1"<?php checked( $options['enable'], 1 ); ?> /> <?php _e( 'send post to steemit when a post updates.' ); ?></label></p>
<?php
	}


	function sort_users_by_display_name( $a, $b ) {
		return strcmp( strtolower( $a->display_name ), strtolower( $b->display_name ) );
	}

	function userinfo_setting() {
		$options = $this->get_options();
?>
		<textarea class="epc-additional-userinfo" rows="1" cols="40" name="steem_post_updates[userinfo]"><?php echo esc_html( join( "\n", $options['userinfo'] ) ); ?></textarea>
		<p class="description"><?php _e( 'Write your steemit ID, except @' ); ?></p>
<?php
	}

	function userinfo_setting_posting_key() {
		$options = $this->get_options();
?>
		<textarea class="epc-additional-userinfo" rows="1" cols="40" name="steem_post_updates[userinfo_posting_key]"><?php echo esc_html( join( "\n", $options['userinfo_posting_key'] ) ); ?></textarea>
		<p class="description"><?php _e( 'Write your posting key' ); ?></p>
<?php
	}

	function userinfo_setting_tags() {
		$options = $this->get_options();
?>
		<textarea class="epc-additional-userinfo" rows="1" cols="40" name="steem_post_updates[userinfo_tags]"><?php echo esc_html( join( "\n", $options['userinfo_tags'] ) ); ?></textarea>
		<p class="description"><?php _e( "Write default tags using ',' --> ex) dev,test,warpsteem " ); ?></p>
<?php
	}

	function post_types_setting() {
		$options = $this->get_options();
?>
		<ul>
<?php		foreach ( $this->get_post_types() as $post_type ) :
			$label = $this->get_post_type_label( $post_type );
?>
			<li><label><input type="checkbox" name="steem_post_updates[post_types][]" value="<?php echo esc_attr( $post_type ); ?>"<?php checked( in_array( $post_type, $options['post_types'] ) ); ?> /> <?php echo esc_html( $label ); ?></label></li>
<?php		endforeach; ?>
		</ul>
<?php
	}

	function drafts_setting() {
		$options = $this->get_options();
?>
		<p><label><input type="checkbox" name="steem_post_updates[drafts]" value="1"<?php checked( $options['drafts'], 1 ); ?> /> <?php _e( 'drafts is not just published.' ); ?></label></p>
<?php
	}
}

