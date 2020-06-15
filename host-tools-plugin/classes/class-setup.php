<?php //phpcs:ignore -- \r\n notice.

/**
 * This file comes with "host-tools".
 *
 * Author:      Konstantinos Xenos
 * Author URI:  https://xkon.gr
 * Repo URI:    https://github.com/mrxkon/host-tools/
 * License:     GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

namespace Host_Tools;

class Setup {
	/**
	 * Instance.
	 */
	private static $instance = null;

	/**
	 * Return class instance.
	 */
	public static function get_instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_shortcode( 'host-tools-dns-checkup', array( '\\Host_Tools\\DNS_Checker', 'shortcode' ) );
		add_shortcode( 'host-tools-php-info', array( '\\Host_Tools\\PHP_Info', 'shortcode' ) );
		add_shortcode( 'host-tools-ping-ttfb', array( '\\Host_Tools\\Ping_TTFB', 'shortcode' ) );

		add_action( 'wp_footer', array( '\\Host_Tools\\Setup', 'scripts_styles' ), 999 );
	}

	/**
	 * Scripts & Styles.
	 */
	public static function scripts_styles() {
		$scripts = '';

		echo $scripts;
	}
}


