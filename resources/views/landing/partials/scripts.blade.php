{{-- ============================ GSAP scroll animations ============================ --}}
<script>
(function () {
    const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    // --- Sticky nav background toggle (runs regardless of motion pref) ----
    const nav = document.getElementById('siteNav');
    const onScroll = () => {
        if (window.scrollY > 24) { nav.classList.add('is-scrolled'); }
        else { nav.classList.remove('is-scrolled'); }
    };
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();

    // --- Dispatch board live clock (text update only, not motion) --------
    const clockEl = document.querySelector('[data-clock]');
    if (clockEl) {
        const tick = () => { clockEl.textContent = new Date().toLocaleTimeString('en-GB'); };
        tick();
        setInterval(tick, 1000);
    }

    if (prefersReduced || typeof gsap === 'undefined') {
        // Ensure all content is visible without animation
        document.querySelectorAll('.reveal').forEach(el => { el.style.opacity = 1; el.style.transform = 'none'; });
        // Still set count-up final values
        document.querySelectorAll('.count-up').forEach(el => {
            el.textContent = Number(el.dataset.target).toLocaleString();
        });
        // Freeze flaps on their final "delivered" state
        document.querySelectorAll('[data-flap]').forEach(el => {
            const states = (el.dataset.states || '').split(',');
            const face = el.querySelector('.flap-face');
            if (face && states.length) {
                const last = states[states.length - 1];
                face.textContent = last;
                face.dataset.state = last;
            }
        });
        return;
    }

    gsap.registerPlugin(ScrollTrigger);

    // Custom webfonts (Bricolage Grotesque / JetBrains Mono) can swap in after
    // ScrollTrigger's first measurement pass, shifting section heights enough
    // to throw off trigger positions — refresh once fonts are actually ready.
    if (document.fonts && document.fonts.ready) {
        document.fonts.ready.then(() => ScrollTrigger.refresh());
    }

    // --- Hero entrance (staggered) ---------------------------------------
    gsap.to('[data-hero]', {
        opacity: 1, y: 0, duration: 0.9, ease: 'power3.out', stagger: 0.12, delay: 0.1
    });

    // --- Generic scroll reveals ------------------------------------------
    gsap.utils.toArray('[data-reveal]').forEach((el) => {
        gsap.to(el, {
            opacity: 1, y: 0, duration: 0.8, ease: 'power3.out',
            scrollTrigger: { trigger: el, start: 'top 85%', toggleActions: 'play none none none' }
        });
    });

    // --- Hero mockup parallax on scroll ----------------------------------
    gsap.utils.toArray('[data-float]').forEach((el) => {
        const depth = parseFloat(el.dataset.float) || 0.2;
        gsap.to(el, {
            yPercent: -depth * 60,
            ease: 'none',
            scrollTrigger: { trigger: '[data-parallax-scene]', start: 'top bottom', end: 'bottom top', scrub: true }
        });
    });

    // --- Hero mockup tilt on mousemove -----------------------------------
    const scene = document.querySelector('[data-parallax-scene]');
    if (scene && window.matchMedia('(min-width: 1024px)').matches) {
        const floats = scene.querySelectorAll('[data-float]');
        scene.addEventListener('mousemove', (e) => {
            const r = scene.getBoundingClientRect();
            const cx = (e.clientX - r.left) / r.width - 0.5;
            const cy = (e.clientY - r.top) / r.height - 0.5;
            floats.forEach((el) => {
                const d = parseFloat(el.dataset.float) || 0.2;
                gsap.to(el, { x: cx * d * 50, rotateY: cx * 6, rotateX: -cy * 6, duration: 0.6, ease: 'power2.out', transformPerspective: 800 });
            });
        });
        scene.addEventListener('mouseleave', () => {
            floats.forEach((el) => gsap.to(el, { x: 0, rotateX: 0, rotateY: 0, duration: 0.8, ease: 'power2.out' }));
        });
    }

    // --- Drifting glow blobs ---------------------------------------------
    gsap.utils.toArray('[data-glow]').forEach((el, i) => {
        gsap.to(el, { x: i % 2 ? 40 : -40, y: i % 2 ? -30 : 30, duration: 6 + i, ease: 'sine.inOut', yoyo: true, repeat: -1 });
    });

    // --- Dispatch board split-flap status cycling --------------------------
    gsap.utils.toArray('[data-flap]').forEach((el, i) => {
        const states = (el.dataset.states || '').split(',').filter(Boolean);
        const face = el.querySelector('.flap-face');
        if (!face || states.length < 2) { return; }
        let idx = 0;
        const flip = () => {
            const next = states[(idx + 1) % states.length];
            gsap.to(face, {
                rotateX: -90, duration: 0.18, ease: 'power1.in', transformPerspective: 240,
                onComplete: () => {
                    face.textContent = next;
                    face.dataset.state = next;
                    idx = (idx + 1) % states.length;
                    gsap.fromTo(face, { rotateX: 90 }, { rotateX: 0, duration: 0.18, ease: 'power1.out' });
                }
            });
        };
        gsap.delayedCall(2 + i * 1.1, function loop() {
            flip();
            gsap.delayedCall(3.4, loop);
        });
    });

    // --- Magnetic hover on primary CTAs ------------------------------------
    if (window.matchMedia('(min-width: 1024px)').matches) {
        document.querySelectorAll('[data-magnetic]').forEach((btn) => {
            btn.addEventListener('mousemove', (e) => {
                const r = btn.getBoundingClientRect();
                gsap.to(btn, { x: (e.clientX - r.left - r.width / 2) * 0.25, y: (e.clientY - r.top - r.height / 2) * 0.4, duration: 0.3, ease: 'power2.out' });
            });
            btn.addEventListener('mouseleave', () => gsap.to(btn, { x: 0, y: 0, duration: 0.5, ease: 'elastic.out(1, 0.4)' }));
        });
    }

    // --- Count-up stats ---------------------------------------------------
    gsap.utils.toArray('.count-up').forEach((el) => {
        const target = Number(el.dataset.target) || 0;
        const obj = { val: 0 };
        ScrollTrigger.create({
            trigger: el,
            start: 'top 90%',
            once: true,
            onEnter: () => {
                gsap.to(obj, {
                    val: target, duration: 1.6, ease: 'power2.out',
                    onUpdate: () => { el.textContent = Math.round(obj.val).toLocaleString(); }
                });
            }
        });
    });
})();
</script>
