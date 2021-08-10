<?php
/**
 *
 * Class Vulnerable_Search
 *
 * This search is vulnerable in the following cases:
 *
 * @package Hacking
 */

namespace Hacking;

if ( ! defined( 'ABSPATH' ) ) {
	die( '' );
}

/**
 * Class Vulnerable_Search
 * this class implements a shortcode do bring a vulnerable search to frontend
 */
class Vulnerable_Search {

	/**
	 * Creates all needed shortcodes
	 */
	public function create_shortcodes() {
        add_shortcode( 'vulnerable_search', [$this, 'search_template'] );
	}

	public function search_template( $attributes ) {
	    $attributes = shortcode_atts( [
	        'last_queries' => 'false',
            'vulnerable_searched_query_text' => 'false',
            'store_vulnerable_search_phrase' => 'false',
            'show_vulnerable_search_list' => 'false'
        ], $attributes );

	    $vulnerable_searched_query_text = ($attributes['vulnerable_searched_query_text'] === 'true');
	    $show_last_queries = ($attributes['last_queries'] === 'true');
        $show_vulnerable_search_list = ($attributes['show_vulnerable_search_list'] === 'true');

	    $this->save_current_search(
            ($attributes['store_vulnerable_search_phrase'] === 'true')
        );

	    $html = '<form type="get" action="' . $this->get_current_url() . '">';
	    $html .= '<input type="text" value="" name="vul_search">';
	    $html .= '<button type="submit">Suchen</button>';
	    $html .= '</form>';
	    if (isset($_GET['vul_search'])) {
            $html .= $this->get_current_search_value_text($vulnerable_searched_query_text);
            $html .= $this->get_some_fake_results();
        }
	    $html .= $this->get_last_searched_list($show_vulnerable_search_list);
	    return $html;
    }

    private function get_current_search_value_text( $vulnerable ) {
	    $html = '<p>Du hast nach %s gesucht.</p>';
	    if ($vulnerable) {
            $html = str_replace('%s', $_GET['vul_search'], $html);
        } else {
            $html = str_replace('%s', htmlspecialchars($_GET['vul_search'], ENT_QUOTES), $html);
        }
	    return $html;
    }

    private function get_some_fake_results(){
	    $html = '<h3>Deine Suchergbenisse</h3><ul>';
	    for ($i = 0; $i < 6; $i++) {
	        $html .= '<li>Ein fake Suchergebnis #' . $i . '</li>';
        }
	    return $html . '</ul>';
    }

    private function save_current_search( $vulnerable ) {
        if (!isset($_GET['vul_search'])) return '';
        $ip = (isset($_SERVER['HTTP_CLIENT_IP'])) ? htmlspecialchars($_SERVER['HTTP_CLIENT_IP'], ENT_QUOTES) : 'XXX.XXX.XX.XX';

        $search_phrase = htmlspecialchars($_GET['vul_search']);
        if ($vulnerable) {
            $search_phrase = $_GET['vul_search'];
        }

        global $wpdb;
        $wpdb->insert(
            $wpdb->prefix . 'search',
            ['phrase' => $search_phrase, 'ip' => $ip],
            ['%s', '%s']
        );
    }

    private function get_last_searched_list( $vulnerable ) {
        global $wpdb;
        $searches = $wpdb->get_results(
            'SELECT * FROM ' . $wpdb->prefix . 'search ORDER BY id DESC LIMIT 10'
        );

        $html = '<h3>Die letzten Suchanfragen</h3><ul>';
        foreach ($searches as $search) {
            $search_string = htmlspecialchars($search->phrase, ENT_QUOTES);
            if ($vulnerable) {
                $search_string = $search->phrase;
            }
            $html .= '<li>' . $search_string. '<br><small>by ip ' . htmlspecialchars($search->ip, ENT_QUOTES) . '</small></li>';
        }
        if (count($searches) === 0) {
            $html .= '<li>Keine Suchanfragen vorhanden.</li>';
        }
        $html .= '</ul>';
        return $html;
	}

    private function get_current_url(){
        global $wp;
        return get_permalink(get_the_ID());
        return home_url(add_query_arg(array($_GET), $wp->request));
    }

}
