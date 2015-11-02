<?php
/**
 * Template file for displaying product affiliate links table
 *
 * @author Yudhistira Mauris <mauris@yudhistiramauris.com>
 * @since 1.0
 */
?>

<div id="affwp-affiliate-dashboard-product-affiliate-links" class="affwp-tab-content" style="padding-top: 20px;">

	<h4><?php _e( 'Product Affiliate Links', 'affwp-paffl' ); ?></h4>

	<table class="affwp-table">
		<thead>
			<tr>
				<th><?php _e( 'Products', 'affwp-paffl' ); ?></th>
				<th><?php _e( 'Rate', 'affwp-paffl' ); ?></th>
				<th><?php _e( 'Commission', 'affwp-paffl' ); ?></th>
				<th><?php _e( 'Affiliate Links', 'affwp-paffl' ); ?></th>
			</tr>
		</thead>

		<tbody>

			<?php $products     = affwp_paffl()->integrations->get_products(); ?>
			<?php $rates		= affwp_paffl()->integrations->get_products_referral_rates( $affiliate_id ); ?>
			<?php $commission	= affwp_paffl()->integrations->get_products_commission( $affiliate_id ); ?>
			<?php $aff_links	= affwp_paffl()->integrations->get_products_affiliate_links( $affiliate_id ); ?>

			<?php foreach ( $products as $product ) : ?>

			<tr>
				<td><?php echo $product->post_title; ?></td>
				<td><?php echo $rates[ $product->ID ]; ?></td>
				<td><?php echo $commission[ $product->ID ]; ?></td>
				<td><?php echo $aff_links[ $product->ID ] ?></td>
			</tr>

			<?php endforeach; ?>
		</tbody>
	</table>

	<?php do_action( 'affwp_affiliate_dashboard_after_product_affiliate_links', affwp_get_affiliate_id() ); ?>

</div>