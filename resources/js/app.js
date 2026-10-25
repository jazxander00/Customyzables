import './bootstrap';

import { animate, stagger, utils } from 'animejs';

/**
 * Motion rules for Customyzables.
 *
 * 1. Motion explains state (something moved, something changed) — it never delays a task.
 * 2. Everything here is opt-out: `prefers-reduced-motion` disables it in one place.
 * 3. Durations stay short (150–600ms). If a user notices the animation more than the content,
 *    it is too slow.
 */
const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

const motion = {
    reduce: prefersReducedMotion,

    reveal(selector = '[data-reveal]', options = {}) {
        const targets = utils.$(selector);
        if (!targets.length) return;

        if (prefersReducedMotion) {
            targets.forEach((el) => {
                el.style.opacity = '1';
                el.style.transform = 'none';
            });
            return;
        }

        animate(targets, {
            opacity: [0, 1],
            translateY: [12, 0],
            duration: 520,
            delay: stagger(options.stagger ?? 45, { start: options.start ?? 0 }),
            ease: 'outQuad',
        });
    },

    cards(selector = '[data-stagger] > *') {
        const targets = utils.$(selector);
        if (!targets.length) return;

        if (prefersReducedMotion) {
            targets.forEach((el) => {
                el.style.opacity = '1';
            });
            return;
        }

        animate(targets, {
            opacity: [0, 1],
            translateY: [16, 0],
            scale: [0.985, 1],
            duration: 460,
            delay: stagger(55),
            ease: 'outCubic',
        });
    },

    countUp(selector = '[data-count-to]') {
        utils.$(selector).forEach((el) => {
            const end = Number(el.dataset.countTo || 0);
            const decimals = Number(el.dataset.countDecimals || 0);
            const suffix = el.dataset.countSuffix || '';

            if (!Number.isFinite(end)) return;

            if (prefersReducedMotion) {
                el.textContent = end.toFixed(decimals) + suffix;
                return;
            }

            const counter = { value: 0 };
            animate(counter, {
                value: end,
                duration: 1100,
                ease: 'outExpo',
                onUpdate: () => {
                    el.textContent = counter.value.toFixed(decimals) + suffix;
                },
                onComplete: () => {
                    el.textContent = end.toFixed(decimals) + suffix;
                },
            });
        });
    },

    stepper(selector = '[data-stepper]') {
        const stepper = document.querySelector(selector);
        if (!stepper) return;

        const bar = stepper.querySelector('[data-stepper-bar]');
        const steps = utils.$(selector + ' [data-step]');

        steps.forEach((step, index) => {
            const delay = prefersReducedMotion ? 0 : index * 70;
            window.setTimeout(() => {
                step.dataset.state = step.dataset.stepState || 'done';

                if (!prefersReducedMotion) {
                    animate(step, {
                        scale: [0.94, 1],
                        opacity: [0.6, 1],
                        duration: 260,
                        ease: 'outBack(2)',
                    });
                }
            }, delay);
        });

        if (bar && !prefersReducedMotion) {
            const pct = Number(bar.dataset.progress || 0);
            bar.style.width = '0%';
            animate(bar, {
                width: ['0%', `${pct}%`],
                duration: 780,
                delay: 120,
                ease: 'outQuart',
            });
        }
    },

    /** Flash messages slide in, then leave on their own. */
    flash() {
        utils.$('[data-flash]').forEach((el) => {
            if (prefersReducedMotion) return;

            animate(el, {
                opacity: [0, 1],
                translateY: [-8, 0],
                duration: 320,
                ease: 'outQuad',
            });

            window.setTimeout(() => {
                animate(el, {
                    opacity: 0,
                    translateY: -6,
                    duration: 260,
                    ease: 'inQuad',
                    onComplete: () => el.remove(),
                });
            }, 4200);
        });
    },

    /** Small press feedback so buttons feel physical. */
    press() {
        if (prefersReducedMotion) return;

        document.querySelectorAll('[data-press]').forEach((el) => {
            el.addEventListener('click', () => {
                animate(el, {
                    scale: [1, 0.96, 1],
                    duration: 260,
                    ease: 'outQuad',
                });
            });
        });
    },

    /** Progress bars in the feed/impact cards. */
    bars() {
        utils.$('[data-bar]').forEach((el) => {
            const pct = Number(el.dataset.bar || 0);
            if (prefersReducedMotion) {
                el.style.width = `${pct}%`;
                return;
            }
            animate(el, {
                width: ['0%', `${pct}%`],
                duration: 700,
                delay: 160,
                ease: 'outQuart',
            });
        });
    },

    enter() {
        this.reveal();
        this.cards();
        this.countUp();
        this.stepper();
        this.bars();
        this.flash();
        this.press();
    },
};

document.addEventListener('DOMContentLoaded', () => motion.enter());

window.motion = motion;
