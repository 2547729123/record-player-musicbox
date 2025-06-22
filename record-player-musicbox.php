<?php
/**
 * Plugin Name: 唱片播放器 MusicBox
 * Description: 仿网易邮箱的唱片音乐播放器，支持自动播放、禁播、位置自定义、进度记忆开关等功能，后台支持分组标签设置。
 * Version: 1.6.3
 * Author: 码铃薯
 */

if (!defined('ABSPATH')) exit;

// 注册后台设置
function musicbox_register_settings() {
    $fields = [
        'musicbox_music_url'             => 'https://mp3.52yzk.com/rand-music.php',
        'musicbox_enable_autoplay'       => '1',
        'musicbox_disable_hours'         => '48',
        'musicbox_only_homepage'         => '0',
        'musicbox_custom_icon'           => '/wp-content/plugins/record-player-musicbox/Musicbox/music2.png',
        'musicbox_width'                 => '100',
        'musicbox_height'                => '100',
        'musicbox_position_left'         => '-10',
        'musicbox_position_bottom'       => '-10',
        'musicbox_disable_progress_memory' => '0',
    ];
    foreach ($fields as $key => $default) {
        add_option($key, $default);
        register_setting('musicbox_options_group', $key);
    }
}
add_action('admin_init', 'musicbox_register_settings');

// 后台菜单
function musicbox_register_menu() {
    add_options_page('唱片播放器设置', '唱片播放器设置', 'manage_options', 'musicbox-settings', 'musicbox_settings_page');
}
add_action('admin_menu', 'musicbox_register_menu');

// 后台设置页面
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
                        <th scope="row">禁止自动播放时长（小时）</th>
                        <td><input type="number" name="musicbox_disable_hours" value="<?php echo esc_attr(get_option('musicbox_disable_hours')); ?>" min="1"></td>
                    </tr>
                    <tr>
                        <th scope="row">仅在首页显示播放器</th>
                        <td><label><input type="checkbox" name="musicbox_only_homepage" value="1" <?php checked(get_option('musicbox_only_homepage'), '1'); ?>> 启用</label></td>
                    </tr>
                    <tr>
                        <th scope="row">关闭播放记忆</th>
                        <td><label><input type="checkbox" name="musicbox_disable_progress_memory" value="1" <?php checked(get_option('musicbox_disable_progress_memory'), '1'); ?>> 勾选后关闭播放进度记忆功能</label></td>
                    </tr>
                    <tr>
                        <th scope="row">一键清除禁播时段</th>
                        <td>
                            <button type="button" id="clear-disable-time" class="button button-secondary">清除禁播</button>
                        </td>
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

    document.getElementById('clear-disable-time').addEventListener('click', () => {
        localStorage.removeItem('musicbox_disable_autoplay_until');
        alert('✅ 已清除禁播限制！刷新后将可自动播放');
    });
    </script>
    <?php
}

// 前台输出播放器
add_action('wp_footer', function () {
    if (get_option('musicbox_only_homepage', '0') === '1' && !(is_home() || is_front_page())) return;
    musicbox_player_output();
});

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
    $disable_progress_memory = get_option('musicbox_disable_progress_memory', '0') === '1';

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
        user-select: none;
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

    <div id='record-player'><div class='player-box'><div id='record'></div></div></div>
    <audio id='bg-music' preload='auto' style='display:none;'><source src='{$music_url}' type='audio/mpeg'></audio>

    <script>
    const record = document.getElementById('record');
    const music = document.getElementById('bg-music');
    const musicUrls = " . json_encode($urls) . ";
    const autoplayKey = 'musicbox_is_playing';
    const disableKey = 'musicbox_disable_autoplay_until';
    const progressKey = 'musicbox_last_position';
    const disableProgressMemory = " . ($disable_progress_memory ? 'true' : 'false') . ";
    const enableAutoplay = " . ($autoplay ? 'true' : 'false') . ";

    const tabLockKey = 'musicbox_is_active_tab';
    const tabId = 'tab_' + Date.now() + '_' + Math.random().toString(36).substr(2);
    let isActiveTab = false;
    let isPlaying = false;

    function isHomePage() {
        const path = window.location.pathname;
        return path === '/' || path === '/index.php';
    }

    function updateTitle() {
        const now = Date.now();
        const disableUntil = parseInt(localStorage.getItem(disableKey)) || 0;
        if (now < disableUntil) {
            record.title = '已禁止自动播放，再次双击取消！';
        } else {
            record.title = '双击我，{$disable_hours}小时内不再自动播放';
        }
    }

    // 初始化提示
    updateTitle();

    function tryBecomeActiveTab() {
        const currentActive = localStorage.getItem(tabLockKey);
        if (!currentActive || currentActive === tabId) {
            localStorage.setItem(tabLockKey, tabId);
            isActiveTab = true;

            const now = Date.now();
            const disableUntil = parseInt(localStorage.getItem(disableKey)) || 0;

            if (enableAutoplay && isHomePage() && now > disableUntil) {
                music.muted = true;
                music.play().then(() => {
                    music.muted = false;
                    record.classList.add('rotating');
                    isPlaying = true;
                    localStorage.setItem(autoplayKey, 'true');
                }).catch(() => {});
            }
        }
    }

    window.addEventListener('storage', (event) => {
        if (event.key === tabLockKey && event.newValue !== tabId) {
            if (isActiveTab && isPlaying) {
                music.pause();
                record.classList.remove('rotating');
                isPlaying = false;
                isActiveTab = false;
            }
        }
    });

    window.addEventListener('beforeunload', () => {
        if (isActiveTab) {
            localStorage.removeItem(tabLockKey);
        }
    });

    tryBecomeActiveTab();

    if (!disableProgressMemory) {
        const lastTime = parseFloat(localStorage.getItem(progressKey));
        if (!isNaN(lastTime)) music.currentTime = lastTime;
    }

    record.addEventListener('click', () => {
        if (!isPlaying) {
            music.play();
            record.classList.add('rotating');
            localStorage.setItem(autoplayKey, 'true');
        } else {
            music.pause();
            record.classList.remove('rotating');
            localStorage.setItem(autoplayKey, 'false');
        }
        isPlaying = !isPlaying;
    });

    record.addEventListener('dblclick', () => {
        const now = Date.now();
        const disableUntil = parseInt(localStorage.getItem(disableKey)) || 0;

        if (now < disableUntil) {
            // 解除禁播
            localStorage.removeItem(disableKey);
            alert('✅ 播放器禁止自动播放已解除，刷新后将可自动播放');
        } else {
            // 设置禁播
            localStorage.setItem(disableKey, (now + {$disable_hours} * 3600 * 1000).toString());
            alert('🎧 播放器将在未来 {$disable_hours} 小时内不再自动播放');
        }
        updateTitle();
    });

    music.addEventListener('ended', () => {
        const next = musicUrls[Math.floor(Math.random() * musicUrls.length)];
        music.src = next;
        music.load();
        music.play();
    });

    if (!disableProgressMemory) {
        setInterval(() => {
            if (!isNaN(music.currentTime)) {
                localStorage.setItem(progressKey, music.currentTime);
            }
        }, 10000);
    }

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
    </script>
    ";
}
