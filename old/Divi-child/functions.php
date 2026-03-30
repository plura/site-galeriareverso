<?php

define("RG_EXHIBITIONS_ARCHIVE_ID", 1723);
define("RG_ARTISTS_ARCHIVE_ID", 1727);


function rg_theme_enqueue_styles() { 

    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );

    wp_enqueue_style( 'rg-theme-globals', get_stylesheet_directory_uri() . '/includes/css/globals.css' );

    wp_enqueue_script('rg-theme-core', get_stylesheet_directory_uri() . '/includes/js/scripts.js', ['jquery'] );

    if( is_page( [1732, 1743] ) ) {  

		wp_enqueue_style( 'leaflet', 'https://unpkg.com/leaflet@1.7.1/dist/leaflet.css');    

		wp_enqueue_script('leaflet', 'https://unpkg.com/leaflet@1.7.1/dist/leaflet.js');

		wp_enqueue_script('leaflet-providers', get_stylesheet_directory_uri() . '/includes/js/leaflet-providers.js', ['leaflet'] );

    }

    wp_localize_script('rg-theme-core', 'rg_theme', array(
        'pluginURL' => plugin_dir_url( __FILE__ ),
        'restURL' => rest_url(),
        'restNonce' => wp_create_nonce('wp_rest')
    ));

	/*wp_dequeue_style( 'divi-style' );

    wp_enqueue_style( 'divi-style', get_stylesheet_directory_uri() . '/style.css', [], time() );  */

}

add_action( 'wp_enqueue_scripts', 'rg_theme_enqueue_styles' );




//add wpml body class  
add_filter('body_class', function( $classes ) {

    global $sitepress;

    $c = [];

    if( class_exists('sitepress') && is_singular() ) {

        $wpmlID = apply_filters( 'wpml_object_id', get_the_ID(), 'alualpha_product', true, $sitepress->get_default_language() );

        $c[] = 'wpmlobj-id-' . rg_wpml_id();

        $c[] = 'wpml-lang-' . strtolower( $sitepress->get_current_language() );

    }

    return array_merge($classes, $c);

} );





//revolution slider bg
function divi_child_revslider_bg_img( $imgData, $sliderID = false ) {

    if( is_singular('rg_artist') ) {

        $imgID = rg_artist_featured_object_image_id( get_the_ID() );

        if( $imgID ) {

            return $imgID;

        }

    }

    return true;

}





function divi_child_title( $text ) {

    global $wp_query;

    if( is_page() && rg_wpml_id() === RG_EXHIBITIONS_ARCHIVE_ID && $wp_query->get('rg_exhibition_year') ) {

        return $wp_query->get('rg_exhibition_year');

    }

    return $text;

}


function rg_banner_subtitle() {

	global $wp_query, $sitepress;

    $breadcrumbs = [];

    if( is_singular('rg_artist') ) {

        $breadcrumbs[] = get_post( rg_wpml_id( RG_ARTISTS_ARCHIVE_ID, false, 'page') );

    } else if( ( is_page() && rg_wpml_id() === RG_EXHIBITIONS_ARCHIVE_ID && $wp_query->get('rg_exhibition_year') ) || is_singular('rg_exhibition') ) {

 		$breadcrumbs[] = get_post( rg_wpml_id( RG_EXHIBITIONS_ARCHIVE_ID, false, 'page') );

        if( is_singular('rg_exhibition') ) {

        	$date = DateTime::createFromFormat('d/m/Y', get_field('rg_exhibition_date_start', rg_wpml_id() ) );

        	$link = preg_replace('/(.+)\/(\?[^\/]+)?$/', '${1}/' . $date->format('Y') . '/${2}', get_permalink( rg_wpml_id( RG_EXHIBITIONS_ARCHIVE_ID, false, 'page') ) );

        	$breadcrumbs[] = [
        		'link' => $link,
        		'title' => $date->format('Y')
        	];

        }

    }

    if( !empty( $breadcrumbs ) ) {

        $html = [];

        foreach( $breadcrumbs as $post ) {

        	$atts = ['class' => 'rg-banner-subtitle-path'];

        	if( is_a( $post, 'WP_Post') ) {

        		$atts = array_merge( $atts, [
					'href' => get_permalink( $post ),
					'title' => $post->post_title
        		]);

        		$title = $post->post_title;

        	} else {

        		$atts = array_merge( $atts, [
        			'href' => $post['link'],
        			'title' => $post['title']
        		]);

        		$title = $post['title'];

        	}

			$html[] = "<a " . p_attributes( $atts ) . ">" . $title . "</a>";

        }

        $atts = ['class' => 'rg-banner-subtitle'];

        return "<div " . p_attributes( $atts ) . ">" . implode('', $html) . "</div>";

    }

}

add_shortcode('rg-banner-subtitle', 'rg_banner_subtitle');







