export default (slideEl) => {

	const postsEl = slideEl.querySelector('.plura-wp-posts');
	const posts   = [...postsEl.querySelectorAll(':scope > .plura-wp-post')];

	postsEl.classList.add('swiper', 'rg-shop-swiper');

	let shopSwiper = null;

	const getGroupSize = () => {
		if (window.innerWidth >= 1200) return 8;
		if (window.innerWidth >= 768)  return 6;
		return 4;
	};

	const regroup = () => {

		if (shopSwiper) {
			shopSwiper.destroy(true, true);
			shopSwiper = null;
		}

		const existingWrapper = postsEl.querySelector('.swiper-wrapper');
		if (existingWrapper) {
			posts.forEach(post => postsEl.appendChild(post));
			existingWrapper.remove();
		}

		const size    = getGroupSize();
		const wrapper = document.createElement('div');
		wrapper.classList.add('swiper-wrapper');

		for (let i = 0; i < posts.length; i += size) {
			const slide = document.createElement('div');
			slide.classList.add('swiper-slide');
			posts.slice(i, i + size).forEach(post => slide.appendChild(post));
			wrapper.appendChild(slide);
		}

		postsEl.appendChild(wrapper);

		shopSwiper = new Swiper(postsEl, {
			slidesPerView: 1,
			speed: 800,
		});

	};

	new ResizeObserver(regroup).observe(document.documentElement);

};
