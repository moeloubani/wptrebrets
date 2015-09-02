<?php

namespace wptrebrets\inc;

class Options {

	/**
	 * Option key, and option page slug
	 * @var string
	 */
	protected static $key = 'wptrebrets_options';
	/**
	 * Array of metaboxes/fields
	 * @var array
	 */
	protected static $plugin_options = array();

	/**
	 * Options Page title
	 * @var string
	 */
	protected $title = '';

	/**
	 * Constructor
	 * @since 0.1.0
	 */
	public function __construct() {
		// Set our title
		$this->title = __( 'TREB Options', 'wptrebrets' );
	}

	/**
	 * Initiate our hooks
	 * @since 0.1.0
	 */
	public function hooks() {
		add_action( 'admin_init', array( $this, 'init' ) );
		add_action( 'admin_menu', array( $this, 'add_options_page' ) );
	}

	/**
	 * Register our setting to WP
	 * @since  0.1.0
	 */
	public function init() {
		register_setting( self::$key, self::$key );
	}

	/**
	 * Add menu options page
	 * @since 0.1.0
	 */
	public function add_options_page() {
		$this->options_page = add_options_page( $this->title, $this->title, 'manage_options', self::$key, array( $this, 'admin_page_display' ) );
	}

	/**
	 * Admin page markup. Mostly handled by CMB
	 * @since  0.1.0
	 */
	public function admin_page_display() {
		?>
		<div class="wrap cmb_options_page <?php echo self::$key; ?>">
			<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
			<?php cmb_metabox_form( self::option_fields(), self::$key ); ?>
		</div>
	<?php
	}

	/**
	 * Defines the theme option metabox and field configuration
	 * @since  0.1.0
	 * @return array
	 */
	public static function option_fields() {

		// Only need to initiate the array once per page-load
		if ( ! empty( self::$plugin_options ) )
			return self::$plugin_options;

		/*
		 * TODO: Add way to integrate other post types' custom fields
		 */

		self::$plugin_options = array(
			'id'         => 'plugin_options',
			'show_on'    => array( 'key' => 'options-page', 'value' => array( self::$key, ), ),
			'show_names' => true,
			'fields'     => array(
				array(
					'name' => __( 'RETS URL', 'wptrebrets' ),
					'desc' => __( 'Add the URL from the information sent to you.', 'wptrebrets' ),
					'id'   => 'rets_url',
					'type' => 'text',
				),
				array(
					'name' => __( 'RETS Username', 'wptrebrets' ),
					'desc' => __( 'Add the username from the information sent to you.', 'wptrebrets' ),
					'id'   => 'rets_username',
					'type' => 'text',
				),
				array(
					'name' => __( 'RETS Password', 'wptrebrets' ),
					'desc' => __( 'Add the password from the information sent to you.', 'wptrebrets' ),
					'id'   => 'rets_password',
					'type' => 'text',
				),
				array(
					'name' => __( 'RETS Limit', 'wptrebrets' ),
					'desc' => __( 'How many properties to get on initial upload.', 'wptrebrets' ),
					'id'   => 'rets_limit',
					'type' => 'text',
				)

			),
		);
		return self::$plugin_options;
	}

	/**
	 * Make public the protected $key variable.
	 * @since  0.1.0
	 * @return string  Option key
	 */
	public static function key() {
		return self::$key;
	}

}

