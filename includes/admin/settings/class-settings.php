<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit();

/**
* AFFWP_PAFFL_Settings class
*
* This class is responsible for managing affwp paffl settings
*
* @since 1.0
*/
class AFFWP_PAFFL_Settings {

	/**
	 * AffiliateWP options
	 *
	 * @since 1.0
	 * @var array
	 */
	private $options;

	/**
	 * Class __construct function
	 *
	 * @since 1.0
	 */
	public function __construct() {

		$this->options = affiliate_wp()->settings->get_all();

		add_filter( 'affwp_settings_integrations', array( $this, 'settings' ) );

		add_action( 'admin_init', array( $this, 'activate_license' ) );
		add_action( 'admin_init', array( $this, 'deactivate_license' ) );
	}

	/**
	 * Settings items of AffiliateWP Product Affiliate Links
	 *
	 * @since 1.0
	 * @param  array $settings Already set settings items
	 * @return array           Filtered settings items
	 */
	public function settings( $settings ) {
		$paffl_settings = apply_filters( 'affwp_paffl_settings', 
			array(
				'paffl_settings' => array(
					'name' => __( 'Product Affiliate Links', 'affwp-paffl' ),
					'desc' => '',
					'type' => 'header',
				),
				'paffl_license_key' => array(
					'name' => __( 'License Key', 'affwp-paffl' ),
					'desc' => $this->license_button() . '<p class="description">' . sprintf( __( 'An active license is required to get plugin updates and <a href="%s" target="_blank">support</a>.', 'affwp-paffl' ), 'https://www.yudhistiramauris.com/support/' ) . '</p>',
					'type' => 'text',
					'size' => 'regular',
				),
			)
		);

		$settings = array_merge( $settings, $paffl_settings );

		return $settings;
	}

	public function license_button() {
		
		$license_status = affiliate_wp()->settings->get( 'paffl_license_status', '' );

		$html = '';

		if ( 'valid' === $license_status ) {
			
			$html .= '<input type="submit" class="button" name="affwp_paffl_deactivate_license" value="' . esc_attr__( 'Deactivate License', 'affwp-paffl' ) . '"/>';
			$html .= '<span style="color:green;">&nbsp;' . __( 'Your license is valid!', 'affwp-paffl' ) . '</span>';

		} else {

			$html .= '<input type="submit" class="button" name="affwp_paffl_activate_license" value="' . esc_attr__( 'Activate License', 'affwp-paffl' ) . '"/>';
		}

		return $html;
	}

	/**
	 * License activation function
	 *
	 * @since 1.0
	 */
	public function activate_license() {
		
		if ( ! isset( $_POST['affwp_settings'] ) ) {
			return;
		}

		if ( ! isset( $_POST['affwp_paffl_activate_license'] ) ) {
			return;
		}

		if ( ! isset( $_POST['affwp_settings']['paffl_license_key'] ) ) {
			return;
		}

		$license_key = trim( $_POST['affwp_settings']['paffl_license_key'] );

		$api_params = array(
			'edd_action' => 'activate_license',
			'item_name'  => urlencode( AFFWP_PAFFL_ITEM_NAME ),
			'license'    => $license_key,
			'url'        => home_url(),
		);

		$response = wp_remote_post( AFFWP_PAFFL_STORE_URL, array(
			'body'      => $api_params,
			'timeout'   => 15,
			'sslverify' => false,
		) );

		if ( is_wp_error( $response ) ) {
			return false;
		}

		$license_data = json_decode( wp_remote_retrieve_body( $response ) );
		
		$options                         = $this->options;
		$options['paffl_license_key']    = $license_key;
		$options['paffl_license_status'] = $license_data->license;

		update_option( 'affwp_settings', $options );
	}

	/**
	 * License deactivation function
	 *
	 * @since 1.0
	 */
	public function deactivate_license() {
		
		if ( ! isset( $_POST['affwp_settings'] ) ) {
			return;
		}

		if ( ! isset( $_POST['affwp_paffl_deactivate_license'] ) ) {
			return;
		}

		if ( ! isset( $_POST['affwp_settings']['paffl_license_key'] ) ) {
			return;
		}

		$license_key = trim( $_POST['affwp_settings']['paffl_license_key'] );

		$api_params = array(
			'edd_action' => 'deactivate_license',
			'item_name'  => urlencode( AFFWP_PAFFL_ITEM_NAME ),
			'license'    => $license_key,
			'url'        => home_url(),
		);

		$response = wp_remote_post( AFFWP_PAFFL_STORE_URL, array(
			'body'      => $api_params,
			'timeout'   => 15,
			'sslverify' => false,
		) );

		if ( is_wp_error( $response ) ) {
			return false;
		}

		$license_data = json_decode( wp_remote_retrieve_body( $response ) );
		
		$options                         = $this->options;
		$options['paffl_license_key']    = '';
		$options['paffl_license_status'] = '';

		update_option( 'affwp_settings', $options );
	}
}

new AFFWP_PAFFL_Settings();