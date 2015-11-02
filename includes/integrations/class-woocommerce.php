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
	 * @since 1.0
	 * @param  integer $affiliate_id ID of an affiliate
	 * @return array                 List of product referral rates
	 */
	public function get_products_referral_rates( $affiliate_id ) {
		$rates    = array();

		foreach ( $this->products as $product ) {
			$wc_product = wc_get_product( $product->ID );
			$price      = $wc_product->price;

			$rates[ $product->ID ] = parent::get_product_referral_rate( 'woocommerce', $product->ID, $price, $affiliate_id );
		}

		return $rates;
	}

	/**
	 * Get products affiliate links
	 * 
	 * Overridden function of base class function
	 * @param  string $integration  Name of AffiliateWP integration
	 * @param  array  $products     Array of products
	 * @param  int    $affiliate_id ID of an affiliate
	 * @return array                Array of products affiliate links
	 */
	public function get_products_affiliate_links( $integration, $products, $affiliate_id ) {
		$aff_links = parent::get_products_affiliate_links( $integration, $this->products, $affiliate_id );

		return $aff_links;
	}
}