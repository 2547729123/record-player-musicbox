document.addEventListener('DOMContentLoaded', () => {
    const record = document.getElementById('record');
    const music = document.getElementById('bg-music');
    const {
        musicUrls,
        icon,
        width,
        height,
        left,
        bottom,
        autoplay,
        disableHours,
        disableProgressMemory
    } = musicboxData;

    const autoplayKey = 'musicbox_is_playing';
    const disableKey = 'musicbox_disable_autoplay_until';
    const progressKey = 'musicbox_last_position';
    const tabLockKey = 'musicbox_is_active_tab';
    const tabId = 'tab_' + Date.now() + '_' + Math.random().toString(36).substr(2);

    let isActiveTab = false;
    let isPlaying = false;

    const player = document.getElementById('record-player');
    player.style.left = `${left}px`;
    player.style.bottom = `${bottom}px`;
    player.style.width = `${width}px`;
    player.style.height = `${height}px`;
    record.style.backgroundImage = `url(${icon})`;

    function isHomePage() {
        const path = window.location.pathname;
        return path === '/' || path === '/index.php';
    }

    function updateTitle() {
        const now = Date.now();
        const disableUntil = parseInt(localStorage.getItem(disableKey)) || 0;
        record.title = now < disableUntil
            ? '已禁止自动播放，再次双击取消！'
            : `双击我，${disableHours}小时内不再自动播放`;
    }

    function tryBecomeActiveTab() {
        const currentActive = localStorage.getItem(tabLockKey);
        if (!currentActive || currentActive === tabId) {
            localStorage.setItem(tabLockKey, tabId);
            isActiveTab = true;

            const now = Date.now();
            const disableUntil = parseInt(localStorage.getItem(disableKey)) || 0;
            const isMobile = /Mobi|Android|iPhone|iPad|iPod/i.test(navigator.userAgent);

            if (!isMobile && autoplay && isHomePage() && now > disableUntil) {
                music.src = musicUrls[Math.floor(Math.random() * musicUrls.length)];
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

    updateTitle();
    tryBecomeActiveTab();

    window.addEventListener('storage', (e) => {
        if (e.key === tabLockKey && e.newValue !== tabId) {
            if (isActiveTab && isPlaying) {
                music.pause();
                record.classList.remove('rotating');
                isPlaying = false;
                isActiveTab = false;
            }
        }
    });

    window.addEventListener('beforeunload', () => {
        if (isActiveTab) localStorage.removeItem(tabLockKey);
    });

    if (!disableProgressMemory) {
        const lastTime = parseFloat(localStorage.getItem(progressKey));
        if (!isNaN(lastTime)) music.currentTime = lastTime;
        setInterval(() => {
            if (!isNaN(music.currentTime)) {
                localStorage.setItem(progressKey, music.currentTime);
            }
        }, 10000);
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
            localStorage.removeItem(disableKey);
            alert('✅ 播放器禁止自动播放已解除，刷新后将可自动播放');
        } else {
            localStorage.setItem(disableKey, (now + disableHours * 3600 * 1000).toString());
            alert(`🎧 播放器将在未来 ${disableHours} 小时内不再自动播放`);
        }
        updateTitle();
    });

    music.addEventListener('ended', () => {
        const next = musicUrls[Math.floor(Math.random() * musicUrls.length)];
        music.src = next;
        music.load();
        music.play();
    });

    // 拖动逻辑
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
});
