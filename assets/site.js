// ─── Scroll reveal for non-hero elements ─────────────────
const revealTargets = document.querySelectorAll(
    '.skills-card, .project-card, .stat, .meta-block, .contact-info__block'
);

if ('IntersectionObserver' in window) {
    const io = new IntersectionObserver((entries) => {
        entries.forEach((entry, i) => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '0';
                entry.target.style.transform = 'translateY(16px)';
                entry.target.style.transition = 'opacity 0.7s ease, transform 0.7s ease';
                requestAnimationFrame(() => {
                    setTimeout(() => {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }, i * 60);
                });
                io.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1 });

    revealTargets.forEach(el => io.observe(el));
}

// ─── Projects coverflow carousel ─────────────────────────
(function initCarousel() {
    const root = document.getElementById('projectCarousel');
    if (!root) return;

    const cards = Array.from(root.querySelectorAll('.pcard'));
    const dots = Array.from(root.querySelectorAll('.carousel__dot'));
    const prevBtn = document.getElementById('carouselPrev');
    const nextBtn = document.getElementById('carouselNext');
    const total = cards.length;
    if (total === 0) return;

    let active = 0;
    let autoTimer = null;
    let hovering = false;
    const AUTOPLAY_MS = 4000;

    // Smallest signed distance from active to i on a ring of `total` items,
    // so cards wrap around (e.g. last sits to the left of first).
    function ringOffset(i) {
        let d = i - active;
        if (d > total / 2) d -= total;
        if (d < -total / 2) d += total;
        return d;
    }

    function layout() {
        // Spread side cards out beside the centered one (not stacked behind it).
        // Offset = half the active card width + a gap, so neighbours peek at the edges.
        const gapRaw = getComputedStyle(root).getPropertyValue('--card-gap');
        const SPREAD = parseInt(gapRaw, 10) || 360;

        cards.forEach((card, i) => {
            const off = ringOffset(i);
            const abs = Math.abs(off);
            const isActive = off === 0;

            // Only the active card and its immediate neighbours are shown.
            const hidden = abs > 1;
            const x = off * SPREAD;
            const scale = isActive ? 1 : 0.82;
            const rot = isActive ? 0 : (off < 0 ? 18 : -18);
            const op = hidden ? 0 : (isActive ? 1 : 0.35);
            const z = isActive ? 10 : 5 - abs;

            card.style.setProperty('--x', x + 'px');
            card.style.setProperty('--scale', scale);
            card.style.setProperty('--rot', rot + 'deg');
            card.style.setProperty('--op', op);
            card.style.setProperty('--z', z);
            card.classList.toggle('is-active', isActive);
            // Fully tuck away cards that shouldn't intercept clicks
            card.style.pointerEvents = hidden ? 'none' : '';
            card.setAttribute('aria-hidden', hidden ? 'true' : 'false');
        });
        dots.forEach((d, i) => d.classList.toggle('is-active', i === active));
    }

    function goTo(i) {
        active = ((i % total) + total) % total;
        layout();
    }
    const next = () => goTo(active + 1);
    const prev = () => goTo(active - 1);

    function startAuto() {
        stopAuto();
        // Rotate by default — but never while the user is hovering to spectate.
        if (total > 1 && !hovering) autoTimer = setInterval(next, AUTOPLAY_MS);
    }
    function stopAuto() {
        if (autoTimer) { clearInterval(autoTimer); autoTimer = null; }
    }

    // Controls
    nextBtn && nextBtn.addEventListener('click', () => { next(); startAuto(); });
    prevBtn && prevBtn.addEventListener('click', () => { prev(); startAuto(); });
    dots.forEach((dot) => {
        dot.addEventListener('click', () => {
            goTo(parseInt(dot.getAttribute('data-index'), 10));
            startAuto();
        });
    });

    // Click a side card to bring it to center (its link is disabled while inactive)
    cards.forEach((card) => {
        card.addEventListener('click', (e) => {
            if (!card.classList.contains('is-active')) {
                e.preventDefault();
                goTo(parseInt(card.getAttribute('data-index'), 10));
                startAuto();
            }
        });
    });

    // Keyboard arrows when the carousel is in view / focused
    root.setAttribute('tabindex', '0');
    root.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowLeft') { prev(); startAuto(); }
        else if (e.key === 'ArrowRight') { next(); startAuto(); }
    });

    // Pause rotation while hovering so the user can spectate the project
    root.addEventListener('mouseenter', () => { hovering = true; stopAuto(); });
    root.addEventListener('mouseleave', () => { hovering = false; startAuto(); });

    // Drag / swipe
    let dragX = null;
    const onDown = (x) => { dragX = x; stopAuto(); };
    const onUp = (x) => {
        if (dragX === null) return;
        const dx = x - dragX;
        if (Math.abs(dx) > 50) { dx < 0 ? next() : prev(); }
        dragX = null;
        startAuto();
    };
    root.addEventListener('mousedown', (e) => onDown(e.clientX));
    window.addEventListener('mouseup', (e) => onUp(e.clientX));
    root.addEventListener('touchstart', (e) => onDown(e.touches[0].clientX), { passive: true });
    root.addEventListener('touchend', (e) => onUp(e.changedTouches[0].clientX));

    // Re-layout on resize (gap changes across the breakpoint)
    let resizeRAF = null;
    window.addEventListener('resize', () => {
        if (resizeRAF) cancelAnimationFrame(resizeRAF);
        resizeRAF = requestAnimationFrame(layout);
    });

    layout();
    startAuto();
})();

// ─── Gallery lightbox ────────────────────────────────────
(function initLightbox() {
    const grid = document.getElementById('projectGallery');
    const lb = document.getElementById('lightbox');
    if (!grid || !lb) return;

    const lbImg = document.getElementById('lbImg');
    const lbPos = document.getElementById('lbPos');
    const lbClose = document.getElementById('lbClose');
    const lbPrev = document.getElementById('lbPrev');
    const lbNext = document.getElementById('lbNext');
    const thumbs = lb.querySelectorAll('.lightbox__thumb');

    const items = Array.from(grid.querySelectorAll('.gallery-grid__item'));
    const total = items.length;
    let current = 0;

    function pad(n) { return String(n).padStart(2, '0'); }

    function show(i) {
        current = (i + total) % total;
        const item = items[current];
        const src = item.getAttribute('data-src');

        // Brief fade for the image swap
        lbImg.style.opacity = '0';
        const next = new Image();
        next.onload = () => {
            lbImg.src = src;
            lbImg.style.opacity = '1';
        };
        next.src = src;

        lbPos.textContent = pad(current + 1) + ' / ' + pad(total);
        thumbs.forEach((t, idx) => t.classList.toggle('is-active', idx === current));

        // Scroll active thumb into view
        const active = thumbs[current];
        if (active && active.scrollIntoView) {
            active.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
        }
    }

    function open(i) {
        show(i);
        lb.classList.add('is-open');
        lb.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
    }

    function close() {
        lb.classList.remove('is-open');
        lb.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
    }

    items.forEach((el, i) => {
        el.addEventListener('click', () => open(i));
    });

    thumbs.forEach((t) => {
        t.addEventListener('click', () => {
            const idx = parseInt(t.getAttribute('data-index'), 10);
            show(idx);
        });
    });

    lbClose.addEventListener('click', close);
    lbPrev.addEventListener('click', () => show(current - 1));
    lbNext.addEventListener('click', () => show(current + 1));

    lb.querySelector('.lightbox__backdrop').addEventListener('click', close);

    document.addEventListener('keydown', (e) => {
        if (!lb.classList.contains('is-open')) return;
        if (e.key === 'Escape') close();
        else if (e.key === 'ArrowLeft') show(current - 1);
        else if (e.key === 'ArrowRight') show(current + 1);
    });

    // Smooth fade for image swaps
    lbImg.style.transition = 'opacity 0.2s ease';
})();

// ─── Hero terminal typing animation ──────────────────────
(function initHeroTerminal() {
    const body = document.getElementById('heroTerminalBody');
    if (!body) return;

    // Each segment: { text, cls } — typed sequentially.
    // `gap` lines render instantly as a blank line (spacing between blocks).
    const SEQUENCE = [
        [{ text: '$ ', cls: 'term-prompt' }, { text: 'whoami', cls: 'term-cmd' }],
        [{ text: '> ', cls: 'term-out' }, { text: 'yeltsin.batiancila', cls: 'term-hl' }],
        'gap',
        [{ text: '$ ', cls: 'term-prompt' }, { text: 'stack --list', cls: 'term-cmd' }],
        [{ text: '> ', cls: 'term-out' }, { text: 'laravel  ·  vue  ·  php', cls: 'term-hl' }],
        [{ text: '> ', cls: 'term-out' }, { text: 'mysql  ·  filament  ·  forge', cls: 'term-hl' }],
        'gap',
        [{ text: '$ ', cls: 'term-prompt' }, { text: 'status', cls: 'term-cmd' }],
        [{ text: '> ', cls: 'term-out' }, { text: 'available_for_hire  ', cls: 'term-out' }, { text: '✓', cls: 'term-ok' }],
        'gap',
        [{ text: '$ ', cls: 'term-prompt' }, { text: '', cls: 'term-cmd', cursor: true }]
    ];

    const CHAR_MIN = 40, CHAR_MAX = 80;   // ms per character
    const LINE_PAUSE = 500;               // ms between lines
    const RESTART_PAUSE = 8000;           // ms before looping

    let timer = null;

    const wait = (ms) => new Promise((resolve) => { timer = setTimeout(resolve, ms); });
    const rand = (min, max) => Math.floor(Math.random() * (max - min + 1)) + min;

    function makeLine() {
        const line = document.createElement('span');
        line.className = 'terminal__line';
        body.appendChild(line);
        return line;
    }

    async function typeSegment(lineEl, seg) {
        const span = document.createElement('span');
        span.className = seg.cls;
        if (seg.cursor) span.classList.add('terminal__cursor');
        lineEl.appendChild(span);

        if (seg.cursor) {
            // Blinking cursor — render the underscore, no per-char typing.
            span.textContent = '_';
            return;
        }
        for (const ch of seg.text) {
            span.textContent += ch;
            await wait(rand(CHAR_MIN, CHAR_MAX));
        }
    }

    async function run() {
        while (true) {
            body.textContent = '';
            for (const entry of SEQUENCE) {
                if (entry === 'gap') {
                    const gap = makeLine();
                    gap.innerHTML = '&nbsp;';
                    continue;
                }
                const lineEl = makeLine();
                for (const seg of entry) {
                    await typeSegment(lineEl, seg);
                }
                await wait(LINE_PAUSE);
            }
            await wait(RESTART_PAUSE);
        }
    }

    run();
})();

// ─── Hero headline live-typing (type → pause → delete → loop) ─
(function initHeroHeadline() {
    const line = document.querySelector('.hero__typeline');
    if (!line) return;

    const emEl = line.querySelector('.hero__type-em em');
    const restEl = line.querySelector('.hero__type-rest');
    if (!emEl || !restEl) return;

    const emText = line.getAttribute('data-type-em') || '';
    const restText = line.getAttribute('data-type-rest') || '';

    const TYPE_MIN = 55, TYPE_MAX = 110;  // ms per char while typing
    const DELETE_SPEED = 35;              // ms per char while deleting
    const HOLD_FULL = 2200;               // pause once fully typed
    const HOLD_EMPTY = 600;               // pause once cleared

    let timer = null;
    const wait = (ms) => new Promise((r) => { timer = setTimeout(r, ms); });
    const rand = (min, max) => Math.floor(Math.random() * (max - min + 1)) + min;

    // The full string typed across the two styled spans, in order.
    // Each char carries which span it belongs to.
    const chars = [];
    for (const c of emText) chars.push({ ch: c, el: emEl });
    for (const c of restText) chars.push({ ch: c, el: restEl });

    function render(count) {
        let emLen = 0;
        for (let i = 0; i < count && i < emText.length; i++) emLen++;
        const restLen = Math.max(0, count - emText.length);
        emEl.textContent = emText.slice(0, emLen);
        restEl.textContent = restText.slice(0, restLen);
    }

    async function run() {
        while (true) {
            // Type forward
            for (let i = 1; i <= chars.length; i++) {
                render(i);
                await wait(rand(TYPE_MIN, TYPE_MAX));
            }
            await wait(HOLD_FULL);
            // Delete backward
            for (let i = chars.length - 1; i >= 0; i--) {
                render(i);
                await wait(DELETE_SPEED);
            }
            await wait(HOLD_EMPTY);
        }
    }

    // Clear the static fallback, then start typing.
    emEl.textContent = '';
    restEl.textContent = '';
    run();
})();
