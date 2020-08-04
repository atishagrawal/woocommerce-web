<div class="wclp_admin_sidebar">  
	<div class="wclp_admin_sidebar_inner">
		<div class="wclp-sidebar__section">
			<h3><?php _e('More plugins by zorem', '') ?></h3>
			<?php $plugin_list = wc_local_pickup()->admin->get_zorem_pluginlist(); ?>	
		<ul>
			<?php foreach($plugin_list as $plugin){ 
				if( 'Advanced Local Pickup for WooCommerce' != $plugin->title ) { 
				?>
					<li><img class="plugin_thumbnail" src="<?php echo $plugin->image_url; ?>"><a class="plugin_url" href="<?php echo $plugin->url; ?>" target="_blank"><?php echo $plugin->title; ?></a></li>
				<?php } 
			} ?>
			</ul>
		</div> 
	</div>
</div>