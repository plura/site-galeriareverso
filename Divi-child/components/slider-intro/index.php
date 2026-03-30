<?php

define('RG_HOME_SLIDE_CONTEXT', 'rg-theme-slider-intro');


add_shortcode('rg-theme-slider-intro', function( $args ) {

	$atts = shortcode_atts(['thumbs' => false], $args);

	$atts['thumbs'] = filter_var($atts['thumbs'], FILTER_VALIDATE_BOOLEAN);

	$context = RG_HOME_SLIDE_CONTEXT;

	if( $atts['thumbs'] ) {

		$context .= '-thumbs';

	}

	$ids = [RG_PAGE_SHOP_ID];

	return implode('', [

		//individual pages: shop
		plura_wp_posts(type: 'page', ids: $ids, context: $context, wrap: 0, link: -1),

		//exhibitions
		plura_wp_posts(type: 'rg_exhibition', terms: 14, taxonomy: "rg_exhibitions_category", context: $context, wrap: 0, link: -1),

	]);

} );


add_filter('plura_wp_post', function (array $entry, WP_Post $post, ?string $context = null, array $original): array {

	if ( in_array( $context, [RG_HOME_SLIDE_CONTEXT, RG_HOME_SLIDE_CONTEXT . '-thumbs'] ) && in_array( get_post_type($post), ['rg_exhibition', 'page'] ) ) {

		$a = [];

		if( $context === RG_HOME_SLIDE_CONTEXT) {

			foreach(['featured-image', 'title'] as $key) {

				if( array_key_exists($key, $original) ) {

					if( $key === 'title' ) {

						$a[$key] = plura_wp_link(html: $original[$key], target: $post);

					} else {

						$a[$key] = $original[$key];

					}				

				}

			}

			if( get_post_type( $post ) === "rg_exhibition" ) {

				$a[] = rg_exhibition_datetime(id: $post->ID, opening: false);

			}

		} else {

			foreach(['featured-image'] as $key) {

				if( array_key_exists($key, $original) ) {

					$a[$key] = $original[$key];

				}

			}			

		}

		return $a;
	}

	return $entry;
}, 10, 4);


add_filter('plura_wp_post_featured_image', function( ?string $result, WP_Post $post, string $size, array $atts, ?string $context = null ) {  

    if( in_array( $context, [RG_HOME_SLIDE_CONTEXT, RG_HOME_SLIDE_CONTEXT . '-thumbs'] ) ) {

		$size = $context === RG_HOME_SLIDE_CONTEXT ? 'full' : 'medium';

		if( !$result  ) {

        	return plura_wp_image(attachment: RG_THEME_IMAGE_DEFAULT_ID, size: $size, atts: $atts);

		} else if(  $context ===  RG_HOME_SLIDE_CONTEXT . '-thumbs' ) {

			return plura_wp_post_featured_image(post: $post, size: $size);

		}

    }

    return $result;

}, 10, 5);


add_filter('plura_wp_post_atts', function(array $atts, WP_Post $post, ?string $context ) {

	if( in_array( $context, [RG_HOME_SLIDE_CONTEXT, RG_HOME_SLIDE_CONTEXT . '-thumbs'] ) ) {

		if( $post->ID === RG_PAGE_SHOP_ID ) {

			$atts['data-slider-type'] = 'shop';

		} else {

			$atts['data-slider-type'] = 'exhibition';

		}

	}

	return $atts;

}, 10, 3);

/* 
$atts = apply_filters('plura_wp_post_atts', $atts, $post, $context); */



/*
		$context === 'rg-theme-slider-intro-thumbs' ? "medium" : "full";



		if( !$result ) {

			return plura_wp_image(attachment: RG_THEME_IMAGE_DEFAULT_ID, size: $size, atts: $atts);

		}
*/