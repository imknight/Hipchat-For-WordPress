<?php
class Rework_Hipchat {

	protected $version = '1.0.2';
	protected $plugin_slug = 'rework-hipchat';
	protected $title = 'HipChat For WordPress';
	protected static $instance = null;
	protected $plugin_screen_hook_suffix = null;

	private function __construct() {

		// Load plugin text domain
		//add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// Add the options page and menu item.
		add_action( 'admin_menu', array( $this, 'add_rework_hipchat_menu' ) );

		// Load admin style sheet and JavaScript.
		//add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		//add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

		// Load public-facing style sheet and JavaScript.
		//add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		//add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		$status = get_option('hipchat_notify_status') ? get_option('hipchat_notify_status') : 'all' ;
		if ($status=='all'){
			add_action( 'transition_post_status',array($this,'publish_all_notification'),10,3);
		}else{
			add_action('new_to_publish', array($this,'publish_notification'));
			add_action('draft_to_publish', array($this,'publish_notification'));			
		}
	}

	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	public static function activate( $network_wide ) {
		add_option('hipchat_auth_token');
		add_option('hipchat_notify_status','all');
	}

	public static function deactivate( $network_wide ) {
		delete_option('hipchat_auth_token');
		delete_option('hipchat_room');
		delete_option('hipchat_notify');
		delete_option('hipchat_notify_status');
	}

	public function load_plugin_textdomain() {

		$domain = $this->plugin_slug;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, FALSE, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
	}

	public function enqueue_admin_styles() {

		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $screen->id == $this->plugin_screen_hook_suffix ) {
			wp_enqueue_style( $this->plugin_slug .'-admin-styles', plugins_url( 'css/admin.css', __FILE__ ), array(), $this->version );
		}

	}

	public function enqueue_admin_scripts() {

		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $screen->id == $this->plugin_screen_hook_suffix ) {
			wp_enqueue_script( $this->plugin_slug . '-admin-script', plugins_url( 'js/admin.js', __FILE__ ), array( 'jquery' ), $this->version );
		}

	}

	/**
	 * Register and enqueue public-facing style sheet.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_slug . '-plugin-styles', plugins_url( 'css/public.css', __FILE__ ), array(), $this->version );
	}

	/**
	 * Register and enqueues public-facing JavaScript files.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_slug . '-plugin-script', plugins_url( 'js/public.js', __FILE__ ), array( 'jquery' ), $this->version );
	}


	public function add_rework_hipchat_menu() {
		$this->plugin_screen_hook_suffix = add_options_page(
			__( $this->title, $this->plugin_slug ),
			__( $this->title, $this->plugin_slug ),
			'read',
			$this->plugin_slug,
			array( $this, 'display_plugin_admin_page' )
		);

	}

	public function display_plugin_admin_page() {
		include_once( 'lib/Hipchat.php' );
		$updated = null;
		$error = null;

		if ($_POST) {
		$auth_token = esc_attr(trim($_POST['auth_token']));
		$room = esc_attr(($_POST['room']));
		$status = esc_attr(($_POST['notify_status']));

		$notify = array();

		//remove the last item since is use for clone purpose only
		array_pop($_POST['notify_type']);
		array_pop($_POST['notify_msg']); 

		foreach ($_POST['notify_type'] as $type){
			if (!empty($type))$notify['post_type'][]=esc_attr($type);
		}
		foreach ($_POST['notify_msg'] as $msg){
			if (!empty($msg))$notify['post_msg'][]=esc_attr($msg);
		}		

		//if auth already set no need to keep ping hipchat
		if(get_option('hipchat_auth_token')){
			$successful = true;
		}else{
		// make sure token is valid and room exists
			$hc = new HipChat($auth_token);
			$successful = false;
			try {
			  $r = $hc->message_room($room, get_bloginfo('name'), "Plugin enabled successfully.");
			  if ($r) {
			    $successful = true;
			  }
			} catch (HipChat_Exception $e) {
			  // token must have failed	
			}
		
		}
			if (!$successful) {
				$error = 'Bad auth token or room name.';
			} else if (!$room) {
		      $error = 'Please enter a "Room Name"';
		    } else if (strlen($from) > 15) {
		      $error = '"From Name" must be less than 15 characters.';
		    } else {
				update_option('hipchat_auth_token', $auth_token);
				update_option('hipchat_room', $room);
				update_option('hipchat_notify', $notify);
				update_option('hipchat_notify_status', $status);
				$updated = 'Settings saved! Auth token is valid and room exists.';
			}
		}else{
			$auth_token = get_option('hipchat_auth_token');
    		$room = get_option('hipchat_room');
    		$notify = get_option('hipchat_notify');
    		$status = get_option('hipchat_notify_status');
		}

		$args=array(
		'public'   => true,
		//'_builtin' => false
		); 
		$output = 'names'; // names or objects, note names is the default
		$operator = 'and'; // 'and' or 'or'
		$post_types=get_post_types($args,$output,$operator); 
		include_once( 'views/admin.php' );
	}

	public function publish_all_notification($new_status, $old_status, $post){
		$status = ' - '.$old_status.' to '.$new_status;
		$this->publish_notification($post,$status);
	}

	public function publish_notification($post,$status=false){
		$notify = get_option('hipchat_notify');
		$ping = false;
		foreach ($notify['post_type'] as $i => $type){
			if ($type==$post->post_type){
				$ping = true;
				$msg = $notify['post_msg'][$i];
			}
		}

		if ($ping){
			include_once( 'lib/Hipchat.php' );
			try {
			$auth_token = get_option('hipchat_auth_token');

			// do nothing if plugin is not configured
			if (!$auth_token) {
			return;
			}

			$room = get_option('hipchat_room');

			$message = str_replace('%title%',$post->post_title,$msg);
			if ($status) $message .= $status;
			$hc = new HipChat($auth_token);
			$r = $hc->message_room($room, get_bloginfo('name'), $message);

			if (!$r) {
			// something went wrong! what do we do with an error in WP?
			}
			} catch (HipChat_Exception $e) {
			// something went wrong! what do we do with an error in WP?
			}
			
		return $post;
		}
	}

}