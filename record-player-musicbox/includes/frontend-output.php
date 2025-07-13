<?php
if ( ! defined( 'ABSPATH' ) ) exit;

add_action('wp_footer', 'musicbox_player_output');
add_action('wp_enqueue_scripts', 'musicbox_enqueue_assets');

function musicbox_enqueue_assets() {
    // 仅首页显示时，非首页不加载资源
    if ( get_option('musicbox_only_homepage', '0') === '1' && ! ( is_home() || is_front_page() ) ) {
        return;
    }

    // 正确获取插件根目录URL，确保assets目录路径无误
    $plugin_url = plugin_dir_url( dirname(__FILE__) ) . 'assets/';

    wp_enqueue_style('musicbox-style', $plugin_url . 'musicbox.css', [], '1.0');
    wp_enqueue_script('musicbox-script', $plugin_url . 'musicbox.js', [], '1.0', true);

    // 处理音乐URL字符串，避免空行
    $music_urls_raw = get_option('musicbox_music_url', '');
    $music_urls = array_filter(array_map('trim', explode("\n", $music_urls_raw)));

    wp_localize_script('musicbox-script', 'musicboxData', [
        'musicUrls' => $music_urls,
        'icon' => esc_url( get_option('musicbox_custom_icon') ?: plugins_url('assets/img/logo.png', dirname(__FILE__)) ),
        'width' => intval( get_option('musicbox_width', 100) ),
        'height' => intval( get_option('musicbox_height', 100) ),
        'left' => intval( get_option('musicbox_position_left', -10) ),
        'bottom' => intval( get_option('musicbox_position_bottom', -10) ),
        'autoplay' => get_option('musicbox_enable_autoplay', '0') === '1',
        'disableHours' => intval( get_option('musicbox_disable_hours', 48) ),
        'disableProgressMemory' => get_option('musicbox_disable_progress_memory', '0') === '1',
        'disableMobileAutoplay' => get_option('musicbox_disable_mobile_autoplay', '0') === '1',
    ]);
}

function musicbox_player_output() {
    static $output_done = false;
    if ( $output_done ) return;
    $output_done = true;

    echo "<div id='record-player'><div class='player-box'><div id='record'></div></div></div><audio id='bg-music' preload='auto' style='display:none;'></audio>";
}