<?php

function rg_publication_meta(): array {
	
	return [
		'title' => ['key' => 'rg_publication_title', 'label' => __('Title', 'rg')],
		'author' => ['key' => 'rg_publication_author', 'label' => __('Author', 'rg')],
		'publisher' => ['key' => 'rg_publication_publisher', 'label' => __('Publisher', 'rg')],
		'designer' => ['key' => 'rg_publication_designer', 'label' => __('Designer', 'rg')], 
		'year' => ['key' => 'rg_publication_year', 'label' => __('Year', 'rg')],
		'pages' => ['key' => 'rg_publication_pages', 'label' => __('Pages', 'rg')],
		'languages' => ['key' => 'rg_publication_languages', 'label' => __('Languages', 'rg')],
		'type' => ['key' => 'rg_publication_type', 'label' => __('Type', 'rg')]
	];

}


// Filter to modify the post entry for rg_publications
add_filter('plura_wp_post', function (array $entry, WP_Post $post, ?string $context = null): array {

	if (get_post_type($post) === 'rg_publication') {

		$a = [];

		foreach(['featured-image', 'title'] as $k) {

			if( array_key_exists($k, $entry) ) {

				$a[ $k ] = $entry[$k];

			}

		}

		if( $context === "publications-more") {

			$meta = array_intersect_key(rg_publication_meta(), array_flip(['title', 'author', 'publisher', 'year']));

			$a['meta'] = plura_wp_post_meta(post: $post, meta: $meta, label_as_data_attr: true, context: $context);

		}

		array_unshift( $a, rg_fancybox_trigger() );

		return $a;

	}

	return $entry;
}, 10, 3);




// Filter to modify the post entry for rg_object posts
/* add_filter('plura_wp_post', function (array $entry, WP_Post $post, ?string $context = null): array {

	if (get_post_type($post) === 'rg_object') {

		$a = [];

		foreach(['featured-image', 'title'] as $key) {

			if( array_key_exists($key, $entry) ) {

				$a[$key] = $entry[$key];

			}

		}


		$meta = [];

		foreach (rg_object_meta() as $key => $item) {

			if ($item['key'] === 'rg_object_title') {

				continue; // Skip title meta, as it will be handled separately

			} else if ($item['key'] === 'rg_object_artist' && ( is_singular('rg_artist') || is_singular('rg_object') ) ) {

				continue; // Skip artist meta if we're already on an artist page or object page - on object pages only related artist objects will be shown

			}

			$meta[$key] = $item;
		}

		$a['meta'] = plura_wp_post_meta(post: $post, meta: $meta, label_as_data_attr: true, context: $context);

		return $a;
	}

	return $entry;
}, 10, 3); */


add_shortcode('rg2-publication-info', function( $args ) {

	$atts = shortcode_atts([
		'id'  => null,
	], $args);

	if (is_numeric($atts['id']) || is_singular('rg_object')) {

		$atts['id'] = is_numeric($atts['id']) ? (int) $atts['id'] : get_the_ID();

		$meta = array_filter( rg2_publication_meta(), fn( $value ) => $value['key'] !== 'rg_object_title');

		return plura_wp_post_meta(post: $atts['id'], meta: $meta, label_as_data_attr: true);

	}

});
