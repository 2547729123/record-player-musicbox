<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function musicbox_register_settings() {
    $fields = [
        'musicbox_music_url'                 => '',
        'musicbox_enable_autoplay'          => '1',
        'musicbox_disable_hours'            => '48',
        'musicbox_only_homepage'            => '0',
        'musicbox_custom_icon'              => '',
        'musicbox_width'                    => '100',
        'musicbox_height'                   => '100',
        'musicbox_position_left'            => '-10',
        'musicbox_position_bottom'          => '-10',
        'musicbox_disable_progress_memory' => '0',
        'musicbox_disable_mobile_autoplay' => '1',
    ];

    // 注册默认选项和设置
    foreach ( $fields as $key => $default ) {
        add_option( $key, $default );
        register_setting( 'musicbox_options_group', $key, 'musicbox_sanitize_' . $key );
    }
}
add_action( 'admin_init', 'musicbox_register_settings' );

// 多行URL，逐行校验过滤
function musicbox_sanitize_musicbox_music_url( $value ) {
    $lines = explode("\n", $value);
    $clean_lines = [];

    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line)) continue;
        $clean_url = esc_url_raw($line);
        if (!empty($clean_url)) {
            $clean_lines[] = $clean_url;
        }
    }

    return implode("\n", $clean_lines);
}

function musicbox_sanitize_musicbox_enable_autoplay( $value ) {
    return $value === '1' ? '1' : '0';
}

function musicbox_sanitize_musicbox_disable_hours( $value ) {
    $int_val = intval($value);
    return $int_val > 0 ? $int_val : 48;  // 最小1小时，默认48
}

function musicbox_sanitize_musicbox_only_homepage( $value ) {
    return $value === '1' ? '1' : '0';
}

function musicbox_sanitize_musicbox_custom_icon( $value ) {
    return esc_url_raw( $value );
}

function musicbox_sanitize_musicbox_width( $value ) {
    $int_val = intval($value);
    return ($int_val >= 10) ? $int_val : 100;  // 尺寸不能小于10，默认100
}

function musicbox_sanitize_musicbox_height( $value ) {
    $int_val = intval($value);
    return ($int_val >= 10) ? $int_val : 100;
}

function musicbox_sanitize_musicbox_position_left( $value ) {
    return intval($value);
}

function musicbox_sanitize_musicbox_position_bottom( $value ) {
    return intval($value);
}

function musicbox_sanitize_musicbox_disable_progress_memory( $value ) {
    return $value === '1' ? '1' : '0';
}

function musicbox_sanitize_musicbox_disable_mobile_autoplay( $value ) {
    return $value === '1' ? '1' : '0';
}