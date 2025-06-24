<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function musicbox_register_settings() {
    $fields = [
        'musicbox_music_url'                 => '',
        'musicbox_enable_autoplay'          => '1',
        'musicbox_disable_hours'            => '48',
        'musicbox_only_homepage'            => '0',
        'musicbox_custom_icon'              => plugin_dir_url(__FILE__) . 'assets/img/logo.png',
        'musicbox_width'                    => '100',
        'musicbox_height'                   => '100',
        'musicbox_position_left'            => '-10',
        'musicbox_position_bottom'          => '-10',
        'musicbox_disable_progress_memory'  => '0',
        'musicbox_disable_mobile_autoplay'  => '1',
    ];

    foreach ( $fields as $key => $default ) {
        add_option( $key, $default );
        register_setting( 'musicbox_options_group', $key, 'musicbox_sanitize_' . $key );
    }
}
add_action( 'admin_init', 'musicbox_register_settings' );

// 数据清理函数
function musicbox_sanitize_musicbox_music_url( $value ) {
    return esc_url_raw( $value );
}

function musicbox_sanitize_musicbox_enable_autoplay( $value ) {
    return absint( $value );
}

function musicbox_sanitize_musicbox_disable_hours( $value ) {
    return absint( $value );
}

function musicbox_sanitize_musicbox_only_homepage( $value ) {
    return absint( $value );
}

function musicbox_sanitize_musicbox_custom_icon( $value ) {
    return esc_url_raw( $value );
}

function musicbox_sanitize_musicbox_width( $value ) {
    return absint( $value );
}

function musicbox_sanitize_musicbox_height( $value ) {
    return absint( $value );
}

function musicbox_sanitize_musicbox_position_left( $value ) {
    return sanitize_text_field( $value );
}

function musicbox_sanitize_musicbox_position_bottom( $value ) {
    return sanitize_text_field( $value );
}

function musicbox_sanitize_musicbox_disable_progress_memory( $value ) {
    return absint( $value );
}

function musicbox_sanitize_musicbox_disable_mobile_autoplay( $value ) {
    return absint( $value );
}
