<?php
/* 
 * Plugin Name: Colibri - Regenerate Partials
 * Author: ExtendThemes
 * Description: Go to Appearance > Colibri Regenerate Partials
 *
 * License: GPLv3 or later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.en.html
 * Version: 1.0
 */

namespace ExtendBuilder;

const REGEN_PARTIALS_ARGS = [
	'single' => [
		'title' => 'Post',
		'slug'  => 'main/post'
	],
	'archive' => [
		'title' => 'Arhive/Blog',
		'slug'  => 'main/archive'
	],
	'sidebar' => [
		'title' => 'Sidebar',
		'slug'  => 'sidebar/post'
	],
	'header' => [
		'title' => 'Header',
		'slug'  => 'post/header'
	],
	'footer' => [
		'title' => 'Footer',
		'slug'  => 'post/footer'
	],
	'search' => [
		'title' => 'Search',
		'slug'  => 'main/search'
	]
];

function regen_add_theme_page() {
	add_theme_page( 
		'Colibri Regenerate Partials', 
		'Colibri Regenerate Partials', 
		'edit_theme_options', 
		'colibri-regenerate-partials', 
		__NAMESPACE__ . '\\regen_page_output' 
	);
}
add_action( 'admin_menu', __NAMESPACE__ . '\\regen_add_theme_page' );

function regen_page_output() {
	?>
	<div class="wrap">
		<h1>Colibri Regenerate Partials</h1>
		<h3>Important</h3>
		<p><strong>Before you use this plugin, please make sure you have a full backup. If something goes wrong, you can always restore the backup</strong></p>
		<hr/>
		<p>After you click the "Regenerate" button, the page will refresh and an overlay will display. <strong>Please don't close or refresh the browser window/page until the overlay disappears.</strong></p>
		<form method="POST" action="<?php echo esc_url( admin_url( 'admin.php' ) ); ?>">

			<input type="hidden" name="action" value="colibri_regen_partials" />

			<p>
			<select name="colibri-regenerate-action" id="colibri-regenerate">
				<?php 
					foreach ( REGEN_PARTIALS_ARGS as $partial => $args ) {
						printf( '<option value="%1$s">%2$s</option>', $partial, $args[ 'title' ] );
					}
				?>
			</select>
			</p>

			<p>
			<input type="submit" value="Regenerate" class="button button-primary">
			</p>

		</form>
	</div>
	<?php
}

function regen_partials_admin_action() {
	$partials = REGEN_PARTIALS_ARGS;

	if ( ! array_key_exists( $_REQUEST['colibri-regenerate-action'], $partials ) ) {
		wp_redirect( admin_url() );
		exit();
	}

	$slug = $partials[ $_REQUEST['colibri-regenerate-action'] ][ 'slug' ];

	Import::unset_default_as_imported( $slug );
	wp_redirect( admin_url( '/?colibri-regenerate-render=1' ) );

	exit();
}
add_action( 'admin_action_colibri_regen_partials', __NAMESPACE__ . '\\regen_partials_admin_action' );

add_action( 'admin_init', function() {
	if ( isset( $_GET[ 'colibri-regenerate-render' ] ) && colibri_user_can_customize() ) {
		Regenerate::schedule();
	}
} );