const cssVar = (name, fallback) => {
	const val = parseFloat(getComputedStyle(document.documentElement).getPropertyValue(name));
	return isNaN(val) ? fallback : val;
};

const adjustRowGap = (swiper) => {
	const cols  = swiper.params.slidesPerView;
	const rows  = swiper.params.grid?.rows ?? 1;
	const group = cols * rows;
	swiper.slides.forEach((slide, i) => {
		[...slide.classList].filter(c => c.startsWith('rg-slider-row-')).forEach(c => slide.classList.remove(c));
		slide.classList.add(`rg-slider-row-${Math.floor((i % group) / cols) + 1}`);
	});
};

export default (postsEl) => {

	if (!postsEl) return;

	postsEl.classList.add('swiper', 'rg-intro-shop-slider');

	[...postsEl.querySelectorAll(':scope > .plura-wp-post')].forEach(post => {
		post.classList.add('swiper-slide');
	});

	const wrapper = document.createElement('div');
	wrapper.classList.add('swiper-wrapper');
	[...postsEl.children].forEach(child => wrapper.appendChild(child));
	postsEl.appendChild(wrapper);

	const pagination = document.createElement('div');
	pagination.classList.add('swiper-pagination');
	postsEl.appendChild(pagination);

	new Swiper(postsEl, {
		grid:          { rows: cssVar('--rg-shop-rows-base', 2), fill: 'row' },
		slidesPerView: cssVar('--rg-shop-cols-base', 2),
		slidesPerGroup: cssVar('--rg-shop-cols-base', 2),
		spaceBetween:  cssVar('--rg-shop-space-x-base', 12),
		speed: 800,
		breakpoints: {
			768:  {
				slidesPerView:  cssVar('--rg-shop-cols-768', 3),
				slidesPerGroup: cssVar('--rg-shop-cols-768', 3),
				spaceBetween:   cssVar('--rg-shop-space-x-768', 20),
				grid: { rows: cssVar('--rg-shop-rows-768', 2), fill: 'row' },
			},
			1200: {
				slidesPerView:  cssVar('--rg-shop-cols-1200', 4),
				slidesPerGroup: cssVar('--rg-shop-cols-1200', 4),
				spaceBetween:   cssVar('--rg-shop-space-x-1200', 60),
				grid: { rows: cssVar('--rg-shop-rows-1200', 2), fill: 'row' },
			},
		},
		pagination: { el: '.swiper-pagination', clickable: true },
		nested: true,
		on: {
			afterInit:  (s) => adjustRowGap(s),
			breakpoint: (s) => adjustRowGap(s),
		},
	});

};
