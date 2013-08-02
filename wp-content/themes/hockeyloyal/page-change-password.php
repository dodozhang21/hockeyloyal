<?php
	//Is the user logged in
	if(is_user_logged_in()) {
		//Is the user submitting the change password form?
		if($_POST['change-password']) {
			//Is the nonce set?
			if(wp_verify_nonce( $_POST['_wpnonce'], 'loyal_change_password' )) {
				//Filter everything
				foreach($_POST as $key => $topurify) {
					$_POST[$key] = $HockeyLoyal->purifier->purify($topurify);
				}
				
				//Do the passwords match?
				if($_POST['new-password'] <> $_POST['new-password-again']) {
					$HockeyLoyal->errors[] = 'Passwords didn\'t match';
				} else {
					//Load the current user
					wp_get_current_user();
					$id = $current_user->ID;
					
					//Prepeare and execute the statement
					global $wpdb;
					$mdfived = md5($_POST['new-password']);
					$wpdb->query(
						$wpdb->prepare(
							"
							UPDATE $wpdb->users
							SET user_pass = %s
							WHERE ID = %s
							",
							$mdfived,
							$id
						)
					);
					
					//Alert the user of the change
					$HockeyLoyal->messages[] = 'Your password has been changed.';
					
				}
				
			}
			else {
				//If not, error
				$HockeyLoyal->errors[] = 'Please use the change password form to change your password';
			}
		}
	} else {
		$HockeyLoyal->errors[] = 'You must be logged in to view this page';
	}
?>
<?php get_header(); ?>
<div id="content">
	<div class="container cf">
		<div class="content-left cf">
			<div class="content-left-container cf">
					<?php hockey_profile_nav() ?>
					<h1>Change Password</h1>
					<?php if(!$HockeyLoyal->messages && !$HockeyLoyal->errors) : ?>
						<?php echo do_shortcode('[change_password_form]'); ?>
					<?php elseif($HockeyLoyal->errors) : ?>
						<?php $HockeyLoyal->errors() ?>
					<?php elseif($HockeyLoyal->messages) : ?>
						<?php $HockeyLoyal->messages() ?>
					<?php endif; ?>
			</div>
		</div>
		<div class="content-right cf">
			<?php get_sidebar() ?>
		</div>
	</div>
</div>
<?php get_footer(); ?>