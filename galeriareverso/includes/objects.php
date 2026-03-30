<?php



//change permalink structure - /objects/postID
//https://wordpress.stackexchange.com/a/158224



// Custom permalink structure for rg_object: /objects/123
add_filter('post_type_link', function( string $link, WP_Post $post ) {
	if ( $post->post_type === 'rg_object' ) {
		return home_url( 'objects/' . $post->ID . '/' );
	}
	return $link;
}, 10, 2);

// Add rewrite rule to map /objects/123 to the rg_object post
add_action( 'init', function() {
	add_rewrite_rule(
		'^objects/([0-9]+)/?$',
		'index.php?post_type=rg_object&p=$matches[1]',
		'top'
	);
});


//change object title
//https://www.cyberciti.biz/programming/how-to-customize-title-in-wordpress-themes-using-pre_get_document_title/
add_filter('pre_get_document_title', function( string $title ) {

	     if ( is_singular('rg_object') ) {

		return __('Object', 'rg') . ' ' . get_the_ID() . ' - ' . get_bloginfo('name');
     
     }

     return $title;

}, 999, 1);




function rg_object_meta(): array {

	return [
		'title'      => ['key' => 'rg_object_title',      'label' => __('Title', 'rg')],
		'type'       => ['key' => 'rg_object_type',       'label' => __('Type', 'rg')],
		'materials'  => ['key' => 'rg_object_materials',  'label' => __('Materials', 'rg')],
		'year'       => ['key' => 'rg_object_year',       'label' => __('Year', 'rg')],
		'dimensions' => ['key' => 'rg_object_dimensions', 'label' => __('Dimensions', 'rg')],
		'price'      => ['key' => 'rg_object_price',      'label' => __('Price', 'rg')],
		'artist'     => [
			'key'               => 'rg_object_artist',
			'label'             => __('Artist', 'rg'),
			'raw_html'          => true,
			'sanitize_callback' => fn($val) =>
				$val instanceof WP_Post ? plura_wp_link(html: $val->post_title, target: $val) : null,
		],
	];
	
}


// Shortcode to display RG Objects (CPT).
// Supports artist filtering (auto or manual), exclusions, context, shop mode, labels, and optional linking.
add_shortcode('rg2-objects', function ($args) {

	$atts = shortcode_atts([
		'artist'  => 0,
		'class'	  => '',
		'context' => null,
		'exclude' => [],
		'ids'     => [],
		'label'   => '',
		'link'    => 0,
		'limit'   => -1,
		'rand'    => 0,
		'shop'    => 0,
	], $args);

	// Normalize artist
	$artist_id = null;
	if (preg_match('/^(1|true)$/i', (string)$atts['artist'])) {
		if (is_singular('rg_artist')) {
			$artist_id = get_the_ID();
		}
		// If on a single RG Object, try to get the artist via ACF (rg_object_artist)
		elseif (is_singular('rg_object')) {
			$artist = get_field('rg_object_artist', get_the_ID());
			if ($artist) {
				$artist_id = $artist->ID;
			}
		}
	} elseif (is_numeric($atts['artist'])) {
		$artist_id = (int) $atts['artist'];
	}

	// Normalize exclude
	$exclude = [];
	if (is_string($atts['exclude']) && preg_match('/^(1|true)$/i', $atts['exclude'])) {
		if (is_singular('rg_object')) {
			$exclude[] = get_the_ID();
		}
	} else {
		$exclude = array_filter(array_map('intval', is_array($atts['exclude']) ? $atts['exclude'] : explode(',', $atts['exclude'])));
	}

	// Normalize ids
	$ids = array_filter(array_map('intval', is_array($atts['ids']) ? $atts['ids'] : explode(',', $atts['ids'])));

	// Normalize shop
	$is_shop = (bool) preg_match('/^(1|true)$/i', (string)$atts['shop']);

	// Normalize context
	$context = trim((string)$atts['context']) ?: null;

	// Normalize limit
	$limit = is_numeric($atts['limit']) ? (int) $atts['limit'] : -1;

	// Normalize link: must be -1, 0, or 1
	$link = in_array((int) $atts['link'], [-1, 0, 1], true) ? (int) $atts['link'] : 0;

	// Normalize rand
	$rand = filter_var($atts['rand'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

	$args = [
		'type'    => 'rg_object',
		'link'    => $link,
		'ids'     => $ids,
		'exclude' => $exclude,
		'context' => $context,
		'label'	  => $atts['label'],
		'limit'   => $limit,
		'rand'	  => $rand,
		'params'  => [
			'artist' => $artist_id,
			'shop'   => $is_shop,
		],
		'class'	=> $atts['class']
	];

	$data = [];

	if ($is_shop) {
		$data['data-rg-shop'] = 1;
	}

	if ($artist_id) {
		$data['data-rg-artist'] = $artist_id;
	}

	if (!empty($data)) {
		$args['data'] = $data;
	}

	return plura_wp_posts(...$args);
});



add_shortcode('rg-gallery', function( $args ) {

	$atts = shortcode_atts( [
		'ids' => [],
	], $args );

	// Normalize ids
	$ids = array_filter(array_map('intval', is_array($atts['ids']) ? $atts['ids'] : explode(',', $atts['ids'])));

	return plura_wp_posts(ids: $ids, type: 'rg_object', context: 'exhibition', class: 'rg-masonry');


});


// Filter to modify the query parameters for rg_object posts
add_filter('plura_wp_posts_query', function (array $query_params, array $args) {

	if ($args['type'] === 'rg_object') {

		$meta = [];

		if (!empty($args['params']['artist'])) {
			$meta[] = [
				'key'     => 'rg_object_artist',
				'value'   => plura_wpml_id($args['params']['artist']),
				'compare' => '='
			];
		}

		if (!empty($args['params']['shop'])) {
			$meta[] = [
				'key'   => 'rg_object_shop',
				'value' => 1
			];
		}

		if (!empty($meta)) {
			$query_params['meta_query'] = array_merge(
				$query_params['meta_query'] ?? [],
				$meta
			);
		}

		//print_r($args);
	}

	return $query_params;
}, 10, 2);



// Filter to modify the post entry for rg_object posts
add_filter('plura_wp_post', function (array $entry, WP_Post $post, ?string $context = null): array {

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


		array_unshift( $a, rg_fancybox_trigger() );

		return $a;
	}

	return $entry;
}, 10, 3);




// Filter to modify the post title for rg_object posts
add_filter('plura_wp_title', function (string $title, WP_Post|WP_Term $post, ?string $context): string {

	if (get_post_type($post) === 'rg_object') {

		$meta_title = get_field('rg_object_title', $post->ID);

		if (!empty($meta_title)) {

			return $meta_title;
		}

		return __('Untitled', 'rg');
	}

	return $title;
}, 10, 3);


// Filter to modify the post meta value for rg_object_artist. Note: This is commented out as it is not be needed in the current context.
// I'm using 'rg_object_meta' to handle the artist link instead.
/* add_filter('plura_wp_post_meta_item_value', function (mixed $value, WP_Post $post, string $meta_key, ?string $context) {

	if (get_post_type($post) === 'rg_object' && $meta_key === 'rg_object_artist') {

		$artist = get_field('rg_object_artist', $post->ID);

		if ($artist) {

			$value = plura_wp_link(html: $artist->post_title, target: $artist);
		}
	}

	return $value;
}, 10, 4); */


add_filter( 'plura_wp_post_featured_image', function( $html, $post, $size, $atts ) {

	if( !$html && get_post_type( $post ) === 'rg_object' ) {

		$gallery = get_field('rg_object_images', $post->ID);

		if (is_array($gallery) && !empty($gallery[0]['ID'])) {
			return plura_wp_image( $gallery[0]['ID'], $size, $atts );
		}

	}

	return $html;

}, 10, 4 );
