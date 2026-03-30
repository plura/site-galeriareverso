<?php

// ===========================================================
// 🔁 Flush rewrite rules once after changes to custom rewrites
// ===========================================================
// This forces WordPress to refresh its internal rewrite rules,
// which is necessary after adding new `add_rewrite_rule()` entries.
//
// Use this ONLY once after making changes to custom permalinks
// or rewrite logic, then comment it out or remove it entirely.
//
// ❗ Leaving this active on every page load is a performance risk.
//
/* flush_rewrite_rules(); // ← Comment or remove after first load

add_action('init', function() {

	flush_rewrite_rules();

});
 */

function rg_get_rewrite_rules() {

	return 	[
		[
			'page' => [
				'en' => 'past-exhibitions',
				//'pt-pt' => 'exposicoes'
				'pt-pt' => 'historico'
			],
			'regex' => "/%s\/([0-9]+)\/?/",
			'var' => 'rg_exhibition_year'
		]
	];

}



//https://halfelf.org/2017/when-a-page-is-an-endpoint/
//https://second-cup-of-coffee.com/creating-a-rewrite-rule-in-wordpress/
add_action( 'init', 'rg_rewrite_rules');

function rg_rewrite_rules() {

	foreach ( rg_get_rewrite_rules() as $rule ) {

		// Ensure it's an array and has required keys
		if ( !is_array( $rule ) || !isset( $rule['page'], $rule['var'] ) ) {
			continue;
		}

		$pages = is_array( $rule['page'] ) ? $rule['page'] : [ $rule['page'] ];

		foreach ( $pages as $page ) {

			add_rewrite_rule(
				"^" . $page . "/([0-9]+)/?",
				'index.php?pagename=' . $page . '&' . $rule['var'] . '=$matches[1]',
				'top'
			);

		}
	}
}


function rg_query_vars( $query_vars ) {

    foreach( rg_get_rewrite_rules() as $rule ) {

        $query_vars[] = $rule['var'];

    }

    return $query_vars;

}

add_filter( 'query_vars', 'rg_query_vars' );



// Filter to handle rewrite rules in WPML Switcher
// https://barebones.dev/articles/wpml-hide-or-rewrite-language-urls-in-language-switcher-and-hreflang-tags/
add_filter('wpml_head_langs', 'rg_wpml_lang_switcher_rewrite_fix');

add_filter('icl_ls_languages', 'rg_wpml_lang_switcher_rewrite_fix'); 

     
function rg_wpml_lang_switcher_rewrite_fix( $languages ) {

	global $wp;

	if( !empty( rg_get_rewrite_rules() ) ) {

		foreach( rg_get_rewrite_rules() as $rule ) {

			$pages = array_map( fn($value) => $value, $rule['page'], array_keys( $rule['page'] ) );

			$regex = sprintf( $rule['regex'], "(" . implode("|", $pages) . ")" );

			foreach( $rule['page'] as $lang => $page ) {

				$matches = [];

				if( isset( $languages[ $lang ] ) && preg_match( $regex, home_url( $wp->request ), $matches ) ) {

					$languages[ $lang ]['url'] = preg_replace('/\/(\?[^\/]+)?$/', "/$matches[2]/$1", $languages[ $lang ]['url'] );
					
				}		

			}

		}

	}

	return $languages;

}
