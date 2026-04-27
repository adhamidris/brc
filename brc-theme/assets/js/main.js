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

	const tabs = Array.from(section.querySelectorAll('[data-project-tab]'));
	const details = Array.from(section.querySelectorAll('[data-project-detail]'));
	const panels = Array.from(section.querySelectorAll('[data-project-panel]'));

	if (!tabs.length || !details.length || !panels.length) {
		return;
	}

	const imageStates = panels.map(function (panel) {
		return {
			images: Array.from(panel.querySelectorAll('[data-project-image]')),
			index: 0,
		};
	});

	let activeIndex = 0;
	let autoplay = null;

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
		if (index === activeIndex || !tabs[index] || !details[index] || !panels[index]) {
			return;
		}

		tabs.forEach(function (tab, tabIndex) {
			const isActive = tabIndex === index;
			tab.classList.toggle('is-active', isActive);
			tab.setAttribute('aria-selected', isActive ? 'true' : 'false');
			tab.tabIndex = isActive ? 0 : -1;
		});

		details.forEach(function (detail, detailIndex) {
			detail.classList.toggle('is-active', detailIndex === index);
		});

		panels.forEach(function (panel, panelIndex) {
			const isActive = panelIndex === index;
			panel.classList.toggle('is-active', isActive);
			panel.setAttribute('aria-hidden', isActive ? 'false' : 'true');
		});

		activeIndex = index;
		startAutoplay();
	};

	tabs.forEach(function (tab, index) {
		tab.addEventListener('click', function () {
			setActive(index);
		});

		tab.addEventListener('keydown', function (event) {
			if (event.key !== 'ArrowDown' && event.key !== 'ArrowUp' && event.key !== 'ArrowRight' && event.key !== 'ArrowLeft') {
				return;
			}

			event.preventDefault();

			let nextIndex = index;

			if (event.key === 'ArrowDown' || event.key === 'ArrowRight') {
				nextIndex = (index + 1) % tabs.length;
			}

			if (event.key === 'ArrowUp' || event.key === 'ArrowLeft') {
				nextIndex = (index - 1 + tabs.length) % tabs.length;
			}

			setActive(nextIndex);
			tabs[nextIndex].focus();
		});
	});

	panels.forEach(function (panel) {
		panel.addEventListener('mouseenter', function () {
			window.clearInterval(autoplay);
		});

		panel.addEventListener('mouseleave', function () {
			startAutoplay();
		});
	});

	startAutoplay();
	setActive(0);
})();
