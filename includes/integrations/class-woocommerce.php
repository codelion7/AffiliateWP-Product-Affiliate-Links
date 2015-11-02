<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit();

/**
* AFFWP_PAFFL_WOOCOMMERCE class
*
* This class is responsible for managing affwp paffl EDD integrations
*
* @since 1.0
*/
class AFFWP_PAFFL_WOOCOMMERCE extends AFFWP_PAFFL_Integrations_Base {

	/**
	 * All EDD Downloads
	 *
	 * @since 1.0
	 */
	public $products;

	/**
	 * Get things started
	 *
	 * @since 1.0
	 */
	public function load() {
		$this->products = $this->get_products( 'product' );
	}

	/**
	 * Get products that have price more than 0
	 *
	 * @since 1.1
	 * @return array Products object
	 */
	public function get_products( $post_type ) {
		$products = parent::get_products( $post_type );

		return $products;
	}

	/**
	 * Get products referral rates of an affiliate
	 *
	 * @since 1.1
	 * @param  integer $affiliate_id ID of an affiliate
	 * @return array                 List of product referral rates
	 */
	public function get_products_referral_rates( $affiliate_id ) {
		$rates    = array();

		foreach ( $this->products as $product ) {
			$price      = $this->get_product_price( $product->ID );

			$rates[ $product->ID ] = parent::get_product_referral_rate( 'woocommerce', $product->ID, $price, $affiliate_id );
		}

		return $rates;
	}

	/**
	 * Get products affiliate links
	 * 
	 * Overridden function of base class function
	 * @since 1.1
	 * @param  string $integration  Name of AffiliateWP integration
	 * @param  array  $products     Array of products
	 * @param  int    $affiliate_id ID of an affiliate
	 * @return array                Array of products affiliate links
	 */
	public function get_products_affiliate_links( $integration, $products, $affiliate_id ) {
		$aff_links = parent::get_products_affiliate_links( $integration, $this->products, $affiliate_id );

		return $aff_links;
	}

	/**
	 * Get products commission from all integrations
	 * @since 1.1
	 * @param  string  $integration  Name of AffiliateWP integration
	 * @param  array   $products     Array of products
	 * @param  string  $price 		 Price of a product
	 * @param  integer $affiliate_id ID of an affiliate
	 * @return array                 Array of products commission
	 */
	public function get_products_commission( $affiliate_id ) {
		$commission = array();

		foreach ( $this->products as $product ) {
			$price = $this->get_product_price( $product->ID );

			$commission[ $product->ID ] = parent::get_product_commission( 'woocommerce', $product->ID, $price, $affiliate_id );
		}

		return $commission;
	}

	/**
	 * Get product price
	 * @since 1.1
	 * @param  int    $product_id ID of a product
	 * @return array|string       Price array if varaible product, string otherwise
	 */
	public function get_product_price( $product_id ) {
		$product = wc_get_product( $product_id );

		if ( $product->is_type( 'variable' ) ) {
			$product = new WC_Product_Variable( $product );

			$price = array();
			$price[0] = $product->min_variation_price;
			$price[1] = $product->max_variation_price;
		} else {
			$price = $product->price;
		}

		return $price;
	}
}