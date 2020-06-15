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

		preg_match_all( '/<body[^>]*>(.*)<\/body>/siU', $phpinfo_raw, $phpinfo );
		preg_match_all( '/<style[^>]*>(.*)<\/style>/siU', $phpinfo_raw, $styles );

		$remove_patterns = array(
			"/a:.+?\n/si",
			"/body.+?\n/si",
		);

		if ( isset( $styles[1][0] ) ) {
			$styles      = preg_replace( $remove_patterns, '', $styles[1][0] );
			$style_array = explode( "\n", $styles );

			echo '<style type="text/css">';

			foreach ( $style_array as $sr ) {
				if ( ! empty( $sr ) ) {
					echo 'div.entry ' . $sr;
				}
			}

			echo '</style>';
		}

		if ( isset( $phpinfo[1][0] ) ) {
			return $phpinfo[1][0];
		} else {
			return 'Couldn not retrieve PHP Info.';
		}
	}

}
