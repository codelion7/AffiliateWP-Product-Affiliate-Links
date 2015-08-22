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
			'post_type'      => 'download',
			'posts_per_page' => 0,
			'orderby'        => 'title',
			'order'          => 'ASC',
		);

		$products = get_posts( $args );

		return $products;
	}

	public function get_products_referral_rates( $affiliate_id ) {
		
		$products = $this->get_products();
		$rates    = array();

		foreach ( $products as $product ) {

			if ( edd_has_variable_prices( $product->ID ) ) {
				
				$price = absint( edd_get_highest_price_option( $product->ID ) );

			} else {

				$price = absint( edd_get_download_price( $product->ID ) );
			}

			$product_rate = get_post_meta( $product->ID, '_affwp_edd_product_rate', true );

			if ( ! empty( $product_rate )  ) {
				
				$rates[ $product->ID ] = $product_rate;

			} elseif ( 0 == $price ) {
				
				$rates[ $product->ID ] = __( 'Free', 'affwp-paffl' );

			} elseif ( 0 < $price ) {

				$rates[ $product->ID ] = affwp_get_affiliate_rate( $affiliate_id, true );

			}
		}

		return $rates;
	}
}