document.addEventListener('DOMContentLoaded', () => {



	//replace language names with their country code only
	document.querySelectorAll('header :is(.nav, .mobile_nav) .menu-item.wpml-ls-menu-item').forEach( element => {

		//Safari does not allow look behind...
		//const lng = element.getAttribute('class').match(/(?<=wpml-ls-item-)([a-z]+)/)[0];
		const lng = element.getAttribute('class').match(/(wpml-ls-item-)([a-z]+)/)[2];

		element.querySelector('span').textContent = lng;

	});


	//Contacts
	if( p.page('wpmlobj-id-1732') ) {



			const coords = [38.70829937744116, -9.154438040991973], map = L.map('map-holder').setView(coords, 15);

			//https://maps.omniscale.com/en/admin/keys/galeriareverso-e44d44e8/complete
			L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
		    	
		    	attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
		
			})


			/*L.tileLayer('https://maps.omniscale.net/v2/galeriareverso-e44d44e8/style.grayscale/{z}/{x}/{y}.png', {
            	id: 'galeriareverso-e44d44e8',
				attribution: '&copy; 2022 &middot; <a href="https://maps.omniscale.com/">Omniscale</a> ' +
            '&middot; Map data: <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
          	}) */

			.addTo( map );

			L.marker(coords).addTo(map);

	}


});