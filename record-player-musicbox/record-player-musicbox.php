<?php
/*
 * Plugin Name: record player musicbox 唱片播放器
 * Plugin URI: https://github.com/2547729123/record-player-musicbox
 * Description: 复古风的唱片音乐播放器，支持自动播放、禁播、位置自定义、进度记忆开关等功能，后台支持分组标签设置。
 * Version: 1.0.0
 * Author: 码铃薯
 * Author URI: https://www.tudoucode.cn
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: record-player-musicbox
 * Domain Path: /languages/
 * Requires at least: 5.0
 * Tested up to: 6.8
 * Requires PHP: 7.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// 引入各个模块
require_once plugin_dir_path( __FILE__ ) . 'includes/settings-register.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/admin-menu.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/settings-page.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/frontend-output.php';

// 插件列表页“设置”和“Pro版”链接
add_filter('plugin_action_links_' . plugin_basename(__FILE__), function ($links) {
    $settings_url = esc_url(admin_url('options-general.php?page=musicbox-settings'));
    $settings_link = '<a href="' . $settings_url . '">' . esc_html__('设置', 'record-player-musicbox') . '</a>';
    $pro_url = esc_url('https://www.tudoucode.cn/plugins/wp-plugins/record-player-musicbox/');
    $pro_link = '<a href="' . $pro_url . '" target="_blank" style="color:#d54e21;">' . esc_html__('Pro版', 'record-player-musicbox') . '</a>';
    array_unshift($links, $settings_link);
    $links[] = $pro_link;
    return $links;
});
