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

class Ping_TTFB {
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
	}

	/**
	 * Shortcode.
	 */
	public static function shortcode() {

		$the_domain = '';

		if ( isset( $_POST['domain'] ) ) {
			$the_domain = $_POST['domain'];
		} elseif ( isset( $_GET['domain'] ) ) {
			$the_domain = $_GET['domain'];
		}

		$result = Helpers::domain_form();

		if ( ! empty( $the_domain ) && Helpers::is_domain_valid( $the_domain ) ) {
			$domain = str_replace( array( 'https', 'http', ':', '/' ), '', $the_domain );

			$ping = shell_exec( 'ping -c 5 ' . $domain );

			$ttfb = shell_exec( 'curl -i -s -w "\nTTFB: %{time_starttransfer}\n" https://' . $domain . ' | egrep "hummingbird-cache|x-cache|TTFB"' );

			$result .= '<div class="host-test-ping-ttfb-results">';
			$result .= str_replace( PHP_EOL, '<br/>', $ping );
			$result .= '<br><br>';
			$result .= str_replace( PHP_EOL, '<br/>', $ttfb );
			$result .= '</div>';
		} else {
			$result .= '<h3>Please enter a domain.</h3>';
		}

		return $result;
	}

}
