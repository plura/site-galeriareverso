import animateIntro from './animations/intro.js';
// import shopSlider from './shop.js';

const setatts = (el, params) => Object.entries(params).forEach(([key, value]) => el?.setAttribute(`data-${key}`, value));

const slides = document.querySelectorAll('.rg-intro-slider .swiper-wrapper > .plura-wp-post');
const thumbs = document.querySelectorAll('.rg-intro-slider-thumbs .swiper-wrapper > .plura-wp-post');

[...slides, ...thumbs].forEach(element => element.classList.add(...['swiper-slide', 'rg-intro-slide']));

[...slides].forEach(slide => {

	setatts(slide.querySelector('.plura-wp-title'), { 'swiper-parallax': -400 });
	setatts(slide.querySelector('.rg-datetime'), { 'swiper-parallax': -200 });
	setatts(slide.querySelector('.plura-wp-breadcrumbs'), { 'swiper-parallax': -100 });

});


const swiper = new Swiper('.rg-intro-slider', {
	navigation: {
		nextEl: '.rg-intro-slider-next',
		prevEl: '.rg-intro-slider-prev',
	},
	parallax: true,
	slidesPerView: 1,
	speed: 2000,
});


const swiper_thumbs = new Swiper('.rg-intro-slider-thumbs', {
	slidesPerView: 'auto',
	slideToClickedSlide: true,
	spaceBetween: 20,
	speed: 2000,
	centeredSlides: true
});


swiper.controller.control = swiper_thumbs;
swiper_thumbs.controller.control = swiper;


const timelines = [];

swiper.slides.forEach((slide, index) => {
	const type = slide.dataset.slideType;

	// Per-slide setup
	// if (type === 'shop') shopSlider(slide);

	// GSAP timeline
	const tl = gsap.timeline({
		paused: true,
		defaults: { ease: "power2.out", duration: 0.6 }
	});

	if (type === 'intro') animateIntro(tl);

	timelines[index] = tl;
});


const playTimelineForSlide = (activeIndex) => {
	timelines.forEach((tl, i) => {
		if (i === activeIndex) {
			tl.play();
		} else {
			if (tl.progress() > 0 && !tl.reversed()) {
				tl.reverse();
			}
		}
	});
};


swiper.on('slideChangeTransitionStart', () => {
	playTimelineForSlide(swiper.realIndex);
});

timelines[swiper.realIndex]?.play();
