<?php
if ( ! defined( 'ABSPATH' ) ) exit;

add_action('wp_footer', 'musicbox_player_output');
add_action('wp_enqueue_scripts', 'musicbox_enqueue_assets');

function musicbox_enqueue_assets() {
    if ( get_option('musicbox_only_homepage', '0') === '1' && ! ( is_home() || is_front_page() ) ) return;

    $plugin_url = plugin_dir_url(__FILE__) . '../assets/';
    wp_enqueue_style('musicbox-style', $plugin_url . 'musicbox.css', [], '1.0');
    wp_enqueue_script('musicbox-script', $plugin_url . 'musicbox.js', [], '1.0', true);

    wp_localize_script('musicbox-script', 'musicboxData', [
        'musicUrls' => array_filter(array_map('trim', explode("\n", get_option('musicbox_music_url')))),
        'icon' => esc_url(get_option('musicbox_custom_icon')),
        'width' => intval(get_option('musicbox_width', 100)),
        'height' => intval(get_option('musicbox_height', 100)),
        'left' => intval(get_option('musicbox_position_left', -10)),
        'bottom' => intval(get_option('musicbox_position_bottom', -10)),
        'autoplay' => get_option('musicbox_enable_autoplay', '1') === '1',
        'disableHours' => intval(get_option('musicbox_disable_hours', 48)),
        'disableProgressMemory' => get_option('musicbox_disable_progress_memory', '0') === '1'
    ]);
}

function musicbox_player_output() {
    echo "<div id='record-player'><div class='player-box'><div id='record'></div></div></div><audio id='bg-music' preload='auto' style='display:none;'></audio>";
}