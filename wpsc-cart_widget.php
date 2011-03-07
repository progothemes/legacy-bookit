<?php if(isset($cart_messages) && count($cart_messages) > 0) { ?>
	<?php foreach((array)$cart_messages as $cart_message) { ?>
	  <span class="cart_message"><?php echo $cart_message; ?></span>
	<?php } ?>
<?php } ?>
<div class="cartcount"><strong>(<?php echo wpsc_cart_item_count(); ?>)</strong> Items in your cart</div>
<?php if(wpsc_cart_item_count() > 0): ?>
    <div class="shoppingcart">
	<table cellpadding="0" cellspacing="0" width="295">
		<tbody>
		<?php while(wpsc_have_cart_items()): wpsc_the_cart_item(); ?>
			<tr>
				<td class="product-name first" width="210"><?php echo wpsc_cart_item_name(); ?> (<?php echo wpsc_cart_item_quantity(); ?>)</td>
				<td width="85"><?php echo wpsc_cart_item_price(); ?></td>
			</tr>	
		<?php endwhile; ?>
		</tbody>
		<tfoot>
			<?php if( ( wpsc_cart_has_shipping() && !wpsc_cart_show_plus_postage() ) || ( (wpsc_cart_tax(false) >0) && !wpsc_cart_show_plus_postage() ) ) : ?>
			<?php if(wpsc_cart_has_shipping() && !wpsc_cart_show_plus_postage()) : ?>
			<tr class="cart-widget-total cart-widget-shipping">
				<td class="pricedisplay checkout-shipping first"><?php _e('Shipping', 'wpsc'); ?> :</td><td><?php echo wpsc_cart_shipping(); ?></td></tr>
					<?php endif; ?>
					<?php if( (wpsc_cart_tax(false) >0) && !wpsc_cart_show_plus_postage()) : ?>
			<tr class="cart-widget-total cart-widget-shipping">
				<td class="pricedisplay checkout-tax first"><?php echo wpsc_display_tax_label(true); ?> :</td><td><?php echo wpsc_cart_tax(); ?></td></tr>
					<?php endif; ?>
			<?php endif; ?>
			<tr class="cart-widget-total" valign="top">
				<td class="pricedisplay checkout-total first"><?php _e('Total', 'wpsc'); ?> :
			<?php if(wpsc_cart_show_plus_postage()) : ?><br />+ <?php _e('Postage &amp; Tax ', 'wpsc'); ?><?php endif; ?></td><td><?php echo wpsc_cart_total_widget(); ?></td>
			</tr>			
			<tr class="links"><td colspan="2"><table width="100%" cellpadding="0" cellspacing="0">
            <tr><td id='cart-widget-links' width="100"><a target="_parent" href="<?php echo get_option('shopping_cart_url'); ?>" title="<?php _e('Checkout', 'wpsc'); ?>" class="gocheckout"><?php _e('Checkout', 'wpsc'); ?></a></td><td class="first"><form action="" method="post" class="wpsc_empty_the_cart">
						<input type="hidden" name="wpsc_ajax_action" value="empty_cart" />
							<a target="_parent" href="<?php echo htmlentities(add_query_arg('wpsc_ajax_action', 'empty_cart', remove_query_arg('ajax')), ENT_QUOTES, 'UTF-8'); ?>" class="emptycart" title="<?php _e('Empty Your Cart', 'wpsc'); ?>"><?php _e('Empty Cart', 'wpsc'); ?></a>                                                                                    
					</form>
				</td>
			</tr>
            </table></td></tr>
		</tfoot>
	</table>
	</div><!--close shoppingcart-->		
<?php endif; ?>

<?php
wpsc_google_checkout();
?>