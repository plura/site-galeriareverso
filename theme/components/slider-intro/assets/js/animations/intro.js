export default (tl) => {

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

};
