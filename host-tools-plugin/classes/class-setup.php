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
		// Shortcodes.
		add_shortcode( 'host-tools-dns-checkup', array( '\\Host_Tools\\DNS_Checker', 'shortcode' ) );
		add_shortcode( 'host-tools-php-info', array( '\\Host_Tools\\PHP_Info', 'shortcode' ) );
		add_shortcode( 'host-tools-ping-ttfb', array( '\\Host_Tools\\Ping_TTFB', 'shortcode' ) );

		// General Scripts & Styles.
		add_action( 'wp_footer', array( '\\Host_Tools\\Setup', 'scripts_styles' ), 999 );

		// DNS Check.
		add_action( 'wp_ajax_host-tools-dns-check', array( '\\Host_Tools\\DNS_Checker', 'run_test' ) );
		add_action( 'wp_ajax_nopriv_host-tools-dns-check', array( '\\Host_Tools\\DNS_Checker', 'run_test' ) );

		// Ping & TTFB.
		add_action( 'wp_ajax_host-tools-ping-ttfb', array( '\\Host_Tools\\Ping_TTFB', 'run_test' ) );
		add_action( 'wp_ajax_nopriv_host-tools-ping-ttfb', array( '\\Host_Tools\\Ping_TTFB', 'run_test' ) );
	}

	/**
	 * Scripts & Styles.
	 */
	public static function scripts_styles() {
		global $post;

		$scripts_styles = '<script>var host_tools_ajax_url = \'' . admin_url( 'admin-ajax.php' ) . '\';</script>';

		if ( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'host-tools-dns-checkup' ) ) {
			ob_start();
			?>
			<script>
			( function( $ ) {
				$( document ).ready( function() {
					$( '#domainInput' ).focus();
				});

				$( '#host-tools-domain-form' ).on ( 'submit', function( e ) {
					e.preventDefault();

					$( '#host-test-results' ).html( '<p class="uk-text-primary">Please wait while we fetch the test results.</p>' );

					var data = {
							'domain': $( '#host-tools-domain-form #domainInput' ).val(),
							'action': 'host-tools-dns-check',
							'htnonce': $( '#host-tools-domain-form #htnonce' ).val(),
						};

					$.post( host_tools_ajax_url, data, function( r ) {
						if ( r.success ) {
							$( '#host-test-results' ).html( r.data );
						} else {
							$( '#host-test-results' ).html( '<p class="uk-text-danger">' + r.data +'</p>' );
						}
					});
				});
			} ( jQuery ) );
			</script>
			<?php
			$dns_check = ob_get_clean();

			$scripts_styles .= $dns_check;
		}

		if ( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'host-tools-ping-ttfb' ) ) {
			ob_start();
			?>
			<script>
			( function( $ ) {
				$( document ).ready( function() {
					$( '#domainInput' ).focus();
				});

				$( '#host-tools-domain-form' ).on ( 'submit', function( e ) {
					e.preventDefault();

					$( '#host-test-results' ).html( '<p class="uk-text-primary">Please wait while we fetch the test results.</p>' );

					var data = {
							'domain': $( '#host-tools-domain-form #domainInput' ).val(),
							'action': 'host-tools-ping-ttfb',
							'htnonce': $( '#host-tools-domain-form #htnonce' ).val(),
						};

					$.post( host_tools_ajax_url, data, function( r ) {
						if ( r.success ) {
							$( '#host-test-results' ).html( r.data );
						} else {
							$( '#host-test-results' ).html( '<p class="uk-text-danger">' + r.data +'</p>' );
						}
					});
				});
			} ( jQuery ) );
			</script>
			<?php
			$ping_ttfb = ob_get_clean();

			$scripts_styles .= $ping_ttfb;
		}

		echo $scripts_styles;
	}
}


