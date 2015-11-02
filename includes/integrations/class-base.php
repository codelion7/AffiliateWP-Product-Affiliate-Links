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
	 * @since  1.1
	 * @param  string  $integration  Name of AffiliateWP integration
	 * @param  int     $product_id   ID of a product
	 * @param  string  $price 		 Price of a product
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
			$rate = __( 'Free Product', 'affwp-paffl' );
		} elseif ( 0 < $price ) {
			$rate = affwp_get_affiliate_rate( $affiliate_id, true );
		}

		return $rate;
	}

	/**
	 * Get products affiliate links
	 * @since  1.1
	 * @param  string $integration  Name of AffiliateWP integration
	 * @param  array  $products     Array of products
	 * @param  int    $affiliate_id ID of an affiliate
	 * @return array                Array of products affiliate links
	 */
	public function get_products_affiliate_links( $integration, $products, $affiliate_id ) {
		$referral_format = affiliate_wp()->settings->get( 'referral_format', 'id' );
		$referral_var    = affiliate_wp()->settings->get( 'referral_var', 'ref' );
		$pretty_url      = affwp_is_pretty_referral_urls();

		if ( 'id' == $referral_format ) {
			$affiliate_id = affwp_get_affiliate_id( $affiliate_id );
		} else {
			$affiliate_id = affwp_get_affiliate_username( $affiliate_id );
		}

		$aff_links = array();

		foreach ( $products as $product ) {
			if ( false === $pretty_url ) {
				$aff_links[ $product->ID ] = add_query_arg( array( $referral_var => $affiliate_id ), get_permalink( $product->ID ) );
			} else {
				$aff_links[ $product->ID ] = get_permalink( $product->ID ) . trailingslashit( $referral_var ) . trailingslashit( $affiliate_id );
			}
		}

		return $aff_links;
	}

	/**
	 * Get products commission from all integrations
	 * @since  1.1
	 * @param  string  $integration  Name of AffiliateWP integration
	 * @param  string  $product_id   ID of a product
	 * @param  string  $price 		 Price of a product
	 * @param  integer $affiliate_id ID of an affiliate
	 * @return string                Product commission
	 */
	public function get_product_commission( $integration, $product_id, $price, $affiliate_id ) {
		$referral_rate = $this->get_product_referral_rate( $integration, $product_id, $price, $affiliate_id );

		if ( 'free product' == strtolower( $referral_rate ) || 'disabled' == strtolower( $referral_rate ) ) {
			$commission = affwp_currency_filter( 0 );
		} else {
			$referral_rate = absint( str_replace( '%', '', $referral_rate ) );

			if ( is_array( $price ) ) {
				$commission = affwp_currency_filter( ( $referral_rate / 100 ) * $price[0] ) . ' - ' . affwp_currency_filter( ( $referral_rate / 100 ) * $price[1] );
			} else {
				$commission = affwp_currency_filter( ( $referral_rate / 100 ) * $price );
			}
		}
		
		return $commission;
	}
}