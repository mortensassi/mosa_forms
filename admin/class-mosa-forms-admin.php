<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       mortensassi.com
 * @since      1.0.0
 *
 * @package    Mosa_Forms
 * @subpackage Mosa_Forms/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Mosa_Forms
 * @subpackage Mosa_Forms/admin
 * @author     Morten Sassi <mail@mortensassi.com>
 */
class Mosa_Forms_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Mosa_Forms_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Mosa_Forms_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/mosa-forms-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Mosa_Forms_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Mosa_Forms_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/mosa-forms-admin.js', array( 'jquery' ), $this->version, false );

	}

    /**
     * Register custom post type for mosa forms
     *
     * @since    1.0.0
     */
    public function register_post_type() {

        register_extended_post_type( 'mosa-form', [
            'menu_icon'	=> 'data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><defs/><path fill="black" d="M256 32C114.6 32 0 125.1 0 240c0 49.6 21.4 95 57 130.7C44.5 421.1 2.7 466 2.2 466.5c-2.2 2.3-2.8 5.7-1.5 8.7S4.8 480 8 480c66.3 0 116-31.8 140.6-51.4 32.7 12.3 69 19.4 107.4 19.4 141.4 0 256-93.1 256-208S397.4 32 256 32zm64 133.2c14.8 0 26.8 12 26.8 26.8s-12 26.8-26.8 26.8-26.8-12-26.8-26.8 12-26.8 26.8-26.8zm-128 0c14.8 0 26.8 12 26.8 26.8s-12 26.8-26.8 26.8-26.8-12-26.8-26.8 12-26.8 26.8-26.8zm164.2 140.9C331.3 335.3 294.8 352 256 352c-38.8 0-75.3-16.7-100.2-45.9-5.8-6.7-5-16.8 1.8-22.5 6.7-5.7 16.8-5 22.5 1.8 18.8 22 46.5 34.6 75.8 34.6 29.4 0 57-12.6 75.8-34.7 5.8-6.7 15.9-7.5 22.6-1.8 6.8 5.8 7.6 15.9 1.9 22.6z"/></svg>'),
            'hierarchical' => false,
            'menu_position' => 99,
            'acfe_admin_archive' => false,
            'show_in_nav_menus' => false,
            'rewrite' => false,
            'publicly_queryable'  => false,
            'show_in_rest' => false,
            'block_editor' => false,
            'dashboard_glance' => false,
            'quick_edit' => false,
            'supports' => array( 'title', 'author', 'revisions', 'custom-fields' ),
            'admin_cols' => [
                'title',
                'shortcode' => array(
                    'title'    => __('Shortcode','mosa-forms'),
                    'function' => function() {
                        echo '<pre>[mosa-forms id="'.get_the_ID().'"]</pre>';
                    },
                ),
                'author',
                'published' => array(
                    'title'       => __('Published','mosa-forms'),
                    'meta_key'    => 'published_date',
                    'date_format' => 'd/m/Y'
                ),
            ]
        ],[
            'singular' => __('Mosa Form','mosa-forms'),
            'plural'   => __('Mosa Form','mosa-forms'),
        ] );

    }

}
