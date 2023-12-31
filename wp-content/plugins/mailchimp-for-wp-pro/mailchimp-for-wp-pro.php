<?php
/*
Plugin Name: MailChimp for WordPress Pro
Plugin URI: https://mc4wp.com/#utm_source=wp-plugin&utm_medium=mailchimp-for-wp-pro&utm_campaign=plugins-page
Description: Pro version of MailChimp for WordPress. Adds various sign-up methods to your website.
Version: 2.7.16
Author: ibericode
Author URI: https://ibericode.com/
License: GPL v3
Text Domain: mailchimp-for-wp

MailChimp for WordPress alias MC4WP
Copyright (C) 2012-2015, Danny van Kooten, danny@ibericode.com

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

if( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

function mc4wp_pro_load_plugin() {

	define( 'MC4WP_VERSION', '2.7.16' );
	define( 'MC4WP_PLUGIN_FILE', __FILE__ );
	define( 'MC4WP_PLUGIN_DIR', dirname( __FILE__ ) . '/' );
	define( 'MC4WP_PLUGIN_URL', plugins_url( '/', __FILE__ ) );

	// Composer PHP 5.2 compatible autoloader
	require MC4WP_PLUGIN_DIR . '/vendor/autoload_52.php';

	 // Global Functions
	require_once MC4WP_PLUGIN_DIR . 'includes/functions/general.php';
	require_once MC4WP_PLUGIN_DIR . 'includes/functions/template.php';

	// Load admin class before rest of plugin (so translations are loaded early)
	if( is_admin()
	    && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {
		new MC4WP_Admin();
	}

	// Initialize Plugin Class
	require_once MC4WP_PLUGIN_DIR . 'includes/class-plugin.php';
	MC4WP::init();
	$GLOBALS['mc4wp'] = MC4WP::instance();
}

add_action( 'plugins_loaded', 'mc4wp_pro_load_plugin', 10 );



// Only add these hooks on Admin requests
if( is_admin() ) {
	// activation & deactivation hooks
	require_once dirname( __FILE__ ) . '/includes/class-installer.php';
	register_activation_hook( __FILE__, array( 'MC4WP_Installer', 'run' ) );
}