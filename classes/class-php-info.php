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

class PHP_Info {
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
		ob_start();
		phpinfo();
		$phpinfo_raw = ob_get_clean();

		// Extract the body of the `phpinfo()` call, to avoid all the styles they introduce.
		preg_match_all( '/<body[^>]*>(.*)<\/body>/siU', $phpinfo_raw, $phpinfo );

		// Extract the styles `phpinfo()` creates for this page.
		preg_match_all( '/<style[^>]*>(.*)<\/style>/siU', $phpinfo_raw, $styles );

		// We remove various styles that break the visual flow of wp-admin.
		$remove_patterns = array(
			"/a:.+?\n/si",
			"/body.+?\n/si",
			"/h1.+?\n/si",
			"/h2.+?\n/si",
			"/h3.+?\n/si",
			"/h4.+?\n/si",
			"/h5.+?\n/si",
			"/h6.+?\n/si",
		);

		// Output the styles as an inline style block.
		if ( isset( $styles[1][0] ) ) {
			$styles = preg_replace( $remove_patterns, '', $styles[1][0] );

			echo '<style type="text/css">' . $styles . '</style>';
		}

		// Output the actual phpinfo data.
		if ( isset( $phpinfo[1][0] ) ) {
			return $phpinfo[1][0];
		} else {
			return 'Couldn\'nt get info.';
		}
	}

}
