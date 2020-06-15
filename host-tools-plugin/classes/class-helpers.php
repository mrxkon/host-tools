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

class Helpers {
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
	 * Domain Form.
	 */
	public static function domain_form() {
		$the_domain = '';

		if ( isset( $_POST['domain'] ) ) {
			$the_domain = $_POST['domain'];
		} elseif ( isset( $_GET['domain'] ) ) {
			$the_domain = $_GET['domain'];
		}

		$html .= '<form class="uk-grid-small" uk-grid id="host-tools-domain-form">';
		$html .= '<div class="uk-width-1-2@s">';
		$html .= '<input class="uk-input" type="text" placeholder="example.com" id="domainInput" name="domain" value="' . $the_domain . '"/>';
		$html .= '</div>';
		$html .= '<div class="uk-width-1-2@s">';
		$html .= wp_nonce_field( 'host_tools_test_nonce', 'htnonce' );
		$html .= '<input class="uk-button uk-button-default" type="submit" value="Submit" />';
		$html .= '</div>';
		$html .= '</form>';

		$html .= '<div class="uk-section">';
		$html .= '<div id="host-test-results">';
		$html .= '<p class="uk-text-warning">Please enter a domain.</p>';
		$html .= '</div>';
		$html .= '</div>';

		return $html;
	}

	/**
	 * Validate domain.
	 */
	public static function is_domain_valid( $domain ) {
		return preg_match( '/^[a-zA-Z0-9\.\-\/\:]*$/', $domain );
	}

}
