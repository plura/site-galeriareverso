<?php

function rg_enqueue( $fancybox = false, $carousel = false, $masonry = false ) {

	$scripts = [];

	if( $fancybox ) {

		$scripts = [ ...$scripts, [
			'https://cdn.jsdelivr.net/npm/@fancyapps/ui@6.0/dist/fancybox/fancybox.umd.js' => ['handle' => 'fancyapps-fancybox'],
			'https://cdn.jsdelivr.net/npm/@fancyapps/ui@6.0/dist/fancybox/fancybox.css' => ['handle' => 'fancyapps-fancybox'],
			'https://cdn.jsdelivr.net/npm/@fancyapps/ui@6.0/dist/panzoom/panzoom.css' => ['handle' => 'fancyapps-panzoom'],
			'https://cdn.jsdelivr.net/npm/@fancyapps/ui@6.0/dist/panzoom/panzoom.umd.js' => ['handle' => 'fancyapps-panzoom']
		]];

	}

	if( $carousel ) {

		$scripts = [ ...$scripts, [
			'https://cdn.jsdelivr.net/npm/@fancyapps/ui@6.0/dist/carousel/carousel.umd.js' => ['handle' => 'fancyapps-carousel'],
			'https://cdn.jsdelivr.net/npm/@fancyapps/ui@6.0/dist/carousel/carousel.css' => ['handle' => 'fancyapps-carousel'],
			'https://cdn.jsdelivr.net/npm/@fancyapps/ui@6.0/dist/carousel/carousel.dots.umd.js' => ['handle' => 'fancyapps-carousel-dots'],
			'https://cdn.jsdelivr.net/npm/@fancyapps/ui@6.0/dist/carousel/carousel.dots.css' => ['handle' => 'fancyapps-carousel-dots']
		]];
		
	}

	plura_wp_enqueue(scripts: $scripts, cache: false);

}



/**
 * Shortcode: [rg-post-meta id="123" type="object"]
 * Renders meta info using the corresponding rg_{type}_meta() function and plura_wp_post_meta().
 */
add_shortcode('rg-post-meta', function( $args ) {

	$atts = shortcode_atts([
		'id'   => null,
		'type' => null
	], $args);

	$post_id = is_numeric($atts['id']) ? (int) $atts['id'] : get_the_ID();

	// Infer type from post type if not provided
	if ( !$atts['type'] && is_singular() ) {
		$atts['type'] = get_post_type( $post_id );
	}

	// Build function name
	$meta_fn = "{$atts['type']}_meta";

	if ( $atts['type'] && function_exists( $meta_fn ) ) {
		$meta = call_user_func( $meta_fn );

		return plura_wp_post_meta(
			post: $post_id,
			meta: $meta,
			label_as_data_attr: true,
			context: 'rg-post-meta'
		);
	}

	return '';
});



/* add_shortcode('plura-wp-gallery', function ($args) {

	$atts = shortcode_atts([
		'id' => null,
		'source_key' => '',
		'source_featured_image' => false
	], $args);

	if (!empty( $atts['source_key'] ) && ( is_numeric($atts['id']) || is_singular('rg_object') ) ) {

		$id = is_numeric($atts['id']) ? (int) $atts['id'] : get_the_ID();

		$add_featured_image = (bool) preg_match('/^(1|true|0|false)$/i', (string)$atts['source_featured_image']);

		return plura_wp_gallery(source: $id, source_key: $atts['source_key'], source_featured_image: $add_featured_image);

	}

	return '';
}); */






function rg_fancybox_trigger(): string {

	$atts1 = ['class' => 'rg-fancybox-wrapper'];

	$atts2 = ['class' => 'rg-fancybox-trigger fa-b'];

	return sprintf('<div %s><div %s></div></div>', plura_attributes($atts1), plura_attributes($atts2));

}





add_shortcode('plura-wp-banner', function( $args ) {

	$atts = shortcode_atts(['source' => null, 'context' => null], $args);

	$atts['source'] = array_filter(array_map('intval', is_array($atts['ids']) ? $atts['ids'] : explode(',', $atts['ids'])));

	return plura_wp_banner( ...$atts );

});


function plura_wp_banner( array|int|WP_Term|WP_Post $source, ?string $context ): ?string {

	$source = (array) $source;

	$html = [];

	foreach( $source as $k => $item ) {

		$item = plura_wp_banner_item( object: $item, context: $context );

		$html[] = $item;

	}

	if( !empty( $html) ) {

		return sprintf('
			<div %s>
				<div %s>
					%s
				</div>
			</div>',
			plura_attributes(['class' => 'rg-banner']),
			plura_attributes(['class' => 'rg-banner-items']),
			implode('', $html)
		);

	}

	return null;

}

function plura_wp_banner_item(int|WP_Term|WP_Post $object, ?string $context): ?string {

	if( is_int( $object) ) {

		$object = get_post( $object );

	}

	if( !$object ) {

		return null;

	}

	$content = [];

	if( $object instanceof WP_Post ) {

		$img = plura_wp_post_featured_image(post: $object);

		if( $img ) {

			$content['featured-image'] = $img;

		}

	}

	$content['title'] = plura_wp_title(object: $object);
	
	$content = apply_filters('plura_wp_banner_item', $content, $object, $context);

	return sprintf('<div %s>%s</div>', plura_attributes(['class' => 'plura-wp-banner-item']), implode('', $content) );

}




/**
 * Shortcode: [rg_post_terms taxonomy="rg_exhibition_category" id="9" sep=" | "]
 * Output: Term Name : 123
 */
function rg_shortcode_post_terms($atts) {
	$atts = shortcode_atts([
		'taxonomy' => 'category',
		'id' => 0,
		'sep' => ' | ',
	], $atts, 'rg_post_terms');

	$post_id = (int) ($atts['id'] ?: get_the_ID());
	if (!$post_id) {
		return '';
	}

	$terms = get_the_terms($post_id, $atts['taxonomy']);
	if (empty($terms) || is_wp_error($terms)) {
		return '';
	}

	$out = [];
	foreach ($terms as $term) {
		$out[] = esc_html($term->name) . ' : ' . (int) $term->term_id;
	}

	return implode($atts['sep'], $out);
}
add_shortcode('rg_post_terms', 'rg_shortcode_post_terms');






