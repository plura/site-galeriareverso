document.addEventListener("DOMContentLoaded", () => {
	// === GSAP LOGO INTRO ANIMATION ===
	const tl = gsap.timeline({
		delay: 1,
		defaults: {
			ease: "power2.out",
			duration: 0.5
		}
	});

	// Animate PURPLE letters
	tl.from([
		"#ph-logo-w1-p1", "#ph-logo-w1-u", "#ph-logo-w1-r",
		"#ph-logo-w1-p2", "#ph-logo-w1-l", "#ph-logo-w1-e"
	], {
		opacity: 0,
		y: 40,
		stagger: {
			amount: 0.4,
			from: "start"
		}
	})

	// Animate honeycombs
	.from([
		"#ph-logo-honeycomb-1", "#ph-logo-honeycomb-2", "#ph-logo-honeycomb-3",
		"#ph-logo-honeycomb-4", "#ph-logo-honeycomb-5", "#ph-logo-honeycomb-6"
	], {
		opacity: 0,
		scale: 0,
		stagger: {
			amount: 0.4,
			from: "random"
		}
	}, "-=0.4")

	// Animate HONEY letters
	.from([
		"#ph-logo-w2-h", "#ph-logo-w2-n", "#ph-logo-w2-e", "#ph-logo-w2-y"
	], {
		opacity: 0,
		y: 40,
		stagger: {
			amount: 0.3,
			from: "start"
		}
	}, "-=0.3");

	// === GSAP TAGLINE ANIMATION ===

	// Animate first sentence: "Buzzing Performance."
	tl.from([
		"#ph-tagline-s1-w1-b", "#ph-tagline-s1-w1-u", "#ph-tagline-s1-w1-z1",
		"#ph-tagline-s1-w1-z2", "#ph-tagline-s1-w1-i", "#ph-tagline-s1-w1-n", "#ph-tagline-s1-w1-g",
		"#ph-tagline-s1-w2-p", "#ph-tagline-s1-w2-e1", "#ph-tagline-s1-w2-r1", "#ph-tagline-s1-w2-f",
		"#ph-tagline-s1-w2-o", "#ph-tagline-s1-w2-r2", "#ph-tagline-s1-w2-m", "#ph-tagline-s1-w2-a",
		"#ph-tagline-s1-w2-n", "#ph-tagline-s1-w2-c", "#ph-tagline-s1-w2-e2",
		"#ph-tagline-s1-dot"
	], {
		opacity: 0,
		y: 30,
		stagger: {
			amount: 0.6,
			from: "start"
		}
	}, "+=0.2");

	// Animate second sentence: "Sweet Returns."
	tl.from([
		"#ph-tagline-s2-w1-s", "#ph-tagline-s2-w1-w", "#ph-tagline-s2-w1-e1", "#ph-tagline-s2-w1-e2", "#ph-tagline-s2-w1-t",
		"#ph-tagline-s2-w2-r1", "#ph-tagline-s2-w2-e", "#ph-tagline-s2-w2-t", "#ph-tagline-s2-w2-u",
		"#ph-tagline-s2-w2-r2", "#ph-tagline-s2-w2-n", "#ph-tagline-s2-w2-s", "#ph-tagline-s2-dot"
	], {
		opacity: 0,
		y: 30,
		stagger: {
			amount: 0.5,
			from: "start"
		}
	}, "+=0.2");
});
