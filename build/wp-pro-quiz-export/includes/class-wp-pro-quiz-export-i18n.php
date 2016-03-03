<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       http://www.battarra.it
 * @since      1.0.0
 *
 * @package    Wp_Pro_Quiz_Export
 * @subpackage Wp_Pro_Quiz_Export/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Wp_Pro_Quiz_Export
 * @subpackage Wp_Pro_Quiz_Export/includes
 * @author     Fabio Battarra <fabio.battarra@gmail.com>
 */
class Wp_Pro_Quiz_Export_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'wp-pro-quiz-export',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
