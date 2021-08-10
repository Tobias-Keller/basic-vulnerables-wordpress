<?php
/**
 * Admin Page Template
 *
 * @package Hacking
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '' );
}

if ( ! current_user_can( 'manage_options' ) ) {
	return;
}

  $default_tab = false;
  $hacking_tab = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : $default_tab;
?>

<div class="wrap">

	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

	<nav class="nav-tab-wrapper">
		<a href="?page=hacking" class="nav-tab 
		<?php
		if ( ! $hacking_tab ) :
			?>
			nav-tab-active<?php endif; ?>">SQL Injection</a>
		<a href="?page=hacking&tab=xss" class="nav-tab 
		<?php
		if ( 'xss' === $hacking_tab ) :
			?>
			nav-tab-active<?php endif; ?>">XSS</a>
	</nav>

	<div class="tab-content">

		<?php if ( 'xss' === $hacking_tab ) : ?>
			<h3>XSS</h3>
			<form method="post" action="<?php echo esc_html( admin_url( 'admin-post.php' ) ); ?>">
				<label><?php echo esc_html( __( 'Creates a databse to store the search phrases', 'hacking_plugin' ) ); ?></label>
				<input type="hidden" name="action" value="hacking_create_table_search">
				<?php
				wp_nonce_field( 'hacking_create_table_search', 'hacking_create_table_search_message' );
				submit_button(
					__( 'Create Table for the search phrases', 'hacking_plugin' ),
					'primary',
					'hacking_create_table_search'
				);
				?>
			</form>

			<?php
			if ( isset( $_GET['_wpnonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'hacking' ) ) :
				if ( isset( $_GET['status'] ) && 'success' === $_GET['status'] ) :
					?>
					<p><?php echo esc_html( __( 'Created database table', 'hacking_plugin' ) ); ?></p>
				<?php endif; ?>
			<?php endif; ?>

		<?php else : ?>
			<h3>SQL Injection</h3>
			<p>Settings for all SQL-Injection Stuff</p>
			<form method="post" action="<?php echo esc_html( admin_url( 'admin-post.php' ) ); ?>">
				<label><?php echo esc_html( __( 'Creates a test database table with some data for the sql injection.', 'hacking_plugin' ) ); ?></label>
				<input type="hidden" name="action" value="hacking_create_table">
				<?php
				wp_nonce_field( 'hacking_create_table', 'hacking_create_table_message' );
				submit_button(
					__( 'Create Table with test data', 'hacking_plugin' ),
					'primary',
					'hacking_create_table'
				);
				?>
			</form>

			<?php
			if ( isset( $_GET['_wpnonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'hacking' ) ) :
				if ( isset( $_GET['status'] ) && 'success' === $_GET['status'] ) :
					?>
					<p><?php echo esc_html( __( 'Created database table', 'hacking_plugin' ) ); ?></p>
				<?php endif; ?>
			<?php endif; ?>

		<?php endif; ?>

	</div>
</div>
