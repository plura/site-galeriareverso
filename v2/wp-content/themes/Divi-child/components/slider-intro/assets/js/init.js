document.addEventListener('DOMContentLoaded', () => {

	const setatts = (el, params) => Object.entries(params).forEach(([key, value]) => el?.setAttribute(`data-${key}`, value));

	const slides = document.querySelectorAll('.rg-slider :is(.swiper-slide, .plura-wp-post)');
	const thumbs = document.querySelectorAll('.rg-slider-thumbs :is(.swiper-slide, .plura-wp-post)');

	[...slides, ...thumbs].forEach(element => element.classList.add(...['swiper-slide', 'rg-slide']));

	[...slides].forEach(slide => {

		setatts(slide.querySelector('.plura-wp-title'), { 'swiper-parallax': -400 });
		setatts(slide.querySelector('.rg-datetime'), { 'swiper-parallax': -200 });
		setatts(slide.querySelector('.plura-wp-breadcrumbs'), { 'swiper-parallax': -100 });


	});


	const swiper = new Swiper('.rg-slider', {
		navigation: {
			nextEl: '.rg-slider-next',
			prevEl: '.rg-slider-prev',
		},
		parallax: true,
		slidesPerView: 1,
		speed: 2000,
		loop: false,
	});


	const swiper_thumbs = new Swiper('.rg-slider-thumbs', {
		slidesPerView: 'auto'/* thumbs.length */,
		slideToClickedSlide: true,
		spaceBetween: 20,
		speed: 2000,
		loop: false,
		centeredSlides: true
	});


	swiper.controller.control = swiper_thumbs;
	swiper_thumbs.controller.control = swiper;


	thumbs.forEach((element, index) => {

		element.addEventListener('click', event => {
			swiper.slideTo(index)
		});

	});


	//animation
	const timelines = [];

	swiper.slides.forEach((slide, index) => {
		const type = slide.dataset.slideType;
		const tl = gsap.timeline({
			paused: true,
			defaults: { ease: "power2.out", duration: 0.6 }
		});

		// --- Unique intro animation
		if (type === 'intro') {
			tl.from("#rg-logo-square", {
				opacity: 0,
				scale: 0.8,
				transformOrigin: "center",
				ease: "back.out(1.7)",
				duration: 0.8
			});

			tl.from([
				"#rg-logo-letter-r1",
				"#rg-logo-letter-e1",
				"#rg-logo-letter-v",
				"#rg-logo-letter-e2",
				"#rg-logo-letter-r2",
				"#rg-logo-letter-s",
				"#rg-logo-letter-o"
			], {
				opacity: 0,
				y: 20,
				stagger: {
					amount: 0.5,
					from: "start"
				}
			}, "-=0.3");
		}

		// --- Shared/global animation: fade in .plura-wp-title
		const title = slide.querySelector('.plura-wp-title');
		if (title) {
			/* tl.fromTo(title, { opacity: 0, y: 50 }, { opacity: 1, y: 0, duration: 1 }, 0); */
		}

		timelines[index] = tl;
	});



	const playTimelineForSlide = (activeIndex) => {
		timelines.forEach((tl, i) => {
			if (i === activeIndex) {
				tl.play();
			} else {
				// Only reverse if it's currently playing forward
				if (tl.progress() > 0 && !tl.reversed()) {
					tl.reverse();
				}
			}
		});
	};



	// on main swiper
	swiper.on('slideChangeTransitionStart', () => {
		playTimelineForSlide(swiper.realIndex);
	});

	// on thumbs swiper (to catch manual clicks)
	swiper_thumbs.on('slideChangeTransitionStart', () => {
		playTimelineForSlide(swiper.realIndex);
	});

	timelines[swiper.realIndex]?.play();

});
