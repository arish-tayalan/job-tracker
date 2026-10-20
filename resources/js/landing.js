// Interactive touches for the landing page. Everything here is decoration:
// the page works the same without it, and it is skipped for "reduce motion".

const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
const hasMouse = window.matchMedia('(pointer: fine)').matches;

// 1. Fade sections in as they scroll into view.
const revealItems = document.querySelectorAll('.reveal');

if (reduceMotion || !('IntersectionObserver' in window)) {
    revealItems.forEach((item) => item.classList.add('is-visible'));
} else {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) return;
            entry.target.classList.add('is-visible');
            observer.unobserve(entry.target); // animate once
        });
    }, { threshold: 0.15 });

    revealItems.forEach((item) => observer.observe(item));
}

// 2. Cursor effects: only for people with a mouse, and not with reduced motion.
if (!reduceMotion && hasMouse) {
    // Glow that follows the cursor, plus floating labels that drift with it (parallax).
    const hero = document.querySelector('[data-spotlight]');

    hero?.addEventListener('pointermove', (event) => {
        const box = hero.getBoundingClientRect();
        hero.style.setProperty('--mx', `${event.clientX - box.left}px`);
        hero.style.setProperty('--my', `${event.clientY - box.top}px`);

        // -0.5 .. 0.5, measured from the middle of the hero.
        const dx = (event.clientX - box.left) / box.width - 0.5;
        const dy = (event.clientY - box.top) / box.height - 0.5;

        hero.querySelectorAll('[data-parallax]').forEach((item) => {
            const strength = Number(item.dataset.parallax);
            item.style.transform = `translate(${dx * strength}px, ${dy * strength}px)`;
        });
    });

    // Screenshots tilt toward the cursor while it is over them.
    document.querySelectorAll('[data-tilt]').forEach((item) => {
        const max = Number(item.dataset.tilt) || 5; // degrees

        item.addEventListener('pointermove', (event) => {
            const box = item.getBoundingClientRect();
            const x = (event.clientX - box.left) / box.width - 0.5;
            const y = (event.clientY - box.top) / box.height - 0.5;
            item.style.setProperty('--tilt-y', `${x * max * 2}deg`);
            item.style.setProperty('--tilt-x', `${-y * max * 2}deg`);
        });

        item.addEventListener('pointerleave', () => {
            item.style.setProperty('--tilt-x', '0deg');
            item.style.setProperty('--tilt-y', '0deg');
        });
    });
}
