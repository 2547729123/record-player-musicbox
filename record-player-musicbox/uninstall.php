<?php
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// 删除所有插件设置项
$options = [
    'musicbox_music_url',
    'musicbox_enable_autoplay',
    'musicbox_disable_hours',
    'musicbox_only_homepage',
    'musicbox_custom_icon',
    'musicbox_width',
    'musicbox_height',
    'musicbox_position_left',
    'musicbox_position_bottom',
    'musicbox_disable_progress_memory',
    'musicbox_disable_mobile_autoplay',
];

foreach ($options as $option) {
    delete_option($option);
}
