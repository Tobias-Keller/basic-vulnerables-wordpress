<?php
/**
 *
 * Class Admin_Page
 *
 * Creates the Admin Page and adds some logic to create a database
 *
 * @package Hacking
 */

namespace Hacking;

/**
 * Class Admin_Page
 *
 * @package Hacking
 */
class Admin_Page {

	/**
	 * Admin_Page constructor.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'create_admin_page' ) );
		add_action( 'admin_post_hacking_create_table', array( $this, 'create_table_endpoint' ) );
		add_action( 'admin_post_hacking_create_table_search', array( $this, 'create_search_table_endpoint' ) );
	}

	/**
	 * Handles the form post request.
	 */
	public function create_table_endpoint() {
		if ( ! empty( $_POST['_wp_http_referer'] ) ) {
			$form_url = esc_url_raw( wp_unslash( $_POST['_wp_http_referer'] ) );
			$form_url = add_query_arg( '_wpnonce', wp_create_nonce( 'hacking' ), $form_url );
		} else {
			$form_url = home_url( '/' );
		}

		if ( ! isset( $_POST['hacking_create_table_message'] ) ) {
			wp_safe_redirect(
				esc_url_raw(
					add_query_arg( 'status', 'nononce', $form_url )
				)
			);
			exit();
		}

		$nonce = sanitize_key( wp_unslash( $_POST['hacking_create_table_message'] ) );

		if ( ! wp_verify_nonce( $nonce, 'hacking_create_table' ) ) {
			wp_safe_redirect(
				esc_url_raw(
					add_query_arg( 'status', 'error', $form_url )
				)
			);
			exit();
		}

		$this->create_table();
		wp_safe_redirect(
			esc_url_raw(
				add_query_arg( 'status', 'success', $form_url )
			)
		);
		exit();
	}

	/**
	 * Handles the form post request.
	 */
	public function create_search_table_endpoint() {
		if ( ! empty( $_POST['_wp_http_referer'] ) ) {
			$form_url = esc_url_raw( wp_unslash( $_POST['_wp_http_referer'] ) );
			$form_url = add_query_arg( '_wpnonce', wp_create_nonce( 'hacking' ), $form_url );
		} else {
			$form_url = home_url( '/' );
		}

		if ( ! isset( $_POST['hacking_create_table_search_message'] ) ) {
			wp_safe_redirect(
				esc_url_raw(
					add_query_arg( 'status', 'nononce', $form_url )
				)
			);
			exit();
		}

		$nonce = sanitize_key( wp_unslash( $_POST['hacking_create_table_search_message'] ) );

		if ( ! wp_verify_nonce( $nonce, 'hacking_create_table_search' ) ) {
			wp_safe_redirect(
				esc_url_raw(
					add_query_arg( 'status', 'error', $form_url )
				)
			);
			exit();
		}

		$this->create_search_table();
		wp_safe_redirect(
			esc_url_raw(
				add_query_arg( 'status', 'success', $form_url )
			)
		);
		exit();
	}

	/**
	 * Creates a test database table.
	 */
	public function create_table() {
		global $wpdb;
		$table_name      = $wpdb->prefix . 'inject';
		$charset_collate = $wpdb->get_charset_collate();
		$sql             = "CREATE TABLE $table_name (
              id mediumint(9) NOT NULL AUTO_INCREMENT,
              time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
              name tinytext NOT NULL,
              text text NOT NULL,
              url varchar(55) DEFAULT '' NOT NULL,
              PRIMARY KEY  (id)
            ) $charset_collate;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		maybe_create_table( $table_name, $sql );
		$this->add_data();
	}

	/**
	 * Creates the table for the search phrases.
	 */
	public function create_search_table() {
		global $wpdb;
		$table_name      = $wpdb->prefix . 'search';
		$charset_collate = $wpdb->get_charset_collate();
		$sql             = "CREATE TABLE $table_name (
              id mediumint(9) NOT NULL AUTO_INCREMENT,
              phrase text NOT NULL,
              ip text NOT NULL,
              PRIMARY KEY  (id)
            ) $charset_collate;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		maybe_create_table( $table_name, $sql );
		$this->add_data();
	}

	/**
	 * Adds some basic data to our Table.
	 */
	public function add_data() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'inject';
		for ( $i = 0; $i < 11; $i++ ) {
			$welcome_name = 'Mr. WordPress ' . $i;
			$welcome_text = 'Congratulations, you just completed the installation!';
			$url          = 'https://lookfamed.de';
			$wpdb->insert(
				$table_name,
				array(
					'time' => current_time( 'mysql' ),
					'name' => $welcome_name,
					'text' => $welcome_text,
					'url'  => $url,
				)
			);
		}
	}

	/**
	 * Creates the Admin Page for our form.
	 */
	public function create_admin_page() {
		\add_menu_page(
			'Hacking',
			'Hacking',
			'manage_options',
			'hacking',
			array( $this, 'page_content' )
		);
	}

	/**
	 * Loads our page template.
	 */
	public function page_content() {
		require_once HACKING_DIR . '/templates/admin.php';
	}

}
