<?php

// Navigation.
add_action(
	'init',
	function() {
		$locations = array(
			'primary' => __( 'Primary', 'host-tools' ),
		);

		register_nav_menus( $locations );
	}
);

// Theme support.
add_action(
	'after_setup_theme',
	function() {
		load_theme_textdomain( 'host-tools' );
	}
);

// Menu active class.
add_filter(
	'nav_menu_css_class',
	function( $classes, $item ) {
		if ( in_array( 'current-menu-item', $classes, true ) ) {
			$classes[] = ' uk-active ';
		}
		return $classes;
	},
	10,
	2
);

// Add UIKIT Script & Style.
wp_enqueue_script(
	'uikit',
	'https://cdn.jsdelivr.net/npm/uikit@3.5.4/dist/js/uikit.min.js',
	array( 'jquery' ),
	'3.5.4',
	true
);

wp_enqueue_script(
	'uikit-icons',
	'https://cdn.jsdelivr.net/npm/uikit@3.5.4/dist/js/uikit-icons.min.js',
	array( 'jquery' ),
	'3.5.4',
	true
);

wp_enqueue_style(
	'uikit',
	'https://cdn.jsdelivr.net/npm/uikit@3.5.4/dist/css/uikit.min.css',
	false,
	'3.5.4',
	'all'
);

