<?php

/*
Plugin Name: qTranslate support for WooCommerce
Plugin URI: https://github.com/mweimerskirch/wordpress-qtranslate-support-for-woocommerce
Description: Makes qTranslate work with WooCommerce
Version: 1.1.0
Author: Michel Weimerskirch
Author URI: http://michel.weimerskirch.net
License: MIT
*/

class qTranslateSupportForWoocommerce
{
	public function __construct()
	{
		/* Translate category names*/
		add_action('woocommerce_before_subcategory', array($this, 'woocommerce_before_subcategory'));

		/* Translate payment gateway title and description */
		add_filter('woocommerce_gateway_title', array($this, 'translate'), 0);
		add_filter('woocommerce_gateway_description', array($this, 'translate'), 0);

		/* Fix qTranslate WooCommerce AJAX URLs */
		add_filter('admin_url', array($this, 'admin_url'));

		/* Various translation filters*/
		add_filter('the_title_attribute', array($this, 'translate'), 0);
		add_filter('woocommerce_attribute_label', array($this, 'translate'), 0);
		add_filter('woocommerce_variation_option_name', array($this, 'translate'), 0);
		add_filter('woocommerce_page_title', array($this, 'translate'), 0);
		add_filter('woocommerce_product_title', array($this, 'translate'), 0);
		add_filter('woocommerce_cart_shipping_method_full_label', array($this, 'translate'), 0);

		/* Replace the "sanitize_title" filter from qTranslate with a custom implementation that prevents accents to be replaced language-specifically as this leads to problems with product attributes in WooCommerce. */
		remove_filter('sanitize_title', 'qtrans_useRawTitle', 0, 3);
		remove_filter('sanitize_title', 'qtranxf_useRawTitle', 0, 3);
		add_filter('sanitize_title', array($this, 'sanitize_title'), -10, 3);

		/* Fix the categories displayed on the single product pages */
		add_filter('get_the_terms', array($this, 'get_the_terms'));

		/* Fix the product attributes displayed in the cart */
		add_filter('get_term', array($this, 'get_term'));
		/* Fix the product categories and tags */
		add_filter('wp_get_object_terms', array($this, 'wp_get_object_terms'));
		/* Fix the product attributes displayed in the "additional informations" tab */
		add_filter('woocommerce_attribute', array($this, 'woocommerce_attribute'));
		/* Fix the "add to cart" button in the product list */
		add_filter('woocommerce_add_to_cart_url', array($this, 'convertURL'));

		/* Fix the product links (in the cart and possibly other places) */
		add_filter('post_type_link', array($this, 'post_type_link'), 10, 2);
		/* Rewrite post titles when items are sent to paypal during checkout */
		add_filter('woocommerce_paypal_args', array($this, 'woocommerce_paypal_args'));
	}

	private function isEnabled()
	{
		return (function_exists('qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage') || function_exists('qtranxf_useCurrentLanguageIfNotFoundUseDefaultLanguage'));
	}

	public function translate($text)
	{
		if (function_exists('qtranxf_useCurrentLanguageIfNotFoundUseDefaultLanguage')) {
			return qtranxf_useCurrentLanguageIfNotFoundUseDefaultLanguage($text);
		} else {
			return qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage($text);
		}
	}

	public function convertURL($url, $lang = '')
	{
		if (function_exists('qtranxf_convertURL')) {
			return qtranxf_convertURL($url, $lang);
		} else {
			return qtrans_convertURL($url, $lang);
		}
	}

	public function useDefaultLanguage($text)
	{
		if (function_exists('qtranxf_useDefaultLanguage')) {
			return qtranxf_useDefaultLanguage($text);
		} else {
			return qtrans_useDefaultLanguage($text);
		}
	}

	public function woocommerce_before_subcategory($category)
	{
		if (!$this->isEnabled()) return $category;

		$category->name = $this->translate($category->name);
		return $category;
	}


	public function admin_url($url)
	{
		if ($url == '/wp-admin/admin-ajax.php') {
			global $q_config;
			$url = $url . '?lang=' . $q_config['language'];
		}
		return $url;
	}

	public function sanitize_title($title, $raw_title = '', $context = 'save')
	{
		if (!$this->isEnabled()) return $title;

		if ('save' == $context) {
			if ($raw_title == '') $raw_title = $title;
			$raw_title = $this->useDefaultLanguage($raw_title);

			// Temporarily set a dummy language so the "remove_accents" method is not language-specific
			add_filter('locale', array($this, 'returnDummyLanguage'), 100);

			$title = remove_accents($raw_title);

			// Restore the return value of the "get_locale()" method
			remove_filter('locale', array($this, 'returnDummyLanguage'), 100);
		}
		return $title;
	}

	public function returnDummyLanguage()
	{
		return 'dummy';
	}

	public function get_the_terms($terms)
	{
		if (!$this->isEnabled()) return $terms;

		foreach ($terms as $term) {
			if ($term->taxonomy == 'product_cat') {
				$term->name = $this->translate($term->name);
			}
		}
		return $terms;
	}

	public function get_term($term)
	{
		if (!$this->isEnabled()) return $term;

		if (substr($term->taxonomy, 0, 3) == 'pa_') {
			$term->name = $this->translate($term->name);
		}
		return $term;
	}

	public function wp_get_object_terms($terms)
	{
		if (!$this->isEnabled()) return $terms;

		foreach ($terms as $term) {
			if ($term->taxonomy == 'product_cat' || $term->taxonomy == 'product_tag') {
				$term->name = $this->translate($term->name);
			}
		}
		return $terms;
	}

	public function woocommerce_attribute($text)
	{
		if (!$this->isEnabled()) return $text;

		$values = explode(', ', $text);
		foreach ($values as $i => $val) {
			$values[$i] = $this->translate($val);
		}
		return implode(', ', $values);
	}

	public function post_type_link($post_link, $post)
	{
		if (!$this->isEnabled()) return $post_link;

		if ($post->post_type == 'product') {
			$post_link = $this->convertURL($post_link);
		}
		return $post_link;
	}

	public function woocommerce_paypal_args($paypal_args)
	{
		if (!$this->isEnabled()) return $paypal_args;

		foreach ($paypal_args as $key => $value) {
			if (strpos($key, 'item_name_') !== false) {
				$paypal_args[$key] = $this->translate($paypal_args[$key]);
			}
		}
		return $paypal_args;
	}

}

new qTranslateSupportForWoocommerce();