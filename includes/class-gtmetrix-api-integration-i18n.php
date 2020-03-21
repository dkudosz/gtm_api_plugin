<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://yupscode.com/our-team
 * @since      1.0.0
 *
 * @package    Gtmetrix_Api_Integration
 * @subpackage Gtmetrix_Api_Integration/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Gtmetrix_Api_Integration
 * @subpackage Gtmetrix_Api_Integration/includes
 * @author     Damian Kudosz <damian@yupscode.com>
 */
class Gtmetrix_Api_Integration_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'gtmetrix-api-integration',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
