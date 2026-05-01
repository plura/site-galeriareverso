<?php

define("RG_PAGE_ARTISTS_ID", 7384);
define("RG_PAGE_EXHIBITIONS_ID", 7488);
define("RG_PAGE_SHOP_ID", 7435);
define("RG_THEME_IMAGE_DEFAULT_ID", 1721);


function rg_theme_enqueue_styles() {

    $dir = get_stylesheet_directory();

    $scripts = [
        $dir . '/includes/css/base.css'       => ['handle' => 'rg-theme-base'],
        $dir . '/includes/css/layout.css'     => ['handle' => 'rg-theme-layout'],
        $dir . '/includes/css/posts.css'      => ['handle' => 'rg-theme-posts'],
        $dir . '/includes/css/divi.css'       => ['handle' => 'rg-theme-divi'],
        $dir . '/includes/css/deprecated.css' => ['handle' => 'rg-theme-deprecated'],
        $dir . '/includes/js/scripts.js'      => ['handle' => 'rg-theme-core', 'deps' => ['jquery']],
    ];

    if( is_page( [1732, 1743] ) ) {

        $scripts = [
            ...$scripts,
            'https://unpkg.com/leaflet@1.7.1/dist/leaflet.css' => ['handle' => 'leaflet'],
            'https://unpkg.com/leaflet@1.7.1/dist/leaflet.js'  => ['handle' => 'leaflet'],
            $dir . '/includes/js/leaflet-providers.js'         => ['handle' => 'leaflet-providers', 'deps' => ['leaflet']],
        ];

    }

    plura_wp_enqueue(scripts: $scripts, admin: false);

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

}, 10, 3);



add_filter('plura_wp_post_featured_image', function( ?string $result, WP_Post $post, string $size, array $atts, ?string $context = null ) {  

    if( !$result && $context === 'plura-wp-component-banner' ) {

        return plura_wp_image(attachment: RG_THEME_IMAGE_DEFAULT_ID, size: 'full', atts: $atts);

    }

    return $result;

}, 10, 5);




