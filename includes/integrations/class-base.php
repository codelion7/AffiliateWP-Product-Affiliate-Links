<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit();

/**
* AFFWP_PAFFL_Integrations_Base class
*
* This base class is responsible for managing affwp paffl integrations
*
* @since 1.0
*/
class AFFWP_PAFFL_Integrations_Base {
	/**
	 * Class __construct function
	 *
	 * @since 1.0
	 */
	public function __construct() {
		$this->load();
	}

	/**
	 * Get things started
	 *
	 * @since 1.0
	 */
	public function load() {
		
	}

	/**
	 * Get products that have price more than 0
	 *
	 * @since 1.1
	 * @return array Products object
	 */
	public function get_products( $post_type ) {
		$args = array(
			'post_type'      => $post_type,
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
	public function get_product_referral_rate( $integration, $product_id, $price, $affiliate_id ) {
		$product_rate     = get_post_meta( $product_id, '_affwp_' . $integration . '_product_rate', true );
		$disabled_product = get_post_meta( $product_id, '_affwp_' . $integration . '_referrals_disabled', true );

		if ( 1 == $disabled_product ) {
			$rate = __( 'Disabled', 'affwp-paffl' );
		} elseif ( ! empty( $product_rate )  ) {
			$rate = $product_rate . '%';
		} elseif ( 0 == $price ) {
			$rate = __( 'Free', 'affwp-paffl' );
		} elseif ( 0 < $price ) {
			$rate = affwp_get_affiliate_rate( $affiliate_id, true );
		}

		return $rate;
	}

	/**
	 * Get products affiliate links
	 * @param  string $integration  Name of AffiliateWP integration
	 * @param  array  $products     Array of products
	 * @param  int    $affiliate_id ID of an affiliate
	 * @return array                Array of products affiliate links
	 */
	public function get_products_affiliate_links( $integration, $products, $affiliate_id ) {
		$referral_format = affiliate_wp()->settings->get( 'referral_format', 'id' );
		$referral_var    = affiliate_wp()->settings->get( 'referral_var', 'ref' );

		if ( 'id' == $referral_format ) {
			$affiliate_id = affwp_get_affiliate_id( $affiliate_id );
		} else {
			$affiliate_id = affwp_get_affiliate_username( $affiliate_id );
		}

		$aff_links = array();

		foreach ( $products as $product ) {
			$aff_links[ $product->ID ] = add_query_arg( array( $referral_var => $affiliate_id ), get_permalink( $product->ID ) );
		}

		return $aff_links;
	}
}