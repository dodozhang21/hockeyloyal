<?php get_header(); ?>
<div id="content">
	<div class="container cf">
		<div class="content-left cf">
			<div class="content-left-container cf">
				<?php hockey_profile_nav() ?>
				<?php if(!$HockeyLoyal->errors) : ?>
					<div id="profile" class="cf">
						<div class="profile-left">
							<img src="<?php echo $HockeyLoyal->profile_picture_url ?>" alt="<?php echo $HockeyLoyal->profile_picture_alt ?>">
							<?php if($_GET['message'] == 'edit-success') : ?><div class="message">If you updated your profile image, it may take a while for it to update in our system.</div><?php endif; ?>
						</div>
						<div class="profile-right">
							<div class="profile-heading">
								<span class="profile-label">Screen Name: </span>
								<?php echo $HockeyLoyal->profile_screen_name ?>
							</div>
							
							<?php if($HockeyLoyal->is_publicly_visible('i_am_a')) : ?>
								<div class="profile-heading">
									<span class="profile-label">I am a: </span>
									<?php echo $HockeyLoyal->profile_fan_type($HockeyLoyal->profile_fan_type) ?>
								</div>
							<?php endif; ?>
							
							<?php if($HockeyLoyal->is_publicly_visible('street_address')) : ?>
								<div class="profile-heading">
									<span class="profile-label">Street Address: </span>
									<?php echo $HockeyLoyal->profile_street_address ?>
								</div>
							<?php endif; ?>
							<?php if($HockeyLoyal->is_publicly_visible('city')) : ?>
								<div class="profile-heading">
									<span class="profile-label">City: </span>
									<?php echo $HockeyLoyal->profile_city ?>
								</div>
							<?php endif; ?>
							<?php if($HockeyLoyal->is_publicly_visible('state_province')) : ?>
								<div class="profile-heading">
									<span class="profile-label">State/Province: </span>
									<?php echo $HockeyLoyal->profile_state ?>
								</div>
							<?php endif; ?>
							<?php if($HockeyLoyal->is_publicly_visible('postal_code')) : ?>
								<div class="profile-heading">
									<span class="profile-label">Postal Code: </span>
									<?php echo $HockeyLoyal->profile_postal_code ?>
								</div>
							<?php endif; ?>
							<?php if($HockeyLoyal->is_publicly_visible('country')) : ?>
								<div class="profile-heading">
									<span class="profile-label">Country: </span>
									<?php echo $HockeyLoyal->profile_country_longname ?>
								</div>
							<?php endif; ?>
							
							<?php if($HockeyLoyal->is_publicly_visible('age')) : ?>
								<div class="profile-heading">
									<span class="profile-label">Age: </span>
									<?php echo $HockeyLoyal->profile_age ?>
								</div>
							<?php endif; ?>
							<?php if($HockeyLoyal->is_publicly_visible('gender')) : ?>
								<div class="profile-heading">
									<span class="profile-label">Gender: </span>
									<?php echo $HockeyLoyal->profile_gender ?>
								</div>
							<?php endif; ?>
							
							<?php if($HockeyLoyal->is_publicly_visible('favorite_team')) : ?>
								<div class="profile-heading">
									<span class="profile-label">Favorite Team: </span>
									<?php echo $HockeyLoyal->profile_favorite_team ?>
								</div>
							<?php endif; ?>
							<?php if($HockeyLoyal->is_publicly_visible('hockeyloyal_is')) : ?>
								<div class="profile-heading">
									<span class="profile-label">HOCKEYLOYAL is: </span><br>
									<?php echo apply_filters('wp_autop', $HockeyLoyal->profile_hockeyloyal_is) ?>
								</div>
							<?php endif; ?>
							
							<?php if(is_user_logged_in() && !$HockeyLoyal->is_viewing_own_profile) : ?>
								<hr>
								<div class="profile-heading">
									<span class="profile-label">Contact User: </span>
									<?php echo do_shortcode('[contact-form-7 id="84" title="User to User Contact Form"]'); ?>
								</div>
							<?php endif; ?>
						</div>
					</div>
				<?php elseif($HockeyLoyal->errors) : ?>
					<?php $HockeyLoyal->errors() ?>
				<?php endif;  ?>
			</div>
		</div>
		<div class="content-right cf">
			<?php get_sidebar() ?>
		</div>
	</div>
</div>
<?php get_footer(); ?>