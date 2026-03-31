<?php

add_shortcode('rg2-exhibitions', function( $args ) {

	$atts = shortcode_atts([
		'context' => '',
		'terms' => ''
	], $args);

	if( !empty( $atts['terms'] ) ) {

		// Normalize context
		$context = trim((string)$atts['context']) ?: null;

		// Normalize terms
		$terms = array_filter(array_map('intval', is_array($atts['terms']) ? $atts['terms'] : explode(',', $atts['terms'])));

		$args = [
			'context' => $context,
			'taxonomy' => 'rg_exhibitions_category',
			'terms'	=> $terms,
			'type' => 'rg_exhibition'
		];

		return plura_wp_posts( ...$args );

	}

});


add_shortcode('rg2-exhibitions-archive', function( $args ) {

	$atts = shortcode_atts([
		'exclude' => [],
		'terms' => [],
		'test' => false
	], $args);

	// Normalize exclude params/ids
	$exclude = array_filter(array_map('intval', is_array($atts['exclude']) ? $atts['exclude'] : explode(',', $atts['exclude'])));
	
	// Normalize terms params/ids
	$terms = array_filter(array_map('intval', is_array($atts['terms']) ? $atts['terms'] : explode(',', $atts['terms'])));	

	$atts = [
		'exclude' => $exclude,
		'terms' => $terms
	];

	return rg2_exhibitions_archive( ...$atts );

});



//https://developer.wordpress.org/reference/functions/add_query_arg/
//https://wordpress.stackexchange.com/questions/51444/add-extra-parameters-after-permalink
function rg2_exhibitions_archive( array|int $exclude = [], array|int $terms = [], bool $test = false ) {

	global $wp_query;

	$link = get_permalink( get_the_ID() );

	if( $test ) {

		$test_values = [];
		
		foreach(['rg_exhibition_year', 'lang', 'test'] as $query_param) {

			if( $wp_query->get($query_param) ) {
				
				$test_values[$query_param] = $wp_query->get($query_param);

			}

		}

		print_r( $test_values );

	}

	//get all exhibitions from a specific year
	//https://stackoverflow.com/questions/63870725/filter-custom-post-type-based-on-acf-date-field-value
	if( $wp_query->get('rg_exhibition_year') ) {

		$params = [ 'exclude' => $exclude, 'year' => $wp_query->get('rg_exhibition_year')];
		
		return plura_wp_posts(
			limit: -1,
			params: $params,
			type: 'rg_exhibition',
			context: 'rg-exhibitions-year'
		);

	}

	return rg2_exhibitions_years_grid(exclude: $exclude, link: $link);

}




//get all different years with exhibitions 
//https://wordpress.stackexchange.com/a/112120
function rg2_exhibitions_years_grid(array $exclude = [], string $link = '' ) :string {

	global $wpdb;

	$query_vars = '';

	if( empty( $link ) ) {

		$link = get_permalink( get_the_ID() );

	}

	// Extract query string from link (e.g. ?lang=pt-pt added by WPML)
	// so the year segment is inserted before it, not after
	if( ($qpos = strpos($link, '?')) !== false ) {
		$query_vars = substr($link, $qpos);
		$link       = substr($link, 0, $qpos);
	}

	$link = trailingslashit( $link );

	// Build optional exclude clause (by taxonomy term IDs)
	$exclude_clause = '';
	if( !empty( $exclude ) ) {
		$ids = implode( ',', array_map( 'intval', (array) $exclude ) );
		$exclude_clause = "AND p.ID NOT IN (
			SELECT object_id
			FROM {$wpdb->term_relationships} tr
			INNER JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
			WHERE tt.taxonomy = 'rg_exhibitions_category'
			AND tt.term_id IN ($ids)
		)";
	}

	// Filter by current WPML language if active
	$wpml_join  = '';
	$wpml_where = '';
	if( defined('ICL_LANGUAGE_CODE') ) {
		$wpml_join  = "INNER JOIN {$wpdb->prefix}icl_translations t ON t.element_id = p.ID AND t.element_type = 'post_rg_exhibition'";
		$wpml_where = $wpdb->prepare( "AND t.language_code = %s", ICL_LANGUAGE_CODE );
	}

	// Single query: one row per year + one representative post_id for the thumbnail
	$rows = $wpdb->get_results("
		SELECT YEAR(pm.meta_value) AS year, MIN(pm.post_id) AS post_id
		FROM {$wpdb->postmeta} pm
		INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
		$wpml_join
		WHERE pm.meta_key = 'rg_exhibition_date_start'
		  AND p.post_type = 'rg_exhibition'
		  AND p.post_status = 'publish'
		  $wpml_where
		  $exclude_clause
		GROUP BY YEAR(pm.meta_value)
		ORDER BY year DESC
	");


	if( empty( $rows ) ) return '';

	$html = [];

	foreach( $rows as $row ) {

		$html_year = [];

		if( $img = get_post_thumbnail_id( $row->post_id ) ) {

			$html_year[] = plura_wp_image( attachment: $img, atts: ['class' => 'rg-exhibitions-year-featured-image'] );

		}

		$html_year[] = sprintf('<h3 %s>%s</h3>', plura_attributes(['class' => 'rg-exhibitions-year-title']), $row->year);

		$html[] = plura_wp_link(
			html: implode("\n", $html_year),
			target: $link . $row->year . '/' . $query_vars,
			title: $row->year,
			atts: [
				'class' => 'rg-exhibitions-year',
				'data-exhibition-year' => $row->year
			]
		);

	}

	return sprintf(
		'<div %s>%s</div>',
		plura_attributes(['class' => 'rg-exhibitions-years']),
		implode("\n", $html)
	);

}


// Filter to modify the query parameters for rg_object posts
add_filter('plura_wp_posts_query', function (array $query_params, array $args) {

	if ($args['type'] === 'rg_exhibition') {

		$query_params = array_merge($query_params, [
			'meta_key' => 'rg_exhibition_date_start',
			'orderby'  => 'meta_value',
			'order'  	=> 'DESC'
		]);

		$tax_query = [];

		$meta_query = [];

		if( !empty( $args['params']['exclude'] ) ) {

			//$terms_exclude = rg_wpml_id( explode(',', $args['terms']), false, 'rg_exhibitions_category' ); 

			$tax_query[] = [
				'taxonomy' => 'rg_exhibitions_category',
				'field' => 'term_id',
				'terms' => $args['params']['exclude'],//$terms_exclude,
				'operator' => 'NOT IN'
			];

		}

		if( !empty( $args['params']['year'] ) ) {

			$year = $args['params']['year'];

			array_push( $meta_query,

				[
					'key'           => 'rg_exhibition_date_start',
					'compare'       => '>=',
					'value'         => DateTime::createFromFormat('d/m/Y', "01/01/$year")->format('Y-m-d H:i:s'),
					'type'          => 'DATETIME',
				],
				
				[
					'key'           => 'rg_exhibition_date_start',
					'compare'       => '<=',
					'value'         => DateTime::createFromFormat('d/m/Y', "31/12/$year")->format('Y-m-d H:i:s'),
					'type'          => 'DATETIME',
				]

			);

		}

		foreach( ['tax_query' => $tax_query, 'meta_query' => $meta_query] as $params_key => $params_group ) {

			if( empty( $params_group ) ) {

				continue;

			}

			if( !array_key_exists($params_key, $query_params) ) {

				$query_params[$params_key] = $params_group;

			} else {

				$query_params[$params_key][] = $params_group;

			}

		}

	}

	return $query_params;
}, 10, 2);



// Filter to modify the post entry for rg_exhibition posts
add_filter('plura_wp_post', function (array $entry, WP_Post $post, ?string $context = null): array {

	if (get_post_type($post) === 'rg_exhibition') {

		$a = [];

		foreach(['featured-image', 'title'] as $key) {

			if( array_key_exists($key, $entry) ) {

				$a[$key] = $entry[$key];

			}

		}

		return $a;
	}

	return $entry;
}, 10, 3);




//https://galeriareverso.com/v2/rg_exhibition/fabian-kalman/
//get all artists related to an exhibition
function rg2_exhibition_artists( int $exhibitionID ) {

	$id = plura_wpml_id( $exhibitionID );

	if( have_rows('rg_exhibition_artists', $id) ) {

		$html = [];

		while ( have_rows('rg_exhibition_artists', $id) ): the_row();

			$post = get_sub_field('rg_exhibition_artist');

			$html[] = plura_wp_link(
				html: $post->post_title,
				target: $post,
				atts: ['class' => 'rg-exhibition-artist']
			);

		endwhile;

		$atts = [
			'class' => 'rg-exhibition-artists',
			'data-label' => __('Artists', 'rg'),
		];

		return sprintf('<div %s>%s</div>', plura_attributes( $atts ), implode('', $html) );

	}

}

add_shortcode('rg2-exhibition-artists', function( $args ) {

	$atts = shortcode_atts([
		'id' => get_the_ID(),
	], $args);

	if ( is_numeric($atts['id']) ) {
		return rg2_exhibition_artists( (int) $atts['id'] );
	}

	return '';

});





//get all date / hours info related to an exhibition
function rg_exhibition_datetime( int $id, bool $date = true, bool $opening = true ): ?string {

	$id = plura_wpml_id( $id );


	$data = [];
	
	foreach( ['date_start', 'date_end', 'opening_date', 'opening_time_start', 'opening_time_end'] as $field ) {

		if( $value = get_field( 'rg_exhibition_' . $field, $id ) ) {

			$data[$field] = $value;

		}		

	}


	$html = [];

	$classes = ['rg-datetime-item'];


	if( $date && isset( $data['date_start'] ) ) {

		$atts = ['class' => ['start-end', ...$classes] ];

		$start = wp_date( __('j F Y', 'rg'), DateTime::createFromFormat('d/m/Y', $data['date_start'])->getTimestamp() );

		if( isset( $data['date_end'] ) ) {

			$end = wp_date( __('j F Y', 'rg'), DateTime::createFromFormat('d/m/Y', $data['date_end'])->getTimestamp() );

			$html[] = sprintf(
				'<div %s> <div %s>%s</div> <div %s>%s</div> </div>',
				plura_attributes( $atts ),
				plura_attributes( ['class' => 'start'] ),
				$start,
				plura_attributes( ['class' => 'end'] ),
				$end
			);

		} else {
			
			$html[] = sprintf(
				'<div %s> <div %s>%s</div> </div>',
				plura_attributes( $atts ),
				plura_attributes( ['class' => 'start'] ),
				$start
			);

		}


	}


	if( $opening && isset( $data['opening_date'] ) ) {

		$date = wp_date( __('l, F j, Y', 'rg'), DateTime::createFromFormat('d/m/Y', $data['opening_date'])->getTimestamp() );

		$html_opening_time = "";

		if( isset( $data['opening_time_start'], $data['opening_time_end'] ) ) {

			$classes[] = 'has-time';

			$html_opening_time = sprintf(
				'<div %s> <div %s>%s</div> <div %s>%s</div> </div>',

				plura_attributes(['class' => 'start-end']),
				
				plura_attributes(['class' => 'start']),
				$data['opening_time_start'],
				
				plura_attributes(['class' => 'end']),
				$data['opening_time_end']
			);

		}

		$atts = ['class' => ['opening', ...$classes], 'data-label' => __('Opening', 'rg') ];

		$html[] = sprintf(
			'<div %s> <div %s>%s</div> %s </div>',
			plura_attributes( $atts ),
			plura_attributes( ['class' => 'date'] ),
			$date,
			$html_opening_time
		);

	}

	if( !empty( $html ) ) {

		$atts = ['class' => 'rg-datetime'];

		return sprintf(
			'<div %s>%s</div>',
			plura_attributes( $atts ),
			implode('', $html)
		);

	}

	return '';

}

add_shortcode('rg2-exhibition-datetime', function( $args ) {

	$atts = shortcode_atts( [
		'date' => true,
		'id' => get_the_ID(),
		'opening' => true
	], $args );

	$id = is_numeric($atts['id']) ? (int) $atts['id'] : 0;

	if( $id ) {

		$date = filter_var($atts['date'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

		$opening = filter_var($atts['opening'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

		return rg_exhibition_datetime(id: $id, date: $date, opening: $opening );

	}

	return "";

});

