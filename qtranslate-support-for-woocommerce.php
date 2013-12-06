<?php
/*
Plugin Name: qTranslate support for WooCommerce
Plugin URI: https://github.com/mweimerskirch/wordpress-qtranslate-support-for-woocommerce
Description: Makes qTranslate work with WooCommerce
Version: 0.1
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

