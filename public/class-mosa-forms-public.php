<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://mortensassi.com
 * @since      1.0.0
 *
 * @package    Mosa_Forms
 * @subpackage Mosa_Forms/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Mosa_Forms
 * @subpackage Mosa_Forms/public
 * @author     Morten Sassi <dev@mortensassi.com>
 */
class Mosa_Forms_Public {

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
	 * Locate frontend manifest file
	 *
	 * @var
	 */
	private $manifest_path;
	private $manifest;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->manifest_path = plugin_dir_path( __FILE__ ) . 'app/dist/manifest.json';

		if (file_exists($this->manifest_path)) {
			$manifest = json_decode(file_get_contents($this->manifest_path), TRUE);
		} else {
			$manifest = [];
		}

		$this->manifest = $manifest;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
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

		// wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/mosa-forms-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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

		if (defined('WP_ENV') && WP_ENV == 'production') { ?>
            <script type="module" src="https://<?= $_SERVER['HTTP_HOST'] ?>:3000/@vite/client"></script>
            <script type="module" src="https://<?= $_SERVER['HTTP_HOST'] ?>:3000/src/main.js"></script>
        <?php
		} else {
			wp_register_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'app/dist/' . $this->manifest['src/main.js']['file'], $this->version, true );
            wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'app/dist/style.css', $this->version, true );
		};
	}

}
