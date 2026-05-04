const spacing = {
	base: { x: 12, y: 6  },
	768:  { x: 20, y: 8  },
	1200: { x: 40, y: 10 },
};

const getSpacingY = (swiper) => {
	const cols = swiper.params.slidesPerView;
	if (cols >= 4) return spacing[1200].y;
	if (cols >= 3) return spacing[768].y;
	return spacing.base.y;
};

const adjustRowGap = (swiper) => {
	const cols  = swiper.params.slidesPerView;
	const rows  = swiper.params.grid?.rows ?? 1;
	const group = cols * rows;
	const y     = getSpacingY(swiper);
	swiper.slides.forEach((slide, i) => {
		if (i % group >= cols) slide.style.marginTop = `${y}px`;
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

	new Swiper(postsEl, {
		grid: { rows: 2, fill: 'row' },
		slidesPerView: 2,
		slidesPerGroup: 2,
		spaceBetween: spacing.base.x,
		speed: 800,
		breakpoints: {
			768:  { slidesPerView: 3, slidesPerGroup: 3, spaceBetween: spacing[768].x,  grid: { rows: 2, fill: 'row' } },
			1200: { slidesPerView: 4, slidesPerGroup: 4, spaceBetween: spacing[1200].x, grid: { rows: 2, fill: 'row' } },
		},
		nested: true,
		on: {
			afterInit:  (s) => adjustRowGap(s),
			breakpoint: (s) => adjustRowGap(s),
		},
	});

};
