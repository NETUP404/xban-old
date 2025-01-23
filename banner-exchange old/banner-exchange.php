<?php
/*
Plugin Name: Banner Exchange System
Description: Sistema de intercambio de banners para WordPress.
Version: 1.0
Author: Tu Nombre
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// Incluir archivos necesarios
include_once plugin_dir_path( __FILE__ ) . 'includes/banner-handler.php';
include_once plugin_dir_path( __FILE__ ) . 'admin/admin-panel.php';
include_once plugin_dir_path( __FILE__ ) . 'includes/user-panel.php';

// Activar el plugin
function bes_activate() {
    bes_create_db();
}
register_activation_hook( __FILE__, 'bes_activate' );

// Desactivar el plugin
function bes_deactivate() {
    // Limpieza si es necesario
}
register_deactivation_hook( __FILE__, 'bes_deactivate' );

// Crear la base de datos
function bes_create_db() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'bes_banners';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        user_id bigint(20) NOT NULL,
        banner_url varchar(255) NOT NULL,
        target_url varchar(255) NOT NULL,
        impressions int(11) DEFAULT 0 NOT NULL,
        clicks int(11) DEFAULT 0 NOT NULL,
        credits int(11) DEFAULT 0 NOT NULL,
        approved tinyint(1) DEFAULT 0 NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
}

// Registrar el shortcode para el formulario de envío de banners
function bes_register_shortcodes() {
    add_shortcode( 'bes_submit_banner', 'bes_display_submit_banner_form' );
}
add_action( 'init', 'bes_register_shortcodes' );