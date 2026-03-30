document.addEventListener('DOMContentLoaded', () => {

	function getCSSVarInt(varName, element = document.documentElement) {
		const styles = getComputedStyle(element);
		const value = styles.getPropertyValue(varName).trim();
		return parseInt(value, 10);
	}




	//Object Page - Gallery
	if ( plura_wp_data.type.match(/rg_(object|publication)/) ) {
		const gallery = document.querySelector('.plura-wp-gallery');

		if (gallery) {
			const items = gallery.querySelectorAll('.plura-wp-gallery-item'), id = plura_wp_data.type.split('_').join('-');

			//if (items.length > 1) {
				gallery.classList.add('f-carousel');

				items.forEach(item => {
					item.classList.add('f-carousel__slide');
					item.querySelector('img').setAttribute('data-fancybox', `${id}-gallery`);
				});

				Carousel(gallery, { /* options */ }, { Dots, Thumbs }).init();

			//}

			Fancybox.bind(`[data-fancybox="${id}-gallery"]`);

		}
	}



	//Masonry for Posts (in individual artist and shop pages )
	document.querySelectorAll(`.plura-wp-posts.rg-masonry`).forEach(posts => {

		const msnry = new Masonry(posts, {
			itemSelector: '.plura-wp-post',
			gutter: getCSSVarInt('--plura-wp-posts-gap', posts)
		});

	});



	//Add lightbox with trigger to Publications, "Other Object by Artist", Artist/Exhibtion/Shop Objects
	const objects = document.querySelectorAll(`.plura-wp-posts:is(
		[data-type="rg_publication"],
		[data-type="rg_object"][data-exclude],
		[data-type="rg_object"][data-rg-artist],
		[data-type="rg_object"][data-rg-shop],
		[data-type="rg_object"][data-context="exhibition"]
	)`);

	if( objects.length ) {

		objects.forEach( (group, index) => {

			const clss = `rg-fancybox-${ Date.now() }-${index}`;

			group.classList.add( clss );

			lightbox({wrapper: `.plura-wp-posts.${ clss } .plura-wp-post`, trigger: '.rg-fancybox-trigger'});

		} );

		const resizeobserver = new ResizeObserver( entries => {

			for( const entry of entries ) {

				const trigger_wrapper = entry.target.closest('.plura-wp-post')?.querySelector('.rg-fancybox-wrapper')

				if( trigger_wrapper ) {

					Object.entries({
						't': entry.target.offsetTop,
						'l': entry.target.offsetLeft,
						'w': entry.target.offsetWidth,
						'h': entry.target.offsetHeight
					})
					.forEach( ([key,value]) => trigger_wrapper.style.setProperty(`--img-${key}`, `${value}px`) );

				}

			}

		});

		document.querySelectorAll(`.plura-wp-posts .plura-wp-post .plura-wp-post-featured-image`).forEach(

			image => resizeobserver.observe(image)

		);

	}


	/* 	const swiper = new Swiper('.rg-banner', {
	  wrapperClass: 'rg-banner-items',
	  slideClass: 'rg-banner-item',
	
	  // Optional: enable common features
	  loop: true,
	  pagination: {
		el: '.swiper-pagination',
		clickable: true
	  },
	  navigation: {
		nextEl: '.swiper-button-next',
		prevEl: '.swiper-button-prev'
	  },
	}); */


});