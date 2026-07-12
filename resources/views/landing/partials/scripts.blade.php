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

    if (prefersReduced || typeof gsap === 'undefined') {
        // Ensure all content is visible without animation
        document.querySelectorAll('.reveal').forEach(el => { el.style.opacity = 1; el.style.transform = 'none'; });
        // Still set count-up final values
        document.querySelectorAll('.count-up').forEach(el => {
            el.textContent = Number(el.dataset.target).toLocaleString();
        });
        return;
    }

    gsap.registerPlugin(ScrollTrigger);

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
