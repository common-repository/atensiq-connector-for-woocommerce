=== Atensiq Restaurant menu & Connector for WooCommerce ===
Contributors: atensiq
Tags: restaurant menu, food menu, qr menu, food ordering, restaurant orders, woocommerce
Requires at least: 4.6
Tested up to: 5.5.3
Stable tag: 3.0.1
Requires PHP: 5.6
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Instant order notifications from WooCommerce in your restaurant´s Atensiq panel. See how Atensiq works on https://atensiq.com

== Description ==

Atensiq Restaurant menu & Connector for WooCommerce connects your WooCommerce with the [Atensiq](https://atensiq.com/) notification service.

This plugin allows to receive instant order notifications from your online restaurant.

It can be used for remote orders as well as for local orders where your guests can place orders directly to the kitchen.

Note that you will need a valid [subscription](https://atensiq.com/subscriptions/) to the Atensiq service in order to use this plugin.

Learn more about Atensiq [here](https://atensiq.com/features/).

= Atensiq service main features =

* Instant order notifications from WooCommerce
* Waiter call
* Bill request
* Customer satisfaction surveys

== Installation ==

In order to use Atensiq Connector for WooCommerce you will need a valid [subscription](https://atensiq.com/subscriptions/)!

1. Upload plugin files to the '/wp-content/plugins/woocommerce-atensiq' directory, or install the plugin through the WordPress plugins screen directly and activate it.
2. Access your Atensiq admin panel -> Settings and copy Sbn ID and Secret from the API section.
3. Go to Wordpress admin panel -> WooCommerce -> Atensiq and paste the Sbn ID and Secret into the corresponding fields.

Make sure you have enabled the online orders service in the Atensiq admin panel (see the cart button on the quick options bar).

== Quickcart Shortcode ==

Quickcart shortcode allows to list products from different categories on the same page. 
You can add products to the cart directly from the list. Works with variable products too.
Great for use as a restaurant menu product list.

Note that it is not required to use this shortcode for Atensiq´s online orders notification feature.

Usage:

[wcat-quickcart cats=""]

Options:

cats - product category term slugs, separated by comma

See an example of the quickcart shortcode in action [here](https://restaurant.shopixpress.com/).

== Changelog ==

= 2.0.0 - 2020-06-19 =

** wcat-quickcart shortcode
* Added Remove from cart function.
* WooCommerce minicart sync.
* Responsive design improvements

** API
* Authorization bug fixed.

= 3.0.0 - 2020-06-28 =

** wcat-quickcart shortcode
* Added Local orders feature.

= 3.0.1 - 2020-12-03 =

** order placement
* changed order placement hook to woocommerce_order_status_processing
