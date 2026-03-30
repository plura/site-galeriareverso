<?php

/*
 * Plugin Name: Galeria Reverso
 * Description: Site specific code changes for site galeriareverso.com
 * Domain Path: /languages
 * Text Domain: galeriareverso
 */
//http://www.sitepoint.com/including-javascript-in-plugins-or-themes/


//https://torquemag.io/2014/11/preparing-wordpress-site-power-single-page-web-app/

function rg_load_textdomain() {

    load_plugin_textdomain( 'rg', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' ); 

}

add_action( 'init', 'rg_load_textdomain' );



$MODULES = [
	'p/p',
    'p/modules/p-revslider',
    'includes/common',
	'includes/artists',
	'includes/exhibitions',
	'includes/objects',
	'includes/publications'
];


foreach ($MODULES as $module) {

    $path = dirname( __FILE__ ) . "/" . $module . ".php";

    if( file_exists( $path ) ) {

        include_once( $path );

    }

}


function rg_styles_and_scripts() {

   // wp_enqueue_script('p',  plugins_url( "/p/js/p.js", __FILE__ ) );

    plura_wp_enqueue(scripts: [
        __DIR__ . '/includes/css/styles.css' => ['handle' => 'rg-core'],
        __DIR__ . '/includes/js/scripts.js' => ['handle' => 'rg-core']
    ], cache: false);


  /*   wp_enqueue_style( 'rg-core', plugins_url( "/includes/css/styles.css", __FILE__ ) );

    wp_enqueue_script('rg-core',  plugins_url( "/includes/js/scripts.js", __FILE__ ) ); */

/*     $data = [
        'pluginURL' => plugin_dir_url( __FILE__ ),
        'restURL' => rest_url(),
        'restNonce' => wp_create_nonce('wp_rest')
    ]; */

    if( isset( $REWRITE_RULES ) ) {

        $data['rewrite_rules'] = $REWRITE_RULES;

    }

    wp_localize_script('rg-core', 'rg', $data);

}

add_action( 'wp_enqueue_scripts', 'rg_styles_and_scripts' );







//add body class
add_filter( 'body_class', function( $classes ) {

    if( class_exists('sitepress') ) {

        global $sitepress;

        $c = [];

        if( is_singular() ) {

            $wpmlID = apply_filters('wpml_object_id', get_the_ID(), get_queried_object()->post_type, true, $sitepress->get_default_language() );

            $c[] = 'wpmlobj-id-' . $wpmlID;

            $c[] = 'wpml-lang-' . strtolower( $sitepress->get_current_language() );

        }

        $classes = array_merge($classes, $c);

    }

    return $classes;

});