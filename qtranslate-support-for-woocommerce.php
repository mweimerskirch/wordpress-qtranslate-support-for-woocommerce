<?php
/*
Plugin Name: qTranslate support for WooCommerce
Plugin URI: https://github.com/mweimerskirch/wordpress-qtranslate-support-for-woocommerce
Description: Makes qTranslate work with WooCommerce
Version: 1.0.1
Author: Michel Weimerskirch
Author URI: http://michel.weimerskirch.net
License: MIT
*/

/* Translate category names*/
add_action('woocommerce_before_subcategory', 'qtrans_woocommerce_before_subcategory');
function qtrans_woocommerce_before_subcategory($category) { $category->name = __($category->name); return $category; }

/* Translate payment gateway title and description */
add_filter('woocommerce_gateway_title', 'qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage', 0);
add_filter('woocommerce_gateway_description', 'qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage', 0);

/* Fix qTranslate WooCommerce AJAX URLs */
add_filter('admin_url', 'fix_qtranslate_woocommerce_ajax_url');
function fix_qtranslate_woocommerce_ajax_url ($url) {
	if ($url == '/wp-admin/admin-ajax.php') {
		global $q_config;
		$url = $url . '?lang=' . $q_config['language'];
	}
	return $url;
}

/* Various translation filters*/
add_filter('the_title_attribute', 'qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage', 0);
add_filter('woocommerce_attribute_label', 'qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage', 0);
add_filter('woocommerce_variation_option_name', 'qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage', 0);
add_filter('woocommerce_page_title', 'qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage', 0);


/* Replace the "sanitize_title" filter from qTranslate with a custom implementation that prevents accents to be replaced language-specifically as this leads to problems with product attributes in WooCommerce. */
remove_filter('sanitize_title', 'qtrans_useRawTitle', 0, 3);
add_filter('sanitize_title', 'qwc_useRawTitle', -10, 3);
function qwc_useRawTitle($title, $raw_title = '', $context = 'save') {
	if('save' == $context) {
		if ($raw_title == '') $raw_title = $title;
		$raw_title = qtrans_useDefaultLanguage($raw_title);

		// Temporarily set a dummy language so the "remove_accents" method is not language-specific
		add_filter('locale', 'qwc_returnDummyLanguage', 100);

		$title = remove_accents($raw_title);

		// Restore the return value of the "get_locale()" method
		remove_filter('locale', 'qwc_returnDummyLanguage', 100);
	}
	return $title;
}
function qwc_returnDummyLanguage() { return 'dummy'; }

/* Fix the categories displayed on the single product pages */
add_filter('get_the_terms', 'qwc_get_the_terms');
function qwc_get_the_terms ($terms) {
	foreach($terms as $term) {
		if($term->taxonomy == 'product_cat') {
			$term->name = qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage($term->name);
		}
	}
	return $terms;
}
