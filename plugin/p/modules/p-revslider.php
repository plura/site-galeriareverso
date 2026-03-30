<?php


/**
 * in order for this function to work with revslider it's necessary to hack the get_stream_background_image
 * method in RevSliderOutput class, located in 'revslider/includes/output.class.php'.
 * A simple "if( function_exists('cib_revslider_featured_img') ) return cib_revslider_featured_img()"" should suffice;
 *
 * For categories, the ACF plugin and an id value returned by a 'featured_image' image field are required by default.
 * 
 * @param  string $img 	revolution image src (if it exists) 
 * @return array|null
 */
function p_revslider_bg_img( $imgData, $sliderID = false ) {

	$fn = str_replace('-', '_', strtolower( get_stylesheet() ) ) . '_revslider_bg_img';

	$obj = get_queried_object();

	//check if theme function exists
	if( function_exists( $fn ) ) {

		$data = $fn($imgData, intval( $sliderID ) );

		//if image data is returned
		if( $data && is_array( $data ) ) {

			return $data;

		//othewise it uses the functions default formula for pages/posts/categories
		//if a data "false" value is returned, no modification to revslider bg formula should be made
		} else if( $data && !empty( $imgData ) && is_array( $imgData ) ) {

			if( is_int( $data ) ) {

				$id = $data;

			} else if( is_singular() && has_post_thumbnail() ) {

				$id = get_post_thumbnail_id();

			//categories should use ACF in order to retrieve a 'featured_image' field value
			} else if( class_exists('ACF') && isset( $obj->term_id ) ) {

				$id = get_field('featured_image', $obj );
			
			}

			if( $id ) {

				$src = wp_get_attachment_image_src( $id, 'full' );

				if( $src ) {

					preg_match('/\/([^\/]+)\.[0-9a-z]+$/', $src[0], $matches);

					$imgData = array_merge( $imgData, [
						'src' => $src[0],
						'width' => $src[1],
						'height' => $src[2],
						'title' => $matches[1],
						'data-lazyload' => $src[0]
					]);

				}

			}

		}

	}

	return $imgData;

}
