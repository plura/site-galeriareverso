<?php


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









function rg_fancybox_trigger(): string {

	$atts1 = ['class' => 'rg-fancybox-wrapper'];

	$atts2 = ['class' => 'rg-fancybox-trigger fa-b'];

	return sprintf('<div %s><div %s></div></div>', plura_attributes($atts1), plura_attributes($atts2));

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






