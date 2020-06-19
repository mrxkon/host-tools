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
	 * DNS List.
	 */
	public static function return_dns_list() {
		return array(
			'ns1.wpdns.host',
			'1.1.1.1',
			'8.8.8.8',
		);
	}

	/**
	 * Validate domain.
	 */
	public static function is_domain_valid( $domain ) {
		return preg_match( '/^[a-zA-Z0-9\.\-\/\:]*$/', $domain );
	}

	/**
	 * Clean domain.
	 */
	public static function clean_domain( $domain ) {
		$clean = str_replace( array( 'https', 'http', ':', '/', '\\', '<', '>', '(', ')' ), '', $domain );
		return $clean;
	}

	/**
	 * Validate Certificate.
	 */
	public static function is_cert_valid( $cert ) {
		return preg_match( '/-----BEGIN CERTIFICATE-----[^-]*-----END CERTIFICATE-----/', $cert );
	}

	/**
	 * Validate Private Key.
	 */
	public static function is_key_valid( $key ) {
		return preg_match( '/-----BEGIN PRIVATE KEY-----[^-]*-----END PRIVATE KEY-----/', $key );
	}

	/**
	 * Validate CSR.
	 */
	public static function is_csr_valid( $csr ) {
		return preg_match( '/-----BEGIN CERTIFICATE REQUEST-----[^-]*-----END CERTIFICATE REQUEST-----/', $csr );
	}

}
