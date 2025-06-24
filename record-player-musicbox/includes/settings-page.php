<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function musicbox_settings_page() {
    ?>
    <div class="wrap">
        <h2><?php esc_html_e('唱片播放器设置', 'record-player-musicbox'); ?></h2>
        <h2 class="nav-tab-wrapper">
            <a href="#" class="nav-tab nav-tab-active" data-tab="tab1"><?php esc_html_e('🎵 播放设置', 'record-player-musicbox'); ?></a>
            <a href="#" class="nav-tab" data-tab="tab2"><?php esc_html_e('🎨 样式设置', 'record-player-musicbox'); ?></a>
        </h2>
        <form method="post" action="options.php">
            <?php settings_fields('musicbox_options_group'); ?>
            <div id="tab1" class="musicbox-tab" style="display: block;">
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php esc_html_e('音乐源地址', 'record-player-musicbox'); ?></th>
                        <td>
                            <textarea name="musicbox_music_url" rows="5" cols="50"><?php echo esc_textarea(get_option('musicbox_music_url')); ?></textarea>
                            <p class="description"><?php esc_html_e('每行一个音乐源 URL，例如：https://xxx.com/xxx.mp3或免费API接口https://mp3.xxx.com/rand-music.php', 'record-player-musicbox'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e('启用首页自动播放', 'record-player-musicbox'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="musicbox_enable_autoplay" value="1" <?php checked(get_option('musicbox_enable_autoplay'), '1'); ?>> <?php esc_html_e('启用', 'record-player-musicbox'); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e('禁止自动播放时长（小时）', 'record-player-musicbox'); ?></th>
                        <td>
                            <input type="number" name="musicbox_disable_hours" value="<?php echo esc_attr(get_option('musicbox_disable_hours')); ?>" min="1">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e('仅在首页显示播放器', 'record-player-musicbox'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="musicbox_only_homepage" value="1" <?php checked(get_option('musicbox_only_homepage'), '1'); ?>> <?php esc_html_e('启用', 'record-player-musicbox'); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e('关闭播放进度记忆', 'record-player-musicbox'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="musicbox_disable_progress_memory" value="1" <?php checked(get_option('musicbox_disable_progress_memory'), '1'); ?>> <?php esc_html_e('勾选后关闭播放进度记忆功能', 'record-player-musicbox'); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e('禁止手机端自动播放', 'record-player-musicbox'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="musicbox_disable_mobile_autoplay" value="1" <?php checked(get_option('musicbox_disable_mobile_autoplay'), '1'); ?>> <?php esc_html_e('启用', 'record-player-musicbox'); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e('一键清除禁播时段', 'record-player-musicbox'); ?></th>
                        <td>
                            <button type="button" id="clear-disable-time" class="button button-secondary"><?php esc_html_e('清除禁播', 'record-player-musicbox'); ?></button>
                        </td>
                    </tr>
                </table>
        <hr>
        <div style="display: flex; flex-wrap: wrap; gap: 20px; margin-top: 30px;">
            <?php
            $features = [
                ['icon' => '🔓', 'title' => esc_html__('自动播放控制', 'record-player-musicbox'), 'desc' => esc_html__('选择性播放、白名单限制', 'record-player-musicbox')],
                ['icon' => '🎧', 'title' => esc_html__('支持歌词同步', 'record-player-musicbox'), 'desc' => esc_html__('LRC 歌词自动滚动显示', 'record-player-musicbox')],
                ['icon' => '🔁', 'title' => esc_html__('无限循环模式', 'record-player-musicbox'), 'desc' => esc_html__('灵活配置播放模式', 'record-player-musicbox')],
                ['icon' => '🛠', 'title' => esc_html__('专属技术支持', 'record-player-musicbox'), 'desc' => esc_html__('提供快速响应的支持服务', 'record-player-musicbox')],
            ];
            foreach ($features as $feature) : ?>
                <div style="flex: 1 1 240px; background: #fff8e7; border: 1px solid #f4ca8d; border-radius: 8px; padding: 15px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
                    <h3 style="margin-top: 0; font-size: 16px;">
                        <span style="margin-right: 6px; font-size: 18px;"><?php echo esc_html($feature['icon']); ?></span>
                        <?php echo esc_html($feature['title']); ?>
                    </h3>
                    <p style="margin: 0; color: #555; font-size: 14px;">
						<?php echo esc_html($feature['desc']); ?>
                    </p>
                </div>
            <?php endforeach; ?>

        </div>

        <div style="margin-top: 20px; text-align: center;">
            <a href="https://www.tudoucode.cn/plugins/wp-plugins/record-player-musicbox/" target="_blank" class="button button-primary button-hero">
                <?php esc_html_e('查看 Pro 版详情', 'record-player-musicbox'); ?> ➜
            </a>
        </div>
        <div class="musicbox-self-note">
            <strong><?php esc_html_e('开发者自评：', 'record-player-musicbox'); ?></strong><br>
            <?php esc_html_e('本插件旨在还原经典唱片机交互体验，代码结构清晰，前后端解耦良好。已考虑性能优化和基本安全规范，后续将持续维护并增强自定义能力。欢迎提出宝贵建议！', 'record-player-musicbox'); ?>
        </div>
            </div>
            <div id="tab2" class="musicbox-tab" style="display: none;">
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php esc_html_e('唱盘图标 URL', 'record-player-musicbox'); ?></th>
                        <td>
                            <input type="text" name="musicbox_custom_icon" value="<?php echo esc_url(get_option('musicbox_custom_icon')); ?>" size="60">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e('播放器尺寸（px）', 'record-player-musicbox'); ?></th>
                        <td>
                            <?php esc_html_e('宽：', 'record-player-musicbox'); ?><input type="number" name="musicbox_width" value="<?php echo esc_attr(get_option('musicbox_width')); ?>" style="width:80px;">
                            　
                            <?php esc_html_e('高：', 'record-player-musicbox'); ?><input type="number" name="musicbox_height" value="<?php echo esc_attr(get_option('musicbox_height')); ?>" style="width:80px;">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e('初始位置（left / bottom）', 'record-player-musicbox'); ?></th>
                        <td>
                            <?php esc_html_e('Left：', 'record-player-musicbox'); ?><input type="number" name="musicbox_position_left" value="<?php echo esc_attr(get_option('musicbox_position_left')); ?>" style="width:80px;">
                            　
                            <?php esc_html_e('Bottom：', 'record-player-musicbox'); ?><input type="number" name="musicbox_position_bottom" value="<?php echo esc_attr(get_option('musicbox_position_bottom')); ?>" style="width:80px;">
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
    document.getElementById('clear-disable-time')?.addEventListener('click', () => {
        localStorage.removeItem('musicbox_disable_autoplay_until');
        alert('✅ 已清除禁播限制！刷新后将可自动播放');
    });
    </script>
    <?php
}
