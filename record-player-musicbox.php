<?php
/**
 * Plugin Name: 唱片播放器 MusicBox 修复版
 * Description: 仿网易邮箱的唱片音乐播放器（可自定义音乐源，移动端自动隐藏）。
 * Version: 1.2
 * Author: 码铃薯
 */

if (!defined('ABSPATH')) exit;

// 注册设置项
function musicbox_register_settings() {
    add_option('musicbox_music_url', 'https://mp3.52yzk.com/rand-music.php');//默认播放源
    register_setting('musicbox_options_group', 'musicbox_music_url');
}
add_action('admin_init', 'musicbox_register_settings');

// 创建后台菜单
function musicbox_register_menu() {
    add_options_page('唱片播放器设置', '唱片播放器设置', 'manage_options', 'musicbox-settings', 'musicbox_settings_page');
}
add_action('admin_menu', 'musicbox_register_menu');

// 后台设置页面 HTML
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
                        <p class="description">每行一个音乐源 URL（如 <code>https://api.uomg.com/api/rand.music?或者自建api接口Github开源项目https://github.com/2547729123/MP3-API</code>）</p>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

// 前台输出播放器
function musicbox_player_output() {
    $music_urls = explode("\n", get_option('musicbox_music_url'));
    $music_urls = array_filter(array_map('trim', $music_urls)); // 去掉空行
    $music_url = esc_url($music_urls[array_rand($music_urls)]);  // 初始随机歌曲

    echo '
    <style>
#record-player {
    position: fixed;  /* 建议改为 fixed，避免跟页面滚动产生偏移 */
    bottom: 0px;     /* 改为底部 10px */
    left: 0px;       /* 改为左边 10px */
    top: auto;        /* 取消顶部定位 */
    right: auto;      /* 取消右边定位 */
    z-index: 9999;
    width: 80px;
    height: 80px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    perspective: 600px;
    cursor: pointer;
    transition: left 0.5s ease, bottom 0.5s ease;
}

      #record {
        width: 30px;
        height: 30px;
        background-image: url("/wp-content/plugins/record-player-musicbox/Musicbox/music2.png");
        background-size: cover;
        background-position: center;
        border-radius: 50%;
        position: absolute;
        top: 24px;
        cursor: pointer;
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.3);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        z-index: 2;
        transform-origin: center;
        filter: hue-rotate(0deg);
      }

      #record.rotating {
        animation: spin 4s linear infinite, rainbowEffect 4s linear infinite, breathing 5s ease-in-out infinite;
        box-shadow:
          0 8px 10px rgba(255, 0, 0, 0.1),
          0 8px 10px rgba(255, 255, 0, 0.1),
          0 8px 10px rgba(0, 255, 0, 0.1),
          0 8px 10px rgba(0, 255, 255, 0.1),
          0 8px 10px rgba(255, 0, 255, 0.1);
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
          visibility: hidden;
          opacity: 0;
          pointer-events: none;
       }
     }
    </style>

    <div id="record-player">
      <div id="base"></div>
      <div id="record" title="听听歌"></div>
    </div>

    <audio controls id="bg-music" preload="auto" style="display: none;">
      <source src="' . $music_url . '" type="audio/mpeg">
    </audio>

    <script>
      const record = document.getElementById("record");
      const music = document.getElementById("bg-music");
      const musicUrls = ' . json_encode($music_urls) . ';
      let isPlaying = false;

      // 点击切换播放/暂停
      record.addEventListener("click", function () {
        if (!isPlaying) {
          music.play();
          record.classList.add("rotating");
        } else {
          music.pause();
          record.classList.remove("rotating");
        }
        isPlaying = !isPlaying;
      });

      // 播放完毕自动切换下一首
      music.addEventListener("ended", function () {
        let nextMusicUrl = musicUrls[Math.floor(Math.random() * musicUrls.length)];
        music.src = nextMusicUrl;
        music.load();
        music.play();
      });
    </script>
    ';
}

add_action('wp_footer', 'musicbox_player_output');
