<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit();

/**
* AFFWP_PAFFL_Integrations class
*
* This class is responsible for managing affwp paffl integrations
*
* @since 1.0
*/
class AFFWP_PAFFL_Integrations {

	/**
	 * AffiliateWP referral variable set by user
	 *
	 * @since 1.0
	 * @var string
	 */
	public $referral_var;

	/**
	 * Enabled AffiliateWP integrations
	 *
	 * @since 1.0
	 * @var array
	 */
	private $enabled_integrations;

	/**
	 * EDD integration object
	 *
	 * @since 1.0
	 * @var object
	 */
	public $edd;

	/**
	 * Class __construct function
	 *
	 * @since 1.0
	 */
	public function __construct() {

		$this->referral_var         = affiliate_wp()->settings->get( 'referral_var', 'ref' );
		$this->enabled_integrations = affiliate_wp()->settings->get( 'integrations', array() );
		$this->load();

		add_action( 'affwp_affiliate_dashboard_bottom', array( $this, 'product_affiliate_links' ) );
	}

	/**
	 * Get things started
	 * 
	 * @since 1.0
	 */
	public function load() {

		// Load enabled integrations file
		if ( ! empty( $this->enabled_integrations ) ) {
			include AFFWP_PAFFL_PLUGIN_PATH . '/includes/integrations/class-base.php';
		}

		foreach ( $this->enabled_integrations as $name => $integration ) {

			if ( file_exists( AFFWP_PAFFL_PLUGIN_PATH . '/includes/integrations/class-' . $name . '.php' ) ) {

				include AFFWP_PAFFL_PLUGIN_PATH . '/includes/integrations/class-' . $name . '.php';
				
			}

			$class_name = 'AFFWP_PAFFL_' . strtoupper( $name );

			$this->$name = new $class_name();
		}
	}

	/**
	 * Display product affiliate links table
	 *
	 * Hooked with affwp_affiliate_dashboard_bottom hook in __construct function
	 * 
	 * @since  1.0
	 * @param  integer $affiliate_id ID of the affiliate from the filter
	 */
	public function product_affiliate_links( $affiliate_id ) {

		if ( isset( $_GET['tab'] ) && 'urls' != $_GET['tab'] ) {
			return;
		}

		ob_start();
		
		include AFFWP_PAFFL_PLUGIN_PATH . '/templates/product-affiliate-links.php';

		echo ob_get_clean();
	}

	/**
	 * Get products of all enabled integrations
	 *
	 * @since 1.0
	 * @return array Array of merged products of all enabled integrations
	 */
	public function get_products() {

		$products = array();

		foreach ( $this->enabled_integrations as $name => $integration ) {

			$products = array_replace_recursive( $this->$name->get_products(), $products );
		}

		return $products;
	}

	/**
	 * Get products referral rates
	 *
	 * @since 1.1
	 * @return array Referral rates from enabled integrations
	 */
	public function get_products_referral_rates( $affiliate_id ) {

		$rates = array();

		foreach ( $this->enabled_integrations as $name => $integration ) {

			$rates = array_replace_recursive( $this->$name->get_products_referral_rates( $affiliate_id ), $rates );
		}

		return $rates;
	}
}