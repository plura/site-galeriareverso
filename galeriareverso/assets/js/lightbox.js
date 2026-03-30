function lightbox({ wrapper, trigger, getter, dynamic = false }) {
	const triggers = document.querySelectorAll(trigger);

	const getGallery = () => {
		// Use custom getter if provided
		if (typeof getter === 'function') {
			const result = getter({ wrapper, trigger });
			return Array.isArray(result) ? result : [];
		}

		// Default getter: from triggers
		return Array.from(triggers).map(element => {
			const img = element.closest(wrapper)?.querySelector('img');

			if (img) {
				return {
					src: img.getAttribute('src'),
					type: 'image',
					alt: img.getAttribute('alt') || '',
					thumbSrc: img.dataset.thumb || img.getAttribute('src'),
					custom: {
						trigger: element,
						image: img
					}
				};
			}

			return false;
		});
	};

	let images;

	const update = () => {
		images = getGallery();
	};

	if (!dynamic) {
		update();
	}

	triggers.forEach((button, index) => {
		button.addEventListener('click', event => {
			// Prevent navigation if the trigger is inside an <a href="...">
			event.preventDefault();

			if (dynamic) {
				update();
			}

			if (images[index]) {
				Fancybox.show(images, {
					startIndex: index,
					triggerEl: images[index].custom?.image,
					Thumbs: {
						autoStart: true // ✅ thumbs plugin enabled
					}
				});
			}
		});
	});
}

// Example usage
/* lightbox({
	wrapper: '.plura-wp-post',
	trigger: '.rg-lighbox-trigger'
});
 */