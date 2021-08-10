<?php
/**
 * This Class adds shortcodes to display some vulnerable sql injections
 *
 * @package Hacking
 */

namespace Hacking;

/**
 * Class SQL_Injection
 *
 * @package Hacking
 */
class SQL_Injection {

	/**
	 * Register WordPress shortcodes.
	 */
	public function add_shortcodes() {
		add_shortcode( 'vulnerable_list', array( $this, 'vulnerable_get' ) );
		add_shortcode( 'vulnerable_post', array( $this, 'vulnerable_post' ) );
	}

	/**
	 * Shortcode handler to print a list from database with vulnerable get variable.
	 *
	 * @param object $attributes Shortcode attributes.
	 * @return string
	 */
	public function vulnerable_get( $attributes ) {
		$attributes = shortcode_atts(
			array(
				'vulnerable' => 'false',
			),
			$attributes
		);

		$vulnerable = ( 'true' === $attributes['vulnerable'] );

		global $wpdb;
		$id = '';
		if ( isset( $_GET['id'] ) ) {
			if ( $vulnerable ) {
                // phpcs:ignore
				$id = 'WHERE id = ' . wp_unslash( $_GET['id'] );
			} else {
				$id = $wpdb->prepare( 'WHERE id = %d', sanitize_text_field( wp_unslash( $_GET['id'] ) ) );
			}
		}
		$query = 'SELECT * FROM ' . $wpdb->prefix . 'inject ' . $id;
        // phpcs:ignore
		$data  = $wpdb->get_results( $query  );

		$html = '<ul>';
		foreach ( $data as $object ) {
			$html .= '<li>' . $object->name . '<br> on ' . $object->time . '</li>';
		}
		$html .= '</ul>';
		$html .= '<pre>' . $query . '</pre>';
		return $html;
	}

	/**
	 * Shortcode handler to print a list from database with vulnerable input field.
	 *
	 * @param object $attributes Shortcode attributes.
	 * @return string
	 */
	public function vulnerable_post( $attributes ) {
		$attributes = shortcode_atts(
			array(
				'vulnerable' => 'false',
			),
			$attributes
		);

		$vulnerable = ( 'true' === $attributes['vulnerable'] );

		global $wpdb;
		$id = '';
		if ( isset( $_POST['id'] ) ) {
			if ( $vulnerable ) {
                // phpcs:ignore
				$id = 'WHERE id = ' . wp_unslash( $_POST['id'] );
			} else {
				$id = $wpdb->prepare( 'WHERE id = %d', sanitize_text_field( wp_unslash( $_POST['id'] ) ) );
			}
		}
		$query = 'SELECT * FROM ' . $wpdb->prefix . 'inject ' . $id;
        // phpcs:ignore
		$data = $wpdb->get_results($query );

		$html = '';
		if ( isset( $_POST['id'] ) ) {
			$html .= '<p>Zeige Eintrags-ID: ' . esc_html( sanitize_text_field( wp_unslash( $_POST['id'] ) ) ) . '</p>';
		}

		$html .= '<form method="post">';
		$html .= '<input type="number" name="id">';
		$html .= '<button type="submit">' . __( 'Get by id', 'hacking_plugin' ) . '</button>';
		$html .= '</form>';

		$html .= '<ul>';
		foreach ( $data as $object ) {
			$html .= '<li>ID:' . esc_html( $object->id ) . '<br>' . esc_html( $object->name ) . '<br> on ' . esc_html( $object->time ) . '</li>';
		}
		$html .= '</ul>';
		$html .= '<pre>' . $query . '</pre>';
		return $html;
	}

}
