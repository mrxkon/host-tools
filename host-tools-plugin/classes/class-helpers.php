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
		$form .= '<form class="uk-grid-small" uk-grid method="POST">';
		$form .= '<div class="uk-width-1-2@s">';
		$form .= '<input class="uk-input" type="text" placeholder="example.com" id="domainInput" name="domain" value="' . $the_domain . '"/>';
		$form .= '</div>';
		$form .= '<div class="uk-width-1-2@s">';
		$form .= '<input class="uk-button uk-button-default" type="submit" value="Submit" />';
		$form .= '</div>';
		$form .= '</form>';

		return $form;
	}

	/**
	 * Validate domain.
	 */
	public static function is_domain_valid( $domain ) {
		return preg_match( '/^[a-zA-Z0-9\.\-\/\:]*$/', $domain );
	}

}
