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
		spaceBetween: 12,
		speed: 800,
		breakpoints: {
			768:  { slidesPerView: 3, slidesPerGroup: 3, spaceBetween: 20, grid: { rows: 2, fill: 'row' } },
			1200: { slidesPerView: 4, slidesPerGroup: 4, spaceBetween: 28, grid: { rows: 2, fill: 'row' } },
		},
		nested: true
	});

};
