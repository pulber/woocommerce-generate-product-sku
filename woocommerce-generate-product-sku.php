<?php
/**
 * Plugin Name: WooCommerce Generate Product SKU
 * Plugin URI: http://pulber.com/woocommerce/woocommerce-generate-product-sku/
 * Description: Generates product SKU.
 * Author: Eugene Pulber
 * Author URI: http://pulber.com/
 * Version: 1.0.0
 */

if (!defined( 'ABSPATH' )) {
  exit; // Exit if accessed directly.
}

if (!class_exists( 'WooCommerce_Generate_Product_Sku' )) {

  final class WooCommerce_Generate_Product_Sku
  {

    public static function hooks() {
      if (!defined( 'WC_VERSION' ) || version_compare( WC_VERSION, '3.0.0', '<' )) {
        return;
      }

      add_filter( 'woocommerce_general_settings', __CLASS__ . '::general_settings' );
      add_action( 'woocommerce_admin_process_product_object', __CLASS__ . '::admin_process_product_object' );
    }

    public static function general_settings($settings) {
      $insert_after_key = array_search( 'woocommerce_price_num_decimals', array_column( $settings, 'id' ) );

      array_splice( $settings, $insert_after_key + 1, 0, array(
        array(
          'id'          => 'woocommerce_product_sku_pattern',
          'type'        => 'text',
          'title'       => __( 'Product SKU Pattern', 'woocommerce' ),
          'desc'        => __( '{product_id}, {timestamp}, {slug}', 'woocommerce' ),
          'css'         => 'width: 250px;',
          'placeholder' => __( 'ex. TEXT-{product_id}', 'woocommerce' ),
          'desc_tip'    => true,
          'autoload'    => false
        )
      ) );

      return $settings;
    }

    public static function admin_process_product_object($product) {
      $sku = isset( $_POST['_sku'] ) ? wc_clean( $_POST['_sku'] ) : null;

      if (empty( $sku ) && $option_product_sku_pattern = get_option( 'woocommerce_product_sku_pattern' )) {
        $product->set_sku( str_replace( array('{product_id}', '{timestamp}', '{slug}'), array($product->get_id(), time(), $product->get_slug()), $option_product_sku_pattern ) );
      }
    }

  }

  add_action( 'woocommerce_init', 'WooCommerce_Generate_Product_Sku::hooks' );

}
