(function () {
	const toggle = document.querySelector('[data-nav-toggle]');
	const nav = document.querySelector('[data-mobile-nav]');

	if (!toggle || !nav) {
		return;
	}

	toggle.addEventListener('click', function () {
		const isOpen = toggle.getAttribute('aria-expanded') === 'true';

		toggle.setAttribute('aria-expanded', String(!isOpen));
		nav.hidden = isOpen;
	});
})();

(function () {
	const header = document.querySelector('[data-site-header]');
	const hero = document.querySelector('.home-hero');

	if (!header || !hero) {
		return;
	}

	let ticking = false;

	const syncHeader = function () {
		const heroRect = hero.getBoundingClientRect();
		const shouldHide = heroRect.bottom <= header.offsetHeight;

		header.classList.toggle('site-header--hidden', shouldHide);
		ticking = false;
	};

	const requestSync = function () {
		if (ticking) {
			return;
		}

		ticking = true;
		window.requestAnimationFrame(syncHeader);
	};

	syncHeader();
	window.addEventListener('scroll', requestSync, { passive: true });
	window.addEventListener('resize', requestSync);
})();

(function () {
	const metrics = document.querySelector('.brc-metrics');

	if (!metrics) {
		return;
	}

	const values = Array.from(metrics.querySelectorAll('[data-metric-value]'));

	if (!values.length) {
		return;
	}

	metrics.classList.add('is-primed');

	const parseMetric = function (value) {
		const match = String(value).trim().match(/^(\d+(?:\.\d+)?)([A-Za-z]*)(\+?)$/);

		if (!match) {
			return null;
		}

		return {
			number: Number(match[1]),
			suffix: match[2] || '',
			trailing: match[3] || '',
			decimals: match[1].includes('.') ? match[1].split('.')[1].length : 0,
		};
	};

	const formatMetric = function (metric, progress) {
		const value = metric.number * progress;
		const rounded = metric.decimals > 0 ? value.toFixed(metric.decimals) : String(Math.round(value));

		return rounded + metric.suffix + metric.trailing;
	};

	const animateMetric = function (element) {
		const parsed = parseMetric(element.getAttribute('data-metric-value'));

		if (!parsed) {
			return;
		}

		const duration = 1500;
		const start = window.performance.now();

		const tick = function (now) {
			const elapsed = now - start;
			const progress = Math.min(elapsed / duration, 1);
			const eased = 1 - Math.pow(1 - progress, 3);

			element.textContent = formatMetric(parsed, eased);

			if (progress < 1) {
				window.requestAnimationFrame(tick);
				return;
			}

			element.textContent = element.getAttribute('data-metric-value');
		};

		window.requestAnimationFrame(tick);
	};

	const reveal = function () {
		metrics.classList.add('is-revealed');
		values.forEach(animateMetric);
	};

	const observer = new window.IntersectionObserver(
		function (entries) {
			entries.forEach(function (entry) {
				if (!entry.isIntersecting) {
					return;
				}

				reveal();
				observer.disconnect();
			});
		},
		{
			threshold: 0.35,
		}
	);

	observer.observe(metrics);
})();

(function () {
	const launches = document.querySelector('[data-launches]');

	if (!launches) {
		return;
	}

	const panels = Array.from(launches.querySelectorAll('[data-launch-panel]'));
	const carousels = new Map();

	const formatCount = function (value) {
		return String(value).padStart(2, '0');
	};

	const syncCarousel = function (panel) {
		const state = carousels.get(panel);

		if (!state) {
			return;
		}

		state.slides.forEach(function (slide, index) {
			const isActive = index === state.index;
			const isPrev = index === state.index - 1;
			const isNext = index === state.index + 1;

			slide.classList.toggle('is-active', isActive);
			slide.classList.toggle('is-prev', isPrev);
			slide.classList.toggle('is-next', isNext);
		});

		const activeSlide = state.slides[state.index];
		const offset = activeSlide ? activeSlide.offsetLeft : 0;
		state.track.style.transform = 'translate3d(' + String(offset * -1) + 'px, 0, 0)';

		if (state.current) {
			state.current.textContent = formatCount(state.index + 1);
		}

		if (state.prev) {
			state.prev.disabled = state.index === 0;
		}

		if (state.next) {
			state.next.disabled = state.index === state.slides.length - 1;
		}
	};

	const registerCarousel = function (panel) {
		const track = panel.querySelector('[data-launch-track]');
		const slides = Array.from(panel.querySelectorAll('[data-launch-slide]'));

		if (!track || slides.length === 0) {
			return;
		}

		const state = {
			panel: panel,
			track: track,
			slides: slides,
			index: 0,
			prev: panel.querySelector('[data-launch-prev]'),
			next: panel.querySelector('[data-launch-next]'),
			current: panel.querySelector('[data-launch-current]'),
		};

		if (state.prev) {
			state.prev.addEventListener('click', function () {
				state.index = Math.max(0, state.index - 1);
				syncCarousel(panel);
			});
		}

		if (state.next) {
			state.next.addEventListener('click', function () {
				state.index = Math.min(state.slides.length - 1, state.index + 1);
				syncCarousel(panel);
			});
		}

		slides.forEach(function (slide, index) {
			slide.addEventListener('click', function () {
				state.index = index;
				syncCarousel(panel);
			});
		});

		carousels.set(panel, state);
		syncCarousel(panel);
	};

	panels.forEach(registerCarousel);

	window.addEventListener('resize', function () {
		panels.forEach(function (panel) {
			if (!panel.hidden) {
				syncCarousel(panel);
			}
		});
	});
})();

(function () {
	const section = document.querySelector('[data-project-story]');

	if (!section) {
		return;
	}

	const items = Array.from(section.querySelectorAll('[data-project-story-item]'));

	if (!items.length) {
		return;
	}

	const imageStates = items.map(function (item) {
		return {
			images: Array.from(item.querySelectorAll('[data-project-story-image]')),
			index: 0,
		};
	});

	let activeIndex = 0;
	let autoplay = null;

	const clamp = function (value, min, max) {
		return Math.min(max, Math.max(min, value));
	};

	const syncImages = function (state) {
		state.images.forEach(function (image, index) {
			image.classList.toggle('is-active', index === state.index);
		});
	};

	imageStates.forEach(syncImages);

	const startAutoplay = function () {
		window.clearInterval(autoplay);

		const state = imageStates[activeIndex];

		if (!state || state.images.length < 2) {
			return;
		}

		autoplay = window.setInterval(function () {
			state.index = (state.index + 1) % state.images.length;
			syncImages(state);
		}, 3200);
	};

	const setActive = function (index) {
		if (index === activeIndex || !items[index]) {
			return;
		}

		items[activeIndex].classList.remove('is-active');
		items[index].classList.add('is-active');
		activeIndex = index;
		startAutoplay();
	};

	const applyMobileState = function () {
		items.forEach(function (item, index) {
			item.classList.toggle('is-active', index === activeIndex);
		});
	};

	const syncFromScroll = function () {
		if (window.innerWidth <= 900) {
			applyMobileState();
			return;
		}

		const scrollable = section.offsetHeight - window.innerHeight;

		if (scrollable <= 0) {
			return;
		}

		const rect = section.getBoundingClientRect();
		const progress = Math.max(0, Math.min(1, (-rect.top) / scrollable));
		let nextIndex = activeIndex;

		if (activeIndex === 0 && progress >= 0.56) {
			nextIndex = 1;
		} else if (activeIndex === 1 && progress <= 0.44) {
			nextIndex = 0;
		}

		setActive(nextIndex);
	};

	startAutoplay();
	syncFromScroll();

	let ticking = false;

	const requestSync = function () {
		if (ticking) {
			return;
		}

		ticking = true;
		window.requestAnimationFrame(function () {
			syncFromScroll();
			ticking = false;
		});
	};

	window.addEventListener('scroll', requestSync, { passive: true });
	window.addEventListener('resize', requestSync);
})();
