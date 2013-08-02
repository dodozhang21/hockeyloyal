<?php get_header(); ?>
<?php $nonce = wp_create_nonce('filter_grid_nonce'); ?>
<div id="content">
	<div class="container cf">
		<div class="content-left cf">
			<div class="content-left-container cf">
				<div id="filter-form" class="cf">
					<span class="filter-label">Filter By:</span>
					<div id="state-prov" class="wrapper-dropdown">
						<span class="label" data-state="CLEAR"><span>State/Prov</span></span>
						<ul class="state-prov dropdown cf">
							<li class="optgroup"><a href="<?php echo admin_url('admin-ajax.php?action=filter_grid&state=CLEAR'.'&nonce='.$nonce) ?>" data-state="CLEAR" data-nonce="<?php echo $nonce ?>">State/Prov</a></li>
							<?php
								$states = $HockeyLoyal->like_acf_get_field_object('province/state');
								$states = $states['choices'];
								
								$total = count($states);
								$half = $total/2;
								$i = 0;
							?>
								<li class="cf">
								<?php foreach($states as $state => $state_name) : 
								?>
									<?php if($i == 0 || $i == $half) : ?>
										<div class="dropdown-container dropdown-<?php echo ($i == 0) ? 'left':'right'; ?>">
									<?php endif; ?>
									<a href="<?php echo admin_url('admin-ajax.php?action=filter_grid&state='.$state.'&nonce='.$nonce) ?>" data-state="<?php echo $state ?>" data-nonce="<?php echo $nonce ?>"><?php echo $state_name; ?></a>
									<?php if($i == ($half-1) || $i == ($total - 1)) : ?>
										</div>
									<?php endif; $i++; ?>
								<?php
								endforeach; ?>
							</li>
						</ul>
					</div>
					<div id="fan-type" class="wrapper-dropdown">
						<span class="label" data-fan-type="CLEAR"><span>Fan Type</span></span>
						<ul class="fan-type dropdown">
							<li class="optgroup"><a href="<?php echo admin_url('admin-ajax.php?action=filter_grid&fan_type=CLEAR'.'&nonce='.$nonce) ?>" data-fan-type="CLEAR" data-nonce="<?php echo $nonce ?>">Fan Type</a></li>
							<?php
								$fan_types = $HockeyLoyal->like_acf_get_field_object('fan_type');
								$fan_types = $fan_types['choices'];
							?>
							<?php foreach($fan_types as $fan_type => $type_name) : ?>
								<li><a href="<?php echo admin_url('admin-ajax.php?action=filter_grid&fan_type='.$fan_type.'&nonce='.$nonce) ?>" data-fan-type="<?php echo $fan_type ?>" data-nonce="<?php echo $nonce ?>"><?php echo $type_name; ?></a></li>
							<?php endforeach; ?>
						</ul>
					</div>
				</div>
				<div id="grid" class="cf">
					<?php //Content filled in through AJAX ?>
				</div>
			</div>
		</div>
		<div class="content-right cf">
			<?php get_sidebar() ?>
		</div>
	</div>
</div>
<?php get_footer(); ?>