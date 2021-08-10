<?php
/**
 * Plugin Name:       Hacking
 * Plugin URI:        https://tobier.de/
 * Description:       Creates some shortcodes to integrate some basic security vulnerabilities
 * Version:           0.1
 * Requires at least: 5.7
 * Author:            Tobier.de
 * Author URI:        https://tobier.de
 * Text Domain:       hacking_plugin
 * Domain Path:       /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
    die( '' );
}

define( 'HACKING_DIR', plugin_dir_path( __FILE__ ) );

require "vendor/autoload.php";

use Hacking\Vulnerable_Search;
use Hacking\SQL_Injection;
use Hacking\Admin_Page;

add_action( 'init', 'init_plugin' );
function init_plugin() {
    $admin_page = new Admin_Page();
    $vulnerable_search = new Vulnerable_Search();
    $vulnerable_search->create_shortcodes();
    $sql_injections = new SQL_Injection();
    $sql_injections->add_shortcodes();
}