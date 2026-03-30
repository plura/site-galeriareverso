<?php

/*
 * Plugin Name: Galeria Reverso
 * Description: Site specific code changes for site galeriareverso.com
 * Domain Path: /languages
 * Text Domain: rg
 */
//http://www.sitepoint.com/including-javascript-in-plugins-or-themes/


//https://torquemag.io/2014/11/preparing-wordpress-site-power-single-page-web-app/

add_action('plugins_loaded', function () {
	if (!function_exists('plura_includes')) {
		add_action('admin_notices', function () {
			echo '<div class="notice notice-error"><p><strong>My Plugin:</strong> The <code>Plura</code> plugin must be active for this plugin to work properly.</p></div>';
		});
		return;
	}

	plura_includes([
		'includes/common',
		'includes/artists',
		'includes/exhibitions',
		'includes/objects',
		'includes/publications',
		'includes/rules',

		//'p/p',
		//'p/p-revslider'
		/* 'v1/includes/common', */
		//'v1/includes/artists',
		//'v1/includes/exhibitions',
		/* 'v1/includes/objects',
		'v1/includes/publications' *//* ,
        'v1/includes/test' */
	], __DIR__);
});





function rg_load_textdomain()
{

	load_plugin_textdomain('rg', false, dirname(plugin_basename(__FILE__)) . '/languages');
}

add_action('init', 'rg_load_textdomain');



/* $MODULES = [
	'p/p',
    'p/modules/p-revslider',

];


foreach ($MODULES as $module) {

    $path = dirname( __FILE__ ) . "/" . $module . ".php";

    if( file_exists( $path ) ) {

        include_once( $path );

    }

} */


function rg_styles_and_scripts()
{

	// wp_enqueue_script('p',  plugins_url( "/p/js/p.js", __FILE__ ) );

	$scripts = [
		/* __DIR__ . '/assets/css/styles.css' => ['handle' => 'rg-core'], */
		__DIR__ . '/assets/css/globals.css' => ['handle' => 'rg-globals'],
		__DIR__ . '/assets/css/globals-theme.css' => ['handle' => 'rg-globals-theme'],
		__DIR__ . '/assets/js/lightbox.js' => ['handle' => 'rg-lightbox'],
		__DIR__ . '/assets/js/scripts.js' => ['handle' => 'rg-core']
	];

	$scripts = [
		...[
			'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css' => ['handle' => 'swiper'],
			'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js' => ['handle' => 'swiper']
		],
		...$scripts
	];

	//if (is_singular('rg_object')) {


	$scripts = [
		...[
			'https://cdn.jsdelivr.net/npm/@fancyapps/ui@6.0/dist/fancybox/fancybox.umd.js' => ['handle' => 'fancyapps-fancybox'],
			'https://cdn.jsdelivr.net/npm/@fancyapps/ui@6.0/dist/fancybox/fancybox.css' => ['handle' => 'fancyapps-fancybox'],
			'https://cdn.jsdelivr.net/npm/@fancyapps/ui@6.0/dist/panzoom/panzoom.css' => ['handle' => 'fancyapps-panzoom'],
			'https://cdn.jsdelivr.net/npm/@fancyapps/ui@6.0/dist/panzoom/panzoom.umd.js' => ['handle' => 'fancyapps-panzoom'],
			'https://cdn.jsdelivr.net/npm/@fancyapps/ui@6.0/dist/carousel/carousel.umd.js' => ['handle' => 'fancyapps-carousel'],
			'https://cdn.jsdelivr.net/npm/@fancyapps/ui@6.0/dist/carousel/carousel.css' => ['handle' => 'fancyapps-carousel'],
			'https://cdn.jsdelivr.net/npm/@fancyapps/ui@6.0/dist/carousel/carousel.dots.umd.js' => ['handle' => 'fancyapps-carousel-dots'],
			'https://cdn.jsdelivr.net/npm/@fancyapps/ui@6.0/dist/carousel/carousel.dots.css' => ['handle' => 'fancyapps-carousel-dots'],
		],
		...$scripts
	];
	//}

	$scripts = [
		...[
			'https://cdn.jsdelivr.net/npm/masonry-layout@4/dist/masonry.pkgd.min.js' => ['handle' => 'desandro-masonry']
		],
		...$scripts
	];


	plura_wp_enqueue(scripts: $scripts, cache: false, admin: false);


	/*   wp_enqueue_style( 'rg-core', plugins_url( "/includes/css/styles.css", __FILE__ ) );

    wp_enqueue_script('rg-core',  plugins_url( "/includes/js/scripts.js", __FILE__ ) ); */

	/*     $data = [
        'pluginURL' => plugin_dir_url( __FILE__ ),
        'restURL' => rest_url(),
        'restNonce' => wp_create_nonce('wp_rest')
    ]; */

	$data = [];

	if (isset($REWRITE_RULES)) {

		$data['rewrite_rules'] = $REWRITE_RULES;
	}

	wp_localize_script('rg-core', 'rg', $data);
}

add_action('wp_enqueue_scripts', 'rg_styles_and_scripts', 100);







//add body class
add_filter('body_class', function ($classes) {

	if (class_exists('sitepress')) {

		global $sitepress;

		$c = [];

		if (is_singular()) {

			$wpmlID = apply_filters('wpml_object_id', get_the_ID(), get_queried_object()->post_type, true, $sitepress->get_default_language());

			$c[] = 'wpmlobj-id-' . $wpmlID;

			$c[] = 'wpml-lang-' . strtolower($sitepress->get_current_language());
		}

		$classes = array_merge($classes, $c);
	}

	return $classes;
});
