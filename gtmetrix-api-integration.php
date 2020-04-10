<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://yupscode.com/our-team
 * @since             1.0.0
 * @package           Gtmetrix_Api_Integration
 *
 * @wordpress-plugin
 * Plugin Name:       GTmetrix API integration
 * Plugin URI:        https://yupscode.com/
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Damian Kudosz
 * Author URI:        https://yupscode.com/our-team
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       gtmetrix-api-integration
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'GTMETRIX_API_INTEGRATION_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-gtmetrix-api-integration-activator.php
 */
global $gtm_db_version;
$gtm_db_version = '1.0';

function activate_gtmetrix_api_integration() {
	global $wpdb;
	global $gtm_db_version;

	$table_name = $wpdb->prefix . 'gtm_settings';
	
	$charset_collate = $wpdb->get_charset_collate();
	
	//Create Database table
	$sql = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		api_email varchar(55) DEFAULT '' NOT NULL,
		api_key varchar(55) DEFAULT '' NOT NULL,
		PRIMARY KEY  (id)
	) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );

	add_option( 'gtm_db_version', $gtm_db_version );
	
	//Create PDF Reports directory
	$upload = wp_upload_dir();
    $upload_dir = $upload['basedir'];
    $upload_dir = $upload_dir . '/pdf-reports';
    if (! is_dir($upload_dir)) {
		mkdir( $upload_dir, 0700 );
    }
	
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-gtmetrix-api-integration-activator.php';
	Gtmetrix_Api_Integration_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-gtmetrix-api-integration-deactivator.php
 */
function deactivate_gtmetrix_api_integration() {
	
	//Remove DB Table
	//if( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) exit();
    global $wpdb;
	$table_name = $wpdb->prefix . 'gtm_settings';
    $wpdb->query( "DROP TABLE IF EXISTS $table_name" );
    delete_option("gtm_db_version");
	
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-gtmetrix-api-integration-deactivator.php';
	Gtmetrix_Api_Integration_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_gtmetrix_api_integration' );
register_deactivation_hook( __FILE__, 'deactivate_gtmetrix_api_integration' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-gtmetrix-api-integration.php';
require plugin_dir_path( __FILE__ ) . 'gtmetrix-api-integration-admin.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_gtmetrix_api_integration() {

	$plugin = new Gtmetrix_Api_Integration();
	$plugin->run();

}
run_gtmetrix_api_integration();

/*** Buffer ***/
add_action('init', 'do_output_buffer');
function do_output_buffer() {
        ob_start();
}

/*** Create Admin page ***/

function gtm_admin_menu() {
	add_menu_page(
		__( 'GTmetrix', 'gtmetrix-plugin' ),
		__( 'GTmetrix', 'gtmetrix-plugin' ),
		'manage_options',
		'gtmetrix-plugin',
		'gtm_admin_menu_contents',
		'dashicons-analytics',
		60
	);
}
add_action('admin_menu', 'gtm_admin_menu');
/*** END Create Admin page ***/
