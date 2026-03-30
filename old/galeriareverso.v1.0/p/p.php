<?php

function p_attributes( $atts, $prefix = false ) {

	$a = [];

	foreach($atts as $k => $v) {

		$value = $k . "=\"" . $v . "\"";

		if( $prefix ) {

			$value = "data-" . $value;

		}

		$a[] = $value;

	}

	return implode(' ', $a);

}


function p_thumbnail( $postID, $size = 'large' ) {

	$img = has_post_thumbnail( $postID );

	if( $img ) {

		return wp_get_attachment_image_src( get_post_thumbnail_id( $postID ), $size);

	}

	return false;

}


function p_breadcrumbs() {

	$crumbs = [];

	if( is_archive() ) {

		$crumbs = p_breadcrumbs_terms( get_queried_object()->term_id, get_queried_object()->taxonomy );

	} else if( is_single() ) {

		$post_taxonomies = get_object_taxonomies( get_post() );

		if( !empty( $post_taxonomies ) ) {

			$terms = get_the_terms( get_the_ID(), $post_taxonomies[0] );

			if( !empty( $terms ) ) {

				$crumbs = p_breadcrumbs_terms( $terms[0]->term_id, $terms[0]->taxonomy, true );

			}

		}

	} else if( is_page() ) {

		$ancestors = get_ancestors( get_the_ID() );

		if( !empty( $ancestors ) ) {

			foreach( $ancestors as $ancestor ) {

				$crumbs[] = p_breadcrumb( $ancestor );

			}

		}
	
	}


	$fn = str_replace('-', '_', strtolower( get_stylesheet() ) ) . '_breadcrumbs';

	if( function_exists( $fn ) ) {

		$crumbs = $fn( $crumbs );

	}

	if( !empty( $crumbs ) ) {

		$b = [];

		foreach( $crumbs as $crumb ) {

			$b[] = "<li class=\"p-breadcrumb\"><a href=\"" . $crumb['link'] . "\" title=\"" . $crumb['name'] . "\">" . $crumb['name'] . "</a></li>";

		}

		return "<ul class=\"p-breadcrumbs\">" . implode('', $b) . "</ul>";

	}

}


function p_breadcrumbs_terms( $termID, $taxonomy, $include = false ) {

	$crumbs = [];

	$ancestors = get_ancestors( $termID, $taxonomy );

	if( !empty( $ancestors ) ) {

		foreach( $ancestors as $ancestor ) {

			$crumbs[] = p_breadcrumb( $ancestor, $taxonomy );

		}

	}

	if( $include ) {

		$crumbs[] = p_breadcrumb( $termID, $taxonomy );

	}

	return $crumbs;

}


function p_breadcrumb( $id, $taxonomy = false ) {

	if( $taxonomy ) {

		return ['type' => 'term', 'link' => get_term_link( $id, $taxonomy ), 'name' => get_term( $id, $taxonomy )->name ];

	}

	return ['type' => 'single', 'link' => get_permalink( $id ), 'name' => get_the_title( $id ) ];

}


add_shortcode('p-breadcrumbs', 'p_breadcrumbs');




function p_title() {

	$fn = str_replace('-', '_', strtolower( get_stylesheet() ) ) . '_title';

	if( is_page() || is_single() ) {

		$text = get_the_title();

	} else if( is_archive() ) {

		//https://www.binarymoon.co.uk/2017/02/hide-archive-title-prefix-wordpress/
		$title_parts = explode( ': ', get_the_archive_title(), 2 );

		$text = $title_parts[1];

	}

	if( empty($text) ) {

		$text = 'no title';

	}

	if( function_exists( $fn ) ) {

		$text = $fn( $text );

	}

	return $text;

}

add_shortcode('p-title', 'p_title');




/**
 * [p_posts description]
 * @param  array  $args [description]
 *				$args['date']					bool	indicates if date is to be added
 *				$args['date_format']			string	date format
 *				$args['excerpt']				bool	indicates if excerpt should be visibile
 *				$args['featured_image']			bool	indicates if function should return featured image
 *				$args['featured_image_size']	string	the size of the featured image
*				$args['layout']					string	'div' or 'list'
 *				$args['limit']					number	indicates number of posts
 *				$args['nav']					nav		include navigation (WP-PAGENAVI plugin is required)
 *				$args['type']					string	post type
 *				$args['tax']					string	taxonomy
 *				$args['tax_id']					number	term id
 * @return [type] [description]
 */
function p_posts( array $args ) {

	$args = array_merge([
		'date' 					=> 1,
		'date_format'			=> "l, j F Y",
		'excerpt'				=> 0,
		'featured_image' 		=> 1,
		'featured_image_size'	=> 'medium',
		'layout'				=> 'div',
		'limit'					=> -1,
		'nav'					=> 1,
	    'type' 					=> 'post',
	    'tax'					=> '',
	    'tax_id'				=> ''
	], $args);

	$params = p_posts_query( $args );

	$query = new WP_Query( $params );

	$html = "";

	if( $query->have_posts() ) {

		$classes = ["p-posts"];

		if( $args['featured_image'] ) {

			$classes[] = 'has-thumbnails';

		}

		if( !$args['layout'] !== 'list' ) {

			$tag = $subtag = 'div';

		} else {

			$tag = 'ul';

			$subtag = 'li';

			$classes[] = 'list';

		}

		$atts = ['class' => implode(' ', $classes) ];


		$html .= "<div " . p_attributes( $atts ) . ">\n";

			$html .= "<$tag class=\"p-posts-holder\">";

			foreach( $query->posts as $post ) {

				$classes = ["p-post"];

				if( $args['featured_image'] && has_post_thumbnail( $post->ID ) ) {

						$classes[] = "has-thumbnail";

						$img = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), $args['featured_image_size']);

				}

				$atts = ['class' => implode(' ', $classes)];

				$html .= "<$subtag " . p_attributes( $atts ) . "\">\n";


				if( isset($img) ) {

					$html .= p_post_link($post, "<img src=\"" . $img[0] . "\" width=\"" . $img[1] . "\" height=\"" . $img[2] . "\" class=\"p-post-featured-image\" />\n", ['p-post-link', 'p-post-featured-image-link']);

				}

					$html .= "<div class=\"p-post-title\">" . p_post_link($post, $post->post_title, 'p-post-link') . "</div>";

					$html .= "<div class=\"p-post-meta\">\n";

						$html .= "<div class=\"p-post-date\">" . date_i18n( $args['date_format'], strtotime( get_the_date('Y/n/j H:i:s', $post ) ) ) . "</div>\n";

					$html .= "</div>\n";

				if( $args['excerpt'] ) {

					$html .= "<div class=\"p-post-excerpt\">" . $post->post_excerpt . "</div>\n";

				}

				$html .= "</$subtag>\n";

			}

			$html .= "</$tag>\n"; //p-posts-holder

		if( $args['nav'] && $query->found_posts > $query->post_count ) {

			$html .= "<div class=\"p-posts-nav\">" . p_posts_nav( $query ) . "</div>";

		}

		$html .= "</div>"; //p-posts

	}

	return $html;

}



function p_posts_nav($query) {

	global $wp_query;

	$tmp_query = $wp_query;

	$wp_query = $query;

	if( function_exists('wp_pagenavi') ) {

		$result = wp_pagenavi( array(
			'query' => $query,
			'echo' => false
		));

	} else {

		$result = paginate_links( array(
		           	
			'format'  	=> 'page/%#%',
			'current' 	=> get_query_var( 'paged' ),
			'total'   	=> $query->max_num_pages,
			'mid_size'	=> 2,
			'prev_text'	=> __('&laquo;'),
			'next_text'	=> __('&raquo;')		
			//'prev_text'	=> __('&laquo; Prev Page'),
			//'next_text'	=> __('Next Page &raquo;')
		       		
		) );

	}

	wp_reset_postdata();

	// Restore original query object
	$wp_query = null;
	$wp_query = $tmp_query;

	return $result;

}


function p_post_link($post, $html, $classes = false) {

	$atts = ['href' => get_permalink( $post->ID ), 'title' => $post->post_title];

	if( $classes ) {

		$atts['class'] = is_array( $classes ) ? implode(' ', $classes) : $classes;

	}

	return "<a " . p_attributes( $atts ) . ">\n" . $html . "</a>";

} 



function p_posts_query( $args ) {

	$params = array(
		'post_type' => $args['type']
	);

	if( !empty( $args['tax'] ) ) {

		if( is_numeric( $args['tax'] ) ) {

			$params['cat'] = $args['tax'];

		} else {

			$params['tax_query'] = [
				[
					'taxonomy' => $args['tax'],
					'field' => 'term_id',
					'terms' => $args['tax_id']
				]
			];

		}

	}

	if( !empty( $args['limit'] ) ) {

		$params['posts_per_page'] = $args['limit'];

	}

	if( get_query_var( 'paged' ) ) {

		$params['paged'] = get_query_var( 'paged' );

	}

	return $params;


}



function p_posts_shortcode( $atts ) {

	$args = shortcode_atts( array(
		'date' 					=> '1',
		'date_format'			=> "l, j F Y",
		'excerpt'				=> '',
		'featured_image' 		=> '1',
		'featured_image_size'	=> 'medium',
		'layout'				=> 'div',
		'limit'					=> '',
		'nav'					=> '1',
	    'type' 					=> 'post',
	    'tax'					=> '',
	    'tax_id'				=> ''
	 ), $atts );

	return p_posts( $args );

}

add_shortcode('p-posts', 'p_posts_shortcode');




function p_date_archive() {

	if( is_archive() ) {

		if( is_post_type_archive() ) {

			$post_type = get_queried_object()->name;

			$atts = [
				'data-archive-request-obj' => 'is-archive',
				'data-archive-post-type' => $post_type
			];

		} else if( isset( get_queried_object()->term_id ) ) {

			$post_type = get_taxonomy( get_queried_object()->taxonomy )->object_type[0];

			$term_id = get_queried_object()->term_id;
			
			$atts = [
				'data-archive-request-obj' => 'term',
				'data-archive-post-type' => $post_type
			];

		}		

	} else if( is_singular() ) {

		$post_type = get_post_type();

		$atts = [
			'data-archive-request-obj' => 'single',
			'data-archive-post-type' => $post_type
		];

	}

	if( !empty( $post_type ) ) {


		$atts['class'] = 'p-date-archive';

		return "<ul " . p_attributes( $atts ) . ">" . wp_get_archives( array('echo' => 0, 'type' => 'yearly', 'post_type' => $post_type ) ) . "</ul>";

	}

}

add_shortcode('p-date-archive', 'p_date_archive');
