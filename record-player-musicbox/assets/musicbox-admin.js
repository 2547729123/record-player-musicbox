document.addEventListener('DOMContentLoaded', function () {
    // Tab 切换
    document.querySelectorAll('.nav-tab').forEach(tab => {
        tab.addEventListener('click', e => {
            e.preventDefault();
            document.querySelectorAll('.nav-tab').forEach(t => t.classList.remove('nav-tab-active'));
            document.querySelectorAll('.musicbox-tab').forEach(c => c.style.display = 'none');
            tab.classList.add('nav-tab-active');
            const target = document.getElementById(tab.dataset.tab);
            if (target) target.style.display = 'block';
        });
    });

    // 清除禁播限制
    const clearBtn = document.getElementById('clear-disable-time');
    if (clearBtn) {
        clearBtn.addEventListener('click', () => {
            localStorage.removeItem('musicbox_disable_autoplay_until');
            alert('✅ 已清除禁播限制！刷新后将可自动播放');
        });
    }
});