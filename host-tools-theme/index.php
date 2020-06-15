<!doctype html>

<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0" >
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

	<?php wp_body_open(); ?>

	<div class="uk-container">
		<?php
		wp_nav_menu(
			array(
				'theme_location' => 'primary',
				'items_wrap'     => '<nav class="uk-navbar-container" uk-navbar><div class="uk-navbar-left"><ul id="%1$s" class="uk-navbar-nav %2$s">%3$s</ul></div></div>',
			)
		);
		?>
	</div>

	<div class="uk-container">
		<?php
		if ( have_posts() ) {
			while ( have_posts() ) {
				the_post();

				echo '<h2 class="uk-heading-line uk-text-center"><span>';
				the_title();
				echo '</span></h2>';

				echo '<div class="entry">';
				the_content();
				echo '</div>';
			}
		} else {
			echo '<p>';
			esc_html_e( 'Sorry, no posts matched your criteria.' );
			echo '</p>';
		}
		?>
	</div>

	<?php wp_footer(); ?>
</body>
</html>
