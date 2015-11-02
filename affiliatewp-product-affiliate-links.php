<?php
/**
 * Plugin Name: AffiliateWP - Product Affiliate Links
 * Plugin URI: https://www.yudhistiramauris.com/products/affiliatewp-product-affiliate-links/
 * Description: Display product affiliate link for each product in affiliate area.
 * Version: 1.1
 * Author: Yudhistira Mauris
 * Author URI: https://www.yudhistiramauris.com/
 * Text Domain: affwp-paffl
 * License: GPLv2
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path: languages
 *
 * Copyright © 2015 Yudhistira Mauris (email: mauris@yudhistiramauris.com)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as 
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/gpl-2.0.txt>.
 *
 * @author Yudhistira Mauris <mauris@yudhistiramauris.com>
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// Check if class AFFWP_PAFFL already exists
if ( ! class_exists( 'AFFWP_PAFFL' ) ) :

/**
* Main AFFWP_PAFFL class
*
* This main class is responsible for instantiating the class, including the necessary files
* used throughout the plugin, and loading the plugin translation files.
*
* @since 1.0
*/
final class AFFWP_PAFFL {

	/**
	 * The one and only true AFFWP_PAFFL instance
	 *
	 * @since 1.0
	 * @access private
	 * @var object $instance
	 */
	private static $instance;

	/**
	 * Instantiate the main class
	 *
	 * This function instantiates the class, initialize all functions and return the object.
	 * 
	 * @since 1.0
	 * @return object The one and only true AFFWP_PAFFL instance.
	 */
	public static function instance() {

		if ( ! isset( self::$instance ) && ( ! self::$instance instanceof AFFWP_PAFFL ) ) {

			self::$instance = new AFFWP_PAFFL;
			self::$instance->setup_constants();
			
			add_action( 'plugins_loaded', array( self::$instance, 'load_textdomain' ) );

			self::$instance->includes();
			self::$instance->updater();

			self::$instance->integrations = new AFFWP_PAFFL_Integrations();
		}

		return self::$instance;
	}	

	/**
	 * Function for setting up constants
	 *
	 * This function is used to set up constants used throughout the plugin.
	 *
	 * @since 1.0
	 */
	public function setup_constants() {

		// Plugin version
		if ( ! defined( 'AFFWP_PAFFL_VERSION' ) ) {
			define( 'AFFWP_PAFFL_VERSION', '1.1' );
		}

		// Plugin file
		if ( ! defined( 'AFFWP_PAFFL_FILE' ) ) {
			define( 'AFFWP_PAFFL_FILE', __FILE__ );
		}		

		// Plugin folder path
		if ( ! defined( 'AFFWP_PAFFL_PLUGIN_PATH' ) ) {
			define( 'AFFWP_PAFFL_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
		}

		// Plugin folder URL
		if ( ! defined( 'AFFWP_PAFFL_PLUGIN_URL' ) ) {
			define( 'AFFWP_PAFFL_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
		}

		// Plugin item name
		if ( ! defined( 'AFFWP_PAFFL_ITEM_NAME' ) ) {
			define( 'AFFWP_PAFFL_ITEM_NAME', 'AffiliateWP - Product Affiliate Links' );
		}

		// Plugin store URL
		if ( ! defined( 'AFFWP_PAFFL_STORE_URL' ) ) {
			define( 'AFFWP_PAFFL_STORE_URL', 'https://www.yudhistiramauris.com/' );
		}
	}

	/**
	 * Load text domain used for translation
	 *
	 * This function loads mo and po files used to translate text strings used throughout the 
	 * plugin.
	 *
	 * @since 1.0
	 */
	public function load_textdomain() {

		// Set filter for plugin language directory
		$lang_dir = dirname( plugin_basename( AFFWP_PAFFL_FILE ) ) . '/languages/';
		$lang_dir = apply_filters( 'affwp_paffl_languages_directory', $lang_dir );

		// Load plugin translation file
		load_plugin_textdomain( 'affwp-paffl', false, $lang_dir );
	}

	/**
	 * Includes all necessary PHP files
	 *
	 * This function is responsible for including all necessary PHP files.
	 *
	 * @since  1.0
	 */
	public function includes() {		
		
		if ( is_admin() ) {

			include AFFWP_PAFFL_PLUGIN_PATH . '/includes/admin/settings/class-settings.php';
			include AFFWP_PAFFL_PLUGIN_PATH . '/includes/AFFWP_PAFFL_Plugin_Updater.php';
		}

		include AFFWP_PAFFL_PLUGIN_PATH . '/includes/class-integrations.php';
	}

	/**
	 * Plugin auto updater function
	 *
	 * @since 1.0
	 */
	public function updater() {

		if ( ! is_admin() ) {
			return;
		}

		$license_key = trim( affiliate_wp()->settings->get( 'paffl_license_key' ) );
		
		$update = new AFFWP_PAFFL_Plugin_Updater( AFFWP_PAFFL_STORE_URL, AFFWP_PAFFL_FILE, array(

			'version' 	=> AFFWP_PAFFL_VERSION,
			'license' 	=> $license_key,
			'item_name' => AFFWP_PAFFL_ITEM_NAME,
			'author' 	=> 'Yudhistira Mauris',
			'url'       => home_url(),
		) );
	}
}
endif; // End if class_exist check

/**
 * The main function for returning AFFWP_PAFFL instance
 *
 * @since 1.0
 * @return object The one and only true AFFWP_PAFFL instance.
 */
function affwp_paffl() {
	return AFFWP_PAFFL::instance();
}
add_action( 'plugins_loaded', 'affwp_paffl' );