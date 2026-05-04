const cssVar = (name) => parseFloat(getComputedStyle(document.documentElement).getPropertyValue(name));

const adjustRowGap = (swiper) => {
	const cols  = swiper.params.slidesPerView;
	const rows  = swiper.params.grid?.rows ?? 1;
	const group = cols * rows;
	swiper.slides.forEach((slide, i) => {
		slide.classList.toggle('rg-row-2', i % group >= cols);
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
		grid: { rows: 3, fill: 'row' },
		slidesPerView: 2,
		slidesPerGroup: 2,
		spaceBetween: cssVar('--rg-shop-space-x-base'),
		speed: 800,
		breakpoints: {
			768:  { slidesPerView: 3, slidesPerGroup: 3, spaceBetween: cssVar('--rg-shop-space-x-768'),  grid: { rows: 3, fill: 'row' } },
			1200: { slidesPerView: 4, slidesPerGroup: 4, spaceBetween: cssVar('--rg-shop-space-x-1200'), grid: { rows: 2, fill: 'row' } },
		},
		nested: true,
		on: {
			afterInit:  (s) => adjustRowGap(s),
			breakpoint: (s) => adjustRowGap(s),
		},
	});

};
