<?php
/**
 * Plugin Name: 唱片播放器 MusicBox 修复版
 * Description: 仿网易邮箱的唱片音乐播放器（默认左下角，自定义音乐源，移动端隐藏，支持拖动，仅首页首个 tab 自动播放）。
 * Version: 1.3.1
 * Author: 码铃薯
 */

if (!defined('ABSPATH')) exit;

// 注册设置项
function musicbox_register_settings() {
    add_option('musicbox_music_url', 'https://mp3.52yzk.com/rand-music.php');
    register_setting('musicbox_options_group', 'musicbox_music_url');
}
add_action('admin_init', 'musicbox_register_settings');

// 创建后台菜单
function musicbox_register_menu() {
    add_options_page('唱片播放器设置', '唱片播放器设置', 'manage_options', 'musicbox-settings', 'musicbox_settings_page');
}
add_action('admin_menu', 'musicbox_register_menu');

// 后台设置页面
function musicbox_settings_page() {
    ?>
    <div class="wrap">
        <h2>唱片播放器设置</h2>
        <form method="post" action="options.php">
            <?php settings_fields('musicbox_options_group'); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">音乐源地址</th>
                    <td>
                        <textarea name="musicbox_music_url" rows="5" cols="50"><?php echo esc_textarea(get_option('musicbox_music_url')); ?></textarea>
                        <p class="description">每行一个音乐源 URL，例如：https://mp3.52yzk.com/rand-music.php 或其他自建接口</p>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

// 仅在首页输出播放器
add_action('wp_footer', function () {
    if (is_front_page() || is_home()) {
        musicbox_player_output();
    }
});

// 播放器输出函数
function musicbox_player_output() {
    $music_urls = explode("\n", get_option('musicbox_music_url'));
    $music_urls = array_filter(array_map('trim', $music_urls));
    $music_url = esc_url($music_urls[array_rand($music_urls)]);

    echo '
    <style>
    #record-player {
        position: fixed;
        bottom: -10px;
        left: -10px;
        width: 100px;
        height: 100px;
        z-index: 9999;
        cursor: move;
    }
    .player-box {
        position: relative;
        width: 100%;
        height: 100%;
        user-select: none;
    }
    #record {
        position: absolute;
        width: 40px;
        height: 40px;
        top: 30px;
        left: 30px;
        background-image: url("/wp-content/plugins/record-player-musicbox/Musicbox/music2.png");
        background-size: cover;
        border-radius: 50%;
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.3);
        cursor: pointer;
        z-index: 2;
        transition: transform 0.3s ease;
    }
    #record.rotating {
        animation: spin 4s linear infinite, rainbowEffect 4s linear infinite, breathing 5s ease-in-out infinite;
    }
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    @keyframes rainbowEffect {
        0% { filter: hue-rotate(0deg); }
        100% { filter: hue-rotate(360deg); }
    }
    @keyframes breathing {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.15); }
    }
    @media screen and (max-width: 768px) {
        #record-player {
            display: none;
        }
    }
    </style>

    <div id="record-player">
        <div class="player-box">
            <div id="record" title="点击播放/暂停音乐"></div>
        </div>
    </div>

    <audio controls id="bg-music" preload="auto" style="display: none;">
        <source src="' . $music_url . '" type="audio/mpeg">
    </audio>

    <script>
    const record = document.getElementById("record");
    const music = document.getElementById("bg-music");
    const player = document.getElementById("record-player");
    const musicUrls = ' . json_encode($music_urls) . ';
    let isPlaying = false;
    const storageKey = "musicbox_is_playing";

    // 检查是否已有播放标志
    const canAutoPlay = localStorage.getItem(storageKey) !== "true";

    // 如果是首页且可播放，则自动播放
    if (canAutoPlay && location.pathname === "/") {
        music.play().then(() => {
            record.classList.add("rotating");
            isPlaying = true;
            localStorage.setItem(storageKey, "true");
        }).catch(() => {
            // 自动播放失败（如浏览器限制），用户需点击
        });
    }

    // 点击播放/暂停
    record.addEventListener("click", () => {
        if (!isPlaying) {
            music.play();
            record.classList.add("rotating");
            localStorage.setItem(storageKey, "true");
        } else {
            music.pause();
            record.classList.remove("rotating");
            localStorage.setItem(storageKey, "false");
        }
        isPlaying = !isPlaying;
    });

    // 播放结束自动下一首
    music.addEventListener("ended", () => {
        let next = musicUrls[Math.floor(Math.random() * musicUrls.length)];
        music.src = next;
        music.load();
        music.play();
    });

    // 页面关闭时清除播放标志
    window.addEventListener("beforeunload", () => {
        if (isPlaying) {
            localStorage.setItem(storageKey, "false");
        }
    });

    // 拖动功能
    let offsetX = 0, offsetY = 0, isDragging = false;
    player.addEventListener("mousedown", (e) => {
        isDragging = true;
        offsetX = e.clientX - player.getBoundingClientRect().left;
        offsetY = e.clientY - player.getBoundingClientRect().top;
        document.body.style.userSelect = "none";
    });
    document.addEventListener("mousemove", (e) => {
        if (isDragging) {
            player.style.left = e.clientX - offsetX + "px";
            player.style.top = e.clientY - offsetY + "px";
        }
    });
    document.addEventListener("mouseup", () => {
        isDragging = false;
        document.body.style.userSelect = "";
    });
    </script>
    ';
}
