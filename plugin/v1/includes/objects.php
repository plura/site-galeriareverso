<?php

//get objects grid
function rb_objects( $args ) {

	$query = new WP_Query( rg_objects_query_vars( $args ) );

	if( $query->have_posts() ) {

		$label = empty( $args['label'] ) ? false : $args['label'];

		if( !empty( $args['eg'] ) ) {

			return rg_essential_grid( $query->posts, $args['eg'], $label );

		}

		return rg_objects_grid( $query->posts, $label );

	}

}

//get objects query variables
function  rg_objects_query_vars( $args ) {

	$query_vars = [
		'post_type' => 'rg_object',
		'posts_per_page' => $args['limit']
	];

	$meta = [];

	if( $args['exclude'] ) {

		$exclude = is_array( $args['exclude'] ) ? $args['exclude'] : explode(',', $args['exclude']);

		$query_vars['post__not_in'] = $exclude;

	}

	if( !empty( $args['ids'] ) ) {

		$ids = is_array( $args['ids'] ) ? $args['ids'] : explode(',', $args['ids']);

		$query_vars = array_merge( $query_vars, [
			'orderby' => 'post__in',
			'post__in' => $ids
		]);

	}

	if( !empty( $args['artist'] ) ) {

		$id = preg_match('/([0-9]+)/', $args['artist']) ? $args['artist'] : get_the_ID();

		$meta[] = [
			'key' => 'rg_object_artist',
			'value' => rg_wpml_id( $id ),
			'compare' => '='
		];

	}

	if( !empty( $args['shop'] ) && preg_match("/(true|1)/", $args['shop']) ) {

		$meta[] = [
			'key' => 'rg_object_shop',
			'value' => 1
		];

	}

	if( !empty( $meta ) ) {

		$query_vars['meta_query'] = $meta;

	}

	return $query_vars;

}

//get objects grid
function rg_objects_grid( $posts ) {

	$a = [];

	foreach( $posts as $post ) {

		$atts = ['class' => 'rg-group-item', 'href' => get_permalink( $post->ID )];

		$img = rg_object_featured_image( $post->ID );

		if( $img ) {

			$atts['style'] = "background-image: url('" . $img[0] . "');";

		}

		$html = "<a " . plura_attributes( $atts ) . ">";

		//$html .= "<div class=\"rg-group-item-title\">" . $post->post_title . "</div>";

		$a[] = $html . "</a>";

	}

	$atts = [
		'class' => 'rg-group',
		'data-type' => 'object',
		'data-layout' => 'grid'
	];

	if( $label ) {

		$atts['data-label'] = $label;

	}

	return "<div " . plura_attributes( $atts ) . ">" . implode('', $a) . "</div>";

}

function rb_objects_shortcode( $args ) {

	$atts = shortcode_atts( [
		'artist' => '',
		'eg' => '',
		'ids' => '',
		'label' => '',
		'limit' => '-1',
		'rand' => '',
		'shop' => ''
	], $args );

	return rb_objects( $atts );

}

add_shortcode('rg-objects', 'rb_objects_shortcode');




//get object featured image
function rg_object_featured_image( $objectID, $size = 'large' ) {

	$id = rg_object_featured_image_id( $objectID );

	if( $id ) {

		foreach( ['large', 'full', 'medium', 'thumbnail'] as $imgsize ) {

			$img = wp_get_attachment_image_src($id, $imgsize);

			if( $img ) {

				return $img;

			}

		}

	}

	return false;

}

//get object featured image id
function rg_object_featured_image_id( $objectID ) {

	if( has_post_thumbnail( $objectID ) ) {

		return get_post_thumbnail_id( $objectID );

	} else {

		$gallery = get_field('rg_object_images', $objectID);

		if( $gallery ) {

			return $gallery[0]['ID'];

		}

	}

	return false;

}



//object image (alias of featured)
function rg_object_image( $args ) {

	$img = rg_object_featured_image( $args['id'] );

	$atts = ['class' => 'rg-object-image', 'src' => $img[0], 'width' => $img[1], 'height' => $img[2]];

	return "<img " . plura_attributes( $atts ) . "/>";

}

function rg_object_image_shortcode( $args ) {

	$atts = shortcode_atts( [
		'id' => '',
	], $args );

	if( !empty( $atts['id'] ) || is_singular('rg_object') ) {

		if( empty( $atts['id'] ) ) {

			$atts['id'] = get_the_ID();

		}

		return rg_object_image( $atts );

	}

}

add_shortcode('rg-object-image', 'rg_object_image_shortcode');





//object gallery
function rg_object_gallery( $objectID ) {

	$gallery = get_field('rg_object_images', $objectID);

	if( $gallery ) {

		$a = [];

		foreach( $gallery as $imgData ) {

			$img = rg_object_gallery_image( $imgData );

			$atts = ['src' => $img[0] ];

			$a[] = "<img " . plura_attributes( $atts ) . "/>";

		}

		return implode('', $a);

	}

}

function rg_object_gallery_shortcode() {

	return rg_object_gallery( get_the_ID() );

}

add_shortcode('rg-object-gallery', 'rg_object_gallery_shortcode');




//get artist's featured object image id
function rg_artist_featured_object_image_id( $artistID ) {

	$query = new WP_Query( rg_objects_query_vars( ['artist' => $artistID] ) );

	if( $query->have_posts() ) {

		//loop objects
		foreach( $query->posts as $post ) {

			$imgID = rg_object_featured_image_id( $post->ID );

			if( $imgID ) {

				return $imgID;

			}

		}

	}

	return false;

}





function rg_object_gallery_image( $imgData ) {

	$sizes = ['large', 'full', 'medium', 'thumbnail'];

	foreach( $sizes as $size ) {

		if( $size === 'full' ) {

			return [ $imgData['url'], $imgData['width'], $imgData['height'] ];

		} else if( !empty( $imgData['sizes'][ $size ] ) ) {

			return [ $imgData['sizes'][ $size ], $imgData['sizes'][ "$size-width" ], $imgData['sizes'][ "$size-height" ] ];

		}	

	}

	return false;

}





//related objects of the same artist
function rg_objects_related_shortcode( $args ) { 

	$atts = shortcode_atts([
		'artist' => get_field('rg_object_artist', get_the_ID())->ID,
		'eg' => '',
		'exclude' => get_the_ID(),
		'label' => __('Other Objects by the Artist', 'rg')
	], $args );

	if( !empty( $atts['artist'] ) ) {

		return rb_objects( $atts );

	}

}

add_shortcode('rg-objects-related', 'rg_objects_related_shortcode');





function rg_gallery_shortcode( $args ) {

	$atts = shortcode_atts( [
		'artist' => '',
		'eg' => 'grid-artist-objects',
		'ids' => '',
		'label' => '',
		'limit' => '-1',
		'rand' => '',
		'shop' => ''
	], $args );

	return rb_objects( $atts );

}

add_shortcode('rg-gallery', 'rg_gallery_shortcode');





//change permalink structure - /objects/postID
//https://wordpress.stackexchange.com/a/158224
add_filter('post_type_link', 'rg_object_change_link', 1, 3);

function rg_object_change_link( $link, $post = 0 ){
    
    if ( $post->post_type == 'rg_object' ) {

        return home_url( 'objects/' . $post->ID );
    
    }

	return $link; 
 
}

add_action( 'init', 'rg_object_change_rewrites_init' );

function rg_object_change_rewrites_init(){

	add_rewrite_rule(
		'objects/([0-9]+)?$',
		'index.php?post_type=rg_object&p=$matches[1]',
		'top');

}

//change object title
//https://www.cyberciti.biz/programming/how-to-customize-title-in-wordpress-themes-using-pre_get_document_title/
add_filter('pre_get_document_title', 'rg_object_page_title', 999, 1);

function rg_object_page_title( $title ) {

     if ( is_singular('rg_object') ) {

		return __('Object', 'rg') . ' ' . get_the_ID() . ' - ' . get_bloginfo('name');
     
     }

     return $title;

}