<?php //phpcs:ignore -- \r\n notice.

/**
 *
 * Plugin Name:       Host Tools
 * Description:       Various hosting tools.
 * Version:           1.0.0
 * Author:            Konstantinos Xenos
 * Author URI:        https://xkon.gr
 * License:           GPLv2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       host-tools
 * Domain Path:       /languages
 *
 * Copyright (C) 2019 Konstantinos Xenos (https://xkon.gr).
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see https://www.gnu.org/licenses/.
 */

namespace Host_Tools;

// Check that the file is not accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'We\'re sorry, but you can not directly access this file.' );
}

/**
 * Setup various constants.
 */
define( 'HOST_TOOLS_DIR', wp_normalize_path( dirname( __FILE__ ) ) . '/' );
define( 'HOST_TOOLS_URL', plugin_dir_url( __FILE__ ) );

/**
 * Autoload.
 */
spl_autoload_register(
	function( $class ) {
		$prefix = 'Host_Tools\\';
		$len    = strlen( $prefix );

		if ( 0 !== strncmp( $prefix, $class, $len ) ) {
			return;
		}

		$relative_class = substr( $class, $len );
		$path           = explode( '\\', strtolower( str_replace( '_', '-', $relative_class ) ) );
		$file           = array_pop( $path );
		$file           = HOST_TOOLS_DIR . 'classes/class-' . $file . '.php';

		if ( file_exists( $file ) ) {
			require $file;
		}
	}
);

/**
 * Load plugin.
 */
add_action( 'plugins_loaded', array( '\\Host_Tools\\Setup', 'get_instance' ) );
