<?php

define("RG_PAGE_ARTISTS_ID", 7384);
define("RG_PAGE_EXHIBITIONS_ID", 7488);
define("RG_PAGE_SHOP_ID", 7435);
define("RG_THEME_IMAGE_DEFAULT_ID", 1721);


function rg_theme_enqueue_styles() { 

    /* wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );

    wp_enqueue_style( 'rg-theme-globals', get_stylesheet_directory_uri() . '/includes/css/globals.css' ); */

    wp_enqueue_style( 'rg-theme-fix', get_stylesheet_directory_uri() . '/includes/css/fix.css' );

    wp_enqueue_script('rg-theme-core', get_stylesheet_directory_uri() . '/includes/js/scripts.js', ['jquery'] );

    if( is_page( [1732, 1743] ) ) {  

		wp_enqueue_style( 'leaflet', 'https://unpkg.com/leaflet@1.7.1/dist/leaflet.css');    

		wp_enqueue_script('leaflet', 'https://unpkg.com/leaflet@1.7.1/dist/leaflet.js');

		wp_enqueue_script('leaflet-providers', get_stylesheet_directory_uri() . '/includes/js/leaflet-providers.js', ['leaflet'] );

    }

}

add_action( 'wp_enqueue_scripts', 'rg_theme_enqueue_styles' );



add_filter('plura_wp_breadcrumbs', function( ?array $crumbs, $object, ?string $context) {

    global $wp_query;

    if( is_singular('rg_artist') ) {

        $crumbs = [ 
            [
                plura_wp_breadcrumb( RG_PAGE_ARTISTS_ID ) 
            ]
        ];

    } else if( ( is_page() && plura_wpml_id() === RG_PAGE_EXHIBITIONS_ID && $wp_query->get('rg_exhibition_year') ) || is_singular('rg_exhibition') ) {
                
         $crumbs_group = [ plura_wp_breadcrumb( RG_PAGE_EXHIBITIONS_ID ) ];

        if( is_singular('rg_exhibition') ) {

        	$date = DateTime::createFromFormat('d/m/Y', get_field('rg_exhibition_date_start', plura_wpml_id() ) );

        	$link = preg_replace('/(.+)\/(\?[^\/]+)?$/', '${1}/' . $date->format('Y') . '/${2}', get_permalink( plura_wpml_id( RG_PAGE_EXHIBITIONS_ID, false ) ) );

        	$crumbs_group[] = [
        		'obj' => $link,
        		'name' => $date->format('Y')
        	];

        }
        
        $crumbs = [ $crumbs_group ];

    }

    return $crumbs;

}, 10, 3 );



add_filter('plura_wp_title', function( string $text, WP_Term|WP_Post|int $object, ?string $context ) {

    global $wp_query;

    if( $context === 'plura-wp-component-banner' && is_page() && plura_wpml_id() === RG_PAGE_EXHIBITIONS_ID && $wp_query->get('rg_exhibition_year') ) {

        return $wp_query->get('rg_exhibition_year');

    }

    return $text;

}, 1, 10);



add_filter('plura_wp_post_featured_image', function( ?string $result, WP_Post $post, string $size, array $atts, ?string $context = null ) {  

    if( !$result && $context === 'plura-wp-component-banner' ) {

        return plura_wp_image(attachment: RG_THEME_IMAGE_DEFAULT_ID, size: 'full', atts: $atts);

    }

    return $result;

}, 10, 5);



add_filter('rg-theme-component-intro', function( $atts ) {

    //shop
    $shop = plura_wp_posts(type: 'page', ids: 7435, context: 'rg-theme-component-intro', wrap: false);

    //featured exhibitions
    $exhibitions = plura_wp_posts(type: 'rg_exhibitions', terms: 14, taxonomy: 'rg_exhibitions_category', context: 'rg-theme-component-intro', wrap: false);
   

});



