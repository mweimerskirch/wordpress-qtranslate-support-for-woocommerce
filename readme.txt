=== qTranslate support for WooCommerce ===
Tags: qtranslate, woocommerce
Requires at least: 3.0.1
Tested up to: 3.8
Stable tag: 1.0.8
License: MIT
License URI: http://plugins.svn.wordpress.org/qtranslate-support-for-woocommerce/trunk/LICENSE

== Description ==

Plugin to make qTranslate work with WooCommerce. No more need to duplicate forms for each language.

In order to use you need both qTranslate and WooCommerce installed.

After it is installed, you can use the qTranslate quicktags (see http://www.qianqin.de/qtranslate/forum/viewtopic.php?f=3&t=3&p=15#p15) for your category names, etc.

e.g. if you use "[:en]Test product[:de]Test-Produkt[:lb]Test-Produit" as a category name, qTranslate - with the help of this plugin - will automatically choose the correct language (in this example either English, German or Luxembourgish) when displaying the category name.

To report a bug or contribute to the plugin, please create an issue at the project page on GitHub: https://github.com/mweimerskirch/wordpress-qtranslate-support-for-woocommerce

I won't answer to requests in the support forum.

= Known issues =
* The plugin only works with "URL Modification Mode" set to "Use Pre-Path Mode" in the qTranslate settings.
* You need to fill out the WooCommerce-related fields ("Product category base", "Product tag base", "Product attribute base") in the permalink settings. Otherwise the link structure changes between languages, leading to 404 errors.
* Using HTTPS only for the checkout pages leads to incorrect redirections. The suggested workaround is to enable HTTPS for the entire site.
* Categories/Attributes/etc show up untranslated in the backend.

== Changelog ==

= 1.1.0 =
* Refactored the code to be more readable
* Compatibility with qTranslate-X

= 1.0.8 = 
* Added suport for get_title on product object (contributed by @JohnyGoerend)

= 1.0.7 =
* Translate shippment methods

= 1.0.6 =
* Bugfix

= 1.0.5 =
* Rewrite post titles when items are sent to paypal during checkout (contributed by @deweydb)
* Check if qTranslate functions exist before executing the filters (prevents sites from breaking during upgrades)

= 1.0.4 =
* Fix for the tag display

= 1.0.3 =
* Fix for the "add to cart" button in the product list
* Fix for the product links (in the cart and possibly other places)

= 1.0.2 =
* Fix for the product attributes displayed in the cart
* Fix for the product categories displayed in the breadcrumbs
* Fix for the product attributes displayed in the "additional informations" tab

= 1.0.1 =
* Bugfix
