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
		add_shortcode( 'host-tools-csr-decoder', array( '\\Host_Tools\\CSR_Decoder', 'shortcode' ) );

		// General Scripts & Styles.
		add_action( 'wp_footer', array( '\\Host_Tools\\Setup', 'scripts_styles' ), 999 );

		// DNS Check.
		add_action( 'wp_ajax_host-tools-dns-check', array( '\\Host_Tools\\DNS_Checker', 'run_test' ) );
		add_action( 'wp_ajax_nopriv_host-tools-dns-check', array( '\\Host_Tools\\DNS_Checker', 'run_test' ) );

		// Ping & TTFB.
		add_action( 'wp_ajax_host-tools-ping-ttfb', array( '\\Host_Tools\\Ping_TTFB', 'run_test' ) );
		add_action( 'wp_ajax_nopriv_host-tools-ping-ttfb', array( '\\Host_Tools\\Ping_TTFB', 'run_test' ) );

		// CSR Decoder.
		add_action( 'wp_ajax_host-tools-csr-decoder', array( '\\Host_Tools\\CSR_Decoder', 'run_test' ) );
		add_action( 'wp_ajax_nopriv_host-tools-csr-decoder', array( '\\Host_Tools\\CSR_Decoder', 'run_test' ) );
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

				$( '#host-tools-dns-checker-form' ).on ( 'submit', function( e ) {
					e.preventDefault();

					$( '#host-test-results' ).html( '<p class="uk-text-primary">Please wait while we fetch the test results.</p>' );

					var data = {
							'domain': $( '#host-tools-dns-checker-form #domainInput' ).val(),
							'action': 'host-tools-dns-check',
							'htnonce': $( '#host-tools-dns-checker-form #htnonce' ).val(),
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

				$( '#host-tools-ping-ttfb-form' ).on ( 'submit', function( e ) {
					e.preventDefault();

					$( '#host-test-results' ).html( '<p class="uk-text-primary">Please wait while we fetch the test results.</p>' );

					var data = {
							'domain': $( '#host-tools-ping-ttfb-form #domainInput' ).val(),
							'action': 'host-tools-ping-ttfb',
							'htnonce': $( '#host-tools-ping-ttfb-form #htnonce' ).val(),
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

		if ( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'host-tools-csr-decoder' ) ) {
			ob_start();
			?>
			<script>
			( function( $ ) {
				$( document ).ready( function() {
					$( '#csrInput' ).focus();
				});

				$( '#host-tools-csr-decoder-form' ).on ( 'submit', function( e ) {
					e.preventDefault();

					$( '#host-test-results' ).html( '<p class="uk-text-primary">Please wait while we fetch the test results.</p>' );

					var data = {
							'csr': $( '#host-tools-csr-decoder-form #csrInput' ).val(),
							'action': 'host-tools-csr-decoder',
							'htnonce': $( '#host-tools-csr-decoder-form #htnonce' ).val(),
						};

					$.post( host_tools_ajax_url, data, function( r ) {
						if ( r.success ) {
							var body_string;

							body_string  = '<p><strong>Domain:</strong> ' + r.data.CN + '<br/>';
							body_string += '<p><strong>Organization:</strong> ' + r.data.O + '<br/>';
							body_string += '<p><strong>Department:</strong> ' + r.data.OU + '<br/>';
							body_string += '<p><strong>City:</strong> ' + r.data.L + '<br/>';
							body_string += '<p><strong>State:</strong> ' + r.data.ST + '<br/>';
							body_string += '<p><strong>Email:</strong> ' + r.data.emailAddress + '<br/>';
							body_string += '<p><strong>Country:</strong> ' + r.data.C + '<br/>';

							$( '#host-test-results' ).html( body_string );
						} else {
							$( '#host-test-results' ).html( '<p class="uk-text-danger">' + r.data +'</p>' );
						}
					});
				});
			} ( jQuery ) );
			</script>
			<?php
			$csr_decoder = ob_get_clean();

			$scripts_styles .= $csr_decoder;
		}

		echo $scripts_styles;
	}
}


