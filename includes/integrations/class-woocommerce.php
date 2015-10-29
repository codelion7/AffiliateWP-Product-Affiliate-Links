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
	private $products;

	/**
	 * Get things started
	 *
	 * @since 1.0
	 */
	public function load() {

		$this->products = $this->get_products();
	}

	/**
	 * Get products that have price more than 0
	 *
	 * @since 1.0
	 * @return array Products object
	 */
	public function get_products() {
		
		$args = array(
			'post_type'      => 'product',
			'posts_per_page' => -1,
			'orderby'        => 'title',
			'order'          => 'ASC',
		);

		$products = get_posts( $args );

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
		
		$products = $this->get_products();
		$rates    = array();

		foreach ( $products as $product ) {

			$wc_product = wc_get_product( $product->ID );

			$price = $wc_product->price;
			
			$product_rate = get_post_meta( $product->ID, '_affwp_woocommerce_product_rate', true );
			
			$disabled_product = get_post_meta( $product->ID, '_affwp_woocommerce_referrals_disabled', true );

			if ( 1 == $disabled_product ) {

				$rates[ $product->ID ] = __( 'Disabled', 'affwp-paffl' );

			} elseif ( ! empty( $product_rate )  ) {
				
				$rates[ $product->ID ] = $product_rate . '%';

			} elseif ( 0 == $price ) {
				
				$rates[ $product->ID ] = __( 'Free', 'affwp-paffl' );

			} elseif ( 0 < $price ) {

				$rates[ $product->ID ] = affwp_get_affiliate_rate( $affiliate_id, true );

			}
		}

		return $rates;
	}
}