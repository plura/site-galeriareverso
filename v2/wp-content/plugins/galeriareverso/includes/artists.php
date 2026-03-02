<?php


//https://wordpress.stackexchange.com/questions/120407/how-to-fix-pagination-for-custom-loops
//https://egtutorial.com/howto/how-to-add-numeric-or-numbered-pagination-in-wordpress/

function rg_artists_posts_where( $where ) {
	
	//$where = str_replace("meta_key = 'speakers_$", "meta_key LIKE 'speakers_%", $where);
	$where = str_replace("meta_key = 'rg_exhibition_artists_$", "meta_key LIKE 'rg_exhibition_artists_%", $where);

	return $where;

}

add_filter('posts_where', 'rg_artists_posts_where');


// Add shortcode for displaying artists
add_shortcode('rg-artists2', function($args) {

	$atts = shortcode_atts([
		'external' => '',
		'limit' => -1,
		'rand' => 0
	], $args);

	$const = ['type' => 'rg_artist', 'link' => 1];

	// Normalize and check if 'external' is set
	if (isset($args['external'])) {

		$external = filter_var($atts['external'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

		if ($external !== null) {
			$const['params'] = [
				'external' => $external
			];
		}
	}

	if( !$atts['rand'] ) {
		
		$const = [ ...$const, 'orderby' => 'name', 'order' => 'ASC' ];

	}
	
	unset($atts['external']);

	$params = [...$atts, ...$const];

	return plura_wp_posts(...$params);

});


// Filter to modify the post entry for 'rg_artist' post type
add_filter('plura_wp_post', function( array $entry, WP_Post $post, ?string $context = null ): array {

	if( get_post_type( $post ) === 'rg_artist' ) {

		$a = [];

		foreach ( ['featured-image', 'title'] as $key) {

			if( array_key_exists( $key, $entry ) ) {

				$a[] = $entry[ $key ];

			}

		}

		return $a;

	}

	return $entry;

}, 10, 3 );





/**
 * Returns an HTML link to the artist's biography file.
 *
 * Expects an ACF field 'artist_bio' to be set with a File field (returns array with 'url').
 *
 * @param int $artistID ID of the artist post.
 * @return string HTML link or empty string if not found.
 */
function rg2_artists_bio( int $artistID ): string {
	$bio = get_field('artist_bio', plura_wpml_id( $artistID ) );

	if ( is_array($bio) && !empty($bio['url']) ) {
		$atts = ['class' => 'rg-artist-bio', 'title' => __('Biography', 'rg')];
		return plura_wp_link(html: __('Biography', 'rg'), target: $bio['url'], atts: $atts);
	}

	return '';
}

// Register shortcode [rg2-artist-bio id="123"]
add_shortcode('rg2-artist-bio', function( $args ) {
	$atts = shortcode_atts([
		'id' => get_the_ID(),
	], $args);

	if ( is_numeric($atts['id']) ) {
		return rg2_artists_bio( (int) $atts['id'] );
	}

	return '';
});





//get all exhibitions related to an artist
function rg2_artist_exhibitions( int $artistID ):string {

	$query = new WP_Query([
		'post_type' => 'rg_exhibition',
		'posts_per_page' => -1,
		'meta_query' => [
			[
				'key' => 'rg_exhibition_artists_$_rg_exhibition_artist',
				'value' => plura_wpml_id( $artistID ),
				'compare' => 'LIKE'
			]
		]
	]);

	if( $query->have_posts() ) {

		$html = [];

		foreach( $query->posts as $post ) {

			$html[] = plura_wp_link(
				html: $post->post_title,
				target: $post,
				atts: ['class' => 'rg-artist-exhibition']
			);

		}

		$atts = [
			'class' => 'rg-artist-exhibitions',
			'data-label' => __('Exhibitions', 'rg'),
		];

		return sprintf('<div %s>%s</div>', plura_attributes( $atts ), implode('', $html) );

	}

	return '';

}

add_shortcode('rg2-artist-exhibitions', function( $args ) {

	$atts = shortcode_atts([
		'id' => get_the_ID(),
	], $args);

	if ( is_numeric($atts['id']) ) {
		return rg2_artist_exhibitions( (int) $atts['id'] );
	}

	return '';

} );







// Filter to modify the query parameters for GR artists
add_filter('plura_wp_posts_query', function (array $query_params, array $args) {

	if ($args['type'] === 'rg_artist') {

		$meta = [];

		if (!isset($args['params']['external']) || !$args['params']['external']) { 
			$meta[] = [
				'key'     => 'rg_artist_rg',
				'value'   => '1'/* ,
				'compare' => '=' */
			];
		}

		if (!empty($meta)) {
			$query_params['meta_query'] = array_merge(
				$query_params['meta_query'] ?? [],
				$meta
			);
		}

	}

	return $query_params;
}, 10, 2);
