<?php
/**
 * Plugin Name: 唱片播放器 MusicBox 修复版
 * Description: 仿网易邮箱的唱片音乐播放器，支持自动播放、禁播、位置自定义、进度记忆等功能，后台支持分组标签设置。
 * Version: 1.5.1
 * Author: 码铃薯
 */

if (!defined('ABSPATH')) exit;

// 注册后台设置
function musicbox_register_settings() {
    $fields = [
        'musicbox_music_url'        => 'https://mp3.52yzk.com/rand-music.php',
        'musicbox_enable_autoplay' => '1',
        'musicbox_disable_hours'   => '48',
        'musicbox_only_homepage'   => '0',
        'musicbox_custom_icon'     => '/wp-content/plugins/record-player-musicbox/Musicbox/music2.png',
        'musicbox_width'           => '100',
        'musicbox_height'          => '100',
        'musicbox_position_left'   => '-10',
        'musicbox_position_bottom' => '-10',
    ];
    foreach ($fields as $key => $default) {
        add_option($key, $default);
        register_setting('musicbox_options_group', $key);
    }
}
add_action('admin_init', 'musicbox_register_settings');

// 菜单
function musicbox_register_menu() {
    add_options_page('唱片播放器设置', '唱片播放器设置', 'manage_options', 'musicbox-settings', 'musicbox_settings_page');
}
add_action('admin_menu', 'musicbox_register_menu');

// 设置页面带标签美化
function musicbox_settings_page() {
    ?>
    <div class="wrap">
        <h2>唱片播放器设置</h2>
        <h2 class="nav-tab-wrapper">
            <a href="#" class="nav-tab nav-tab-active" data-tab="tab1">🎵 播放设置</a>
            <a href="#" class="nav-tab" data-tab="tab2">🎨 样式设置</a>
        </h2>

        <form method="post" action="options.php">
            <?php settings_fields('musicbox_options_group'); ?>

            <div id="tab1" class="musicbox-tab" style="display: block;">
                <table class="form-table">
                    <tr>
                        <th scope="row">音乐源地址</th>
                        <td>
                            <textarea name="musicbox_music_url" rows="5" cols="50"><?php echo esc_textarea(get_option('musicbox_music_url')); ?></textarea>
                            <p class="description">每行一个音乐源 URL，例如：https://xxx.com/xxx.mp3</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">启用首页自动播放</th>
                        <td><label><input type="checkbox" name="musicbox_enable_autoplay" value="1" <?php checked(get_option('musicbox_enable_autoplay'), '1'); ?>> 启用</label></td>
                    </tr>
                    <tr>
                        <th scope="row">禁播时长（小时）</th>
                        <td><input type="number" name="musicbox_disable_hours" value="<?php echo esc_attr(get_option('musicbox_disable_hours')); ?>" min="1"></td>
                    </tr>
                    <tr>
                        <th scope="row">仅在首页显示播放器</th>
                        <td><label><input type="checkbox" name="musicbox_only_homepage" value="1" <?php checked(get_option('musicbox_only_homepage'), '1'); ?>> 启用</label></td>
                    </tr>
                </table>
            </div>

            <div id="tab2" class="musicbox-tab" style="display: none;">
                <table class="form-table">
                    <tr>
                        <th scope="row">唱盘图标 URL</th>
                        <td><input type="text" name="musicbox_custom_icon" value="<?php echo esc_url(get_option('musicbox_custom_icon')); ?>" size="60"></td>
                    </tr>
                    <tr>
                        <th scope="row">播放器尺寸（px）</th>
                        <td>
                            宽：<input type="number" name="musicbox_width" value="<?php echo esc_attr(get_option('musicbox_width')); ?>" style="width:80px;">　
                            高：<input type="number" name="musicbox_height" value="<?php echo esc_attr(get_option('musicbox_height')); ?>" style="width:80px;">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">初始位置（left / bottom）</th>
                        <td>
                            Left：<input type="number" name="musicbox_position_left" value="<?php echo esc_attr(get_option('musicbox_position_left')); ?>" style="width:80px;">　
                            Bottom：<input type="number" name="musicbox_position_bottom" value="<?php echo esc_attr(get_option('musicbox_position_bottom')); ?>" style="width:80px;">
                        </td>
                    </tr>
                </table>
            </div>

            <?php submit_button(); ?>
        </form>
    </div>

    <script>
    document.querySelectorAll('.nav-tab').forEach(tab => {
        tab.addEventListener('click', e => {
            e.preventDefault();
            document.querySelectorAll('.nav-tab').forEach(t => t.classList.remove('nav-tab-active'));
            document.querySelectorAll('.musicbox-tab').forEach(c => c.style.display = 'none');
            tab.classList.add('nav-tab-active');
            document.getElementById(tab.dataset.tab).style.display = 'block';
        });
    });
    </script>
    <?php
}

// 输出播放器（已封装为单独函数）
add_action('wp_footer', function () {
    if (get_option('musicbox_only_homepage', '0') === '1' && !(is_home() || is_front_page())) return;
    musicbox_player_output();
});

// 播放器输出函数（功能完整）
function musicbox_player_output() {
    $urls = array_filter(array_map('trim', explode("\n", get_option('musicbox_music_url'))));
    if (!$urls) return;

    $music_url = esc_url($urls[array_rand($urls)]);
    $icon = esc_url(get_option('musicbox_custom_icon'));
    $width = intval(get_option('musicbox_width', 100));
    $height = intval(get_option('musicbox_height', 100));
    $left = intval(get_option('musicbox_position_left', -10));
    $bottom = intval(get_option('musicbox_position_bottom', -10));
    $autoplay = get_option('musicbox_enable_autoplay', '1') === '1';
    $disable_hours = intval(get_option('musicbox_disable_hours', 48));

    echo "
    <style>
    #record-player {
        position: fixed;
        bottom: {$bottom}px;
        left: {$left}px;
        width: {$width}px;
        height: {$height}px;
        z-index: 9999;
        cursor: move;
    }
    #record {
        position: absolute;
        width: 40px;
        height: 40px;
        top: 30px;
        left: 30px;
        background-image: url('{$icon}');
        background-size: cover;
        border-radius: 50%;
        box-shadow: 0 8px 15px rgba(0,0,0,0.3);
        cursor: pointer;
        transition: transform 0.3s ease;
    }
    #record.rotating {
        animation: spin 4s linear infinite, rainbowEffect 4s linear infinite, breathing 5s ease-in-out infinite;
    }
    @keyframes spin { 0% {transform:rotate(0);} 100% {transform:rotate(360deg);} }
    @keyframes rainbowEffect { 0% {filter: hue-rotate(0);} 100% {filter: hue-rotate(360deg);} }
    @keyframes breathing { 0%, 100% {transform: scale(1);} 50% {transform: scale(1.15);} }
    @media screen and (max-width:768px) { #record-player { display: none; } }
    </style>

    <div id='record-player'><div class='player-box'><div id='record' title='🎵 双击禁播 {$disable_hours} 小时'></div></div></div>
    <audio id='bg-music' preload='auto' style='display:none;'><source src='{$music_url}' type='audio/mpeg'></audio>

    <script>
    const record = document.getElementById('record');
    const music = document.getElementById('bg-music');
    const musicUrls = " . json_encode($urls) . ";
    const autoplayKey = 'musicbox_is_playing';
    const disableKey = 'musicbox_disable_autoplay_until';
    const progressKey = 'musicbox_last_position';
    let isPlaying = false;

    const now = Date.now();
    const disableUntil = parseInt(localStorage.getItem(disableKey)) || 0;

    if ($autoplay && location.pathname === '/' && now > disableUntil && localStorage.getItem(autoplayKey) !== 'true') {
        music.play().then(() => {
            record.classList.add('rotating');
            isPlaying = true;
            localStorage.setItem(autoplayKey, 'true');
        }).catch(() => {});
    }

    const lastTime = parseFloat(localStorage.getItem(progressKey));
    if (!isNaN(lastTime)) music.currentTime = lastTime;

    record.addEventListener('click', () => {
        if (!isPlaying) {
            music.play(); record.classList.add('rotating');
            localStorage.setItem(autoplayKey, 'true');
        } else {
            music.pause(); record.classList.remove('rotating');
            localStorage.setItem(autoplayKey, 'false');
        }
        isPlaying = !isPlaying;
    });

    record.addEventListener('dblclick', () => {
        localStorage.setItem(disableKey, (Date.now() + {$disable_hours} * 3600 * 1000).toString());
        alert('🎧 播放器将在未来 {$disable_hours} 小时内不再自动播放');
    });

    music.addEventListener('ended', () => {
        const next = musicUrls[Math.floor(Math.random() * musicUrls.length)];
        music.src = next; music.load(); music.play();
    });

    setInterval(() => {
        if (!isNaN(music.currentTime)) {
            localStorage.setItem(progressKey, music.currentTime);
        }
    }, 10000);

    // 拖动功能
    const player = document.getElementById('record-player');
    let offsetX = 0, offsetY = 0, dragging = false;
    player.addEventListener('mousedown', e => {
        dragging = true;
        offsetX = e.clientX - player.getBoundingClientRect().left;
        offsetY = e.clientY - player.getBoundingClientRect().top;
        document.body.style.userSelect = 'none';
    });
    document.addEventListener('mousemove', e => {
        if (dragging) {
            player.style.left = (e.clientX - offsetX) + 'px';
            player.style.top = (e.clientY - offsetY) + 'px';
        }
    });
    document.addEventListener('mouseup', () => {
        dragging = false;
        document.body.style.userSelect = '';
    });
    </script>";
}
