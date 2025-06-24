<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function musicbox_register_menu() {
    add_options_page(
        __('唱片播放器设置', 'record-player-musicbox'),  // 本地化字符串
        __('唱片播放器设置', 'record-player-musicbox'),  // 本地化字符串
        'manage_options',
        'musicbox-settings',
        'musicbox_settings_page'
    );
}
add_action( 'admin_menu', 'musicbox_register_menu' );
