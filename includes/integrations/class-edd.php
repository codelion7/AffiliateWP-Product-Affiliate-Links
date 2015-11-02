<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit();

/**
* AFFWP_PAFFL_EDD class
*
* This class is responsible for managing affwp paffl EDD integrations
*
* @since 1.0
*/
class AFFWP_PAFFL_EDD extends AFFWP_PAFFL_Integrations_Base {

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
		$this->products = $this->get_products( 'download' );
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
			$price = $this->get_product_price( $product->ID );

			$rates[ $product->ID ] = parent::get_product_referral_rate( 'edd', $product->ID, $price, $affiliate_id );
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

			$commission[ $product->ID ] = parent::get_product_commission( 'edd', $product->ID, $price, $affiliate_id );
		}

		return $commission;
	}

	/**
	 * Get product price
	 * @since 1.1
	 * @param  int    $product_id ID of a product
	 * @return array|string       Array of price if variable product, string otherwise
	 */
	public function get_product_price( $product_id ) {
		if ( edd_has_variable_prices( $product_id ) ) {
			$price = array();
			$price[0] = absint( edd_get_lowest_price_option( $product_id ) );
			$price[1] = absint( edd_get_highest_price_option( $product_id ) );
		} else {
			$price = absint( edd_get_download_price( $product_id ) );
		}

		return $price;
	}

}