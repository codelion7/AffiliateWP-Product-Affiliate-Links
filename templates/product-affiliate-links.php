<div id="affwp-affiliate-dashboard-product-affiliate-links" class="affwp-tab-content" style="padding-top: 20px;">

	<h4><?php _e( 'Product Affiliate Links', 'affwp-paffl' ); ?></h4>

	<table class="affwp-table">
		<thead>
			<tr>
				<th><?php _e( 'Products', 'affwp-paffl' ); ?></th>
				<th><?php _e( 'Commission', 'affwp-paffl' ); ?></th>
				<th><?php _e( 'Affiliate Links', 'affwp-paffl' ); ?></th>
			</tr>
		</thead>

		<tbody>

			<?php $products     = affwp_paffl()->integrations->get_products(); ?>
			<?php $referral_var = affwp_paffl()->integrations->referral_var; ?>
			<?php $affiliate_id = affwp_get_affiliate_id(); ?>
			<?php $rates		= affwp_paffl()->integrations->get_products_referral_rates( $affiliate_id ); ?>
<?php //wp_die( var_dump( $rates) ); ?>
			<?php foreach ( $products as $product ) : ?>

			<tr>
				<td><?php echo $product->post_title; ?></td>
				<td><?php echo $rates[ $product->ID ]; ?></td>
				<td><?php echo add_query_arg( array( $referral_var => $affiliate_id ), get_permalink( $product->ID ) ); ?></td>
			</tr>

			<?php endforeach; ?>
		</tbody>
	</table>

	<?php do_action( 'affwp_affiliate_dashboard_after_product_affiliate_links', affwp_get_affiliate_id() ); ?>

</div>