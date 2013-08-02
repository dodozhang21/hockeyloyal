<?php
	//print_r($_POST);
	if(is_user_logged_in() && $_POST['edit-profile'] && wp_verify_nonce($_POST['_wpnonce'],'fan-edit-profile')) {
		//Update the user to create fields
		wp_update_user( array ('ID' => $HockeyLoyal->current_user_id) );
		
		$purified = array();
		foreach($_POST as $key => $topurify) {
			//Bypass for the fan_type
			if($key == 'fan-type') {
				$purified[$key] = serialize($_POST['fan-type']);
			}
			elseif($key == 'publicly_visible') {
				$purified[$key] = serialize($_POST['publicly_visible']);
			}
			else {
				$purified[$key] = $HockeyLoyal->purifier->purify($topurify);
			}
		}
		
		//Basic fields
		$id = 'user_'.$HockeyLoyal->current_user_id;
		$simpleid = $HockeyLoyal->current_user_id;
		
		$age = $purified['age'];
		$HockeyLoyal->handleusermeta('age', $age, $simpleid);
		
		$gender = $purified['gender'];
		$HockeyLoyal->handleusermeta('gender', $gender, $simpleid);
		
		$city = $purified['city'];
		$HockeyLoyal->handleusermeta('city', $city, $simpleid);
		
		$state = $purified['state'];
		$HockeyLoyal->handleusermeta('province/state', $state, $simpleid);

		$team = $purified['favorite-team'];
		$HockeyLoyal->handleusermeta('favorite_team', $team, $simpleid);
		
		$fan_type = $purified['fan-type'];
		$HockeyLoyal->handleusermeta('fan_type', (string) $fan_type, $simpleid);
		
		$team_played_for = $purified['team_played_for'];
		$HockeyLoyal->handleusermeta('team_played_for', $team_played_for, $simpleid);
		
		$organization_name = $purified['organization_name'];
		$HockeyLoyal->handleusermeta('organization_name', $organization_name, $simpleid);
		
		$hockeyloyal_is = $purified['hockeyloyal-is'];
		$HockeyLoyal->handleusermeta('hockey_loyal_is', $hockeyloyal_is, $simpleid);
		
		$street_address = $purified['street_address'];
		$HockeyLoyal->handleusermeta('street_address', $street_address, $simpleid);
		
		$postal_code = $purified['postal_code'];
		$HockeyLoyal->handleusermeta('postal_code', $postal_code, $simpleid);
		
		$country = $purified['country'];
		$HockeyLoyal->handleusermeta('country', $country, $simpleid);
		
		//All publicly displayable information.
		$publicly_visible = $purified['publicly_visible'];
		$HockeyLoyal->handleusermeta('publicly_visible', (string) $publicly_visible, $simpleid);
		
		//Files
		if($_FILES['profile_pic']['error'] == 0) {
			
			$filecheck = basename($_FILES['profile_pic']['name']);
			$ext = strtolower(substr($filecheck, strrpos($filecheck, '.') + 1));
			
			//If it isn't a jpeg, error. If the file name isn't *.jpg, error.
			if( ($_FILES['profile_pic']['type'] != 'image/jpeg') || $ext != 'jpg') {
				$HockeyLoyal->errors[] = 'Image is not a JPG';
			}
			
			//If it really isn't an image, error hardcore.
			$imageData = @getimagesize($_FILES['profile_pic']['tmp_name']);
			if($imageData === FALSE || !($imageData[2] == IMAGETYPE_JPEG)) {
			  $HockeyLoyal->errors[] = 'Please upload an actual JPG';
			}
			
			/* //If the image isn't the right size (pixel size), error
			if( ($imageData[0] != 200) || ($imageData[1] != 200) ) {
				$HockeyLoyal->errors[] = 'Profile pictures most be 200px x 200px';
			} */
			
			//If the image isn't the right size (pixel size), error
			if( ($imageData[0] < 200) || ($imageData[1] < 200) ) {
				$HockeyLoyal->errors[] = 'Profile pictures most be at least 200px x 200px';
			}
			
			//If is too large (file size) (1 MB), error.
			if($_FILES['profile_pic']['size'] >= 1048576) {
				$HockeyLoyal->errors[] = 'Images must be smaller than 1 MB, please compress it.';
			}
			
			//Rename the file.
			$_FILES['profile_pic']['name'] = $id.'.jpg';
			
			//Upload if no errors
			if($HockeyLoyal->errors) {
				//There's errors, do nothing.
			}
			else {
				//If there is an old image, delete it, then remove it from the custom field.
				if($HockeyLoyal->has_profile_pic) {
					wp_delete_attachment($HockeyLoyal->profile_pic_id, true);
					$HockeyLoyal->handleusermeta('profile_picture', '', $simpleid);
				}
				
				//Upload the file using MediaUpload
				require_once($HockeyLoyal->plugin_path.'MediaUpload.php');
				$tmp = new MediaUpload();
				$attachment = $tmp->saveUpload('profile_pic', $HockeyLoyal->current_user_id);
				
				//Save the profile pic to the custom field
				$HockeyLoyal->handleusermeta('profile_picture', $attachment['attachment_id'], $simpleid);
			}
		}
		
		//Redirect them back to the profile page.
		if(!$HockeyLoyal->errors) {
			wp_redirect(home_url().'/profile?message=edit-success');
			exit;
		}
	}
?>
<?php get_header(); ?>
<div id="content">
	<div class="container cf">
		<div class="content-left cf">
			<div class="content-left-container cf">
				<?php if(is_user_logged_in() && !$error) : ?>
					<?php hockey_profile_nav() ?>
					<div id="profile" class="cf">
						<?php if(!$HockeyLoyal->errors) : ?>
						<form name="edit-profile" method="post" action="<?php echo home_url() ?>/profile/edit-profile" enctype="multipart/form-data">
							<div class="profile-left">
								<img src="<?php echo $HockeyLoyal->profile_picture_url ?>" alt="<?php echo $HockeyLoyal->profile_picture_alt ?>">
								<p class="callout">Profile pictures will be resized to 200px on one side then cropped to a 200px square. Pictures must be JPGs.</p>
								<input type="file" name="profile_pic" id="profile-pic">
							</div>
							<div class="profile-right">
								<input type="hidden" name="edit-profile" value="1">
								
								<div class="profile-heading">
									<span class="profile-label">Screen Name: </span>
									<?php echo $HockeyLoyal->profile_screen_name ?>
								</div>
								
								<div class="profile-heading">
									<?php
										$fan_types = $HockeyLoyal->like_acf_get_field_object('fan_type');
										$fan_types = $fan_types['choices'];
										$current = $HockeyLoyal->profile_fan_type;
									?>
									<span class="profile-label">I am a: </span>
									<ul class="fantype_listing">
										<?php foreach($fan_types as $fan_type => $fan_type_longname) :
											if(in_array($fan_type_longname, $current)) { $checked = true; } else { $checked = false; }
										?>
											<li>
												<input type="checkbox" name="fan-type[]" id="fan-type-<?php echo $fan_type ?>" <?php echo ($checked) ? 'checked="checked"':'' ?> value="<?php echo $fan_type ?>" class="<?php
													if($fan_type == 'player' || $fan_type == 'organization') {
														echo 'has-hidden-data';
													}
												?>">
												<label for="fan-type-<?php echo $fan_type ?>"><?php echo $fan_type_longname ?></label>
												<?php if($fan_type == 'player') : ?>
													<div class="hidden-until-checked" data-invoker="fan-type-<?php echo $fan_type ?>">
														<label for="team_played_for">Team Played For:</label> <input type="text" name="team_played_for" id="team_played_for" value="<?php echo $HockeyLoyal->profile_team_played_for ?>">
													</div>
												<?php elseif($fan_type == 'organization') : ?>
													<div class="hidden-until-checked" data-invoker="fan-type-<?php echo $fan_type ?>">
														<label for="organization_name">Organization Name:</label> <input type="text" name="organization_name" id="organization_name" value="<?php echo $HockeyLoyal->profile_organization_name ?>">
													</div>
												<?php endif; ?>
											</li>
										<?php endforeach; ?>
									</ul>
								</div>
								
								<div class="profile-heading">
									<label for="street_address"><span class="profile-label">Street Address: </span></label>
									<input type="text" name="street_address" id="street_address" value="<?php echo $HockeyLoyal->profile_street_address ?>">
									<?php $HockeyLoyal->display_checkbox('street_address'); ?>
								</div>
								<div class="profile-heading">
									<label for="city"><span class="profile-label">City: </span></label>
									<input type="text" name="city" id="city" value="<?php echo $HockeyLoyal->profile_city ?>">
								</div>
								<div class="profile-heading">
									<?php
										$states = $HockeyLoyal->like_acf_get_field_object('province/state');
										$states = $states['choices'];
										$current_state = $HockeyLoyal->profile_state;
										$current_state_longname = $states[$current_state];
										$selected = array($current_state => $current_state_longname);
										/* Move current state to front by removing it then adding it to front */
										unset($states[$current_state]);
										$states = array_merge($selected, $states);
									?>
									<span class="profile-label">State/Province: </span>
										<select name="state" id="state">
											<?php foreach($states as $state => $state_longname) : ?>
												<option value="<?php echo $state ?>"><?php echo $state_longname ?></option>
											<?php endforeach; ?>
										</select>
								</div>
								<div class="profile-heading">
									<label for="postal_code"><span class="profile-label">Postal Code: </span></label>
									<input type="text" name="postal_code" id="postal_code" value="<?php echo $HockeyLoyal->profile_postal_code ?>">
									<?php $HockeyLoyal->display_checkbox('postal_code'); ?>
								</div>
								<div class="profile-heading">
									<?php
										$country = $HockeyLoyal->like_acf_get_field_object('country');
										$country = $country['choices'];
										$current_country = $HockeyLoyal->profile_country;
										$current_country_longname = $country[$current_country];
										$selected = array($current_country => $current_country_longname);
										/* Move current state to front by removing it then adding it to front */
										unset($country[$current_country]);
										$country = array_merge($selected, $country);
									?>
									<span class="profile-label">Country: </span>
										<select name="country" id="country">
											<?php foreach($country as $selected_country => $country_longname) : ?>
												<option value="<?php echo $selected_country ?>"><?php echo $country_longname ?></option>
											<?php endforeach; ?>
										</select>
										<?php $HockeyLoyal->display_checkbox('country'); ?>
								</div>
								
								<div class="profile-heading">
									<label for="age"><span class="profile-label number">Age: </span></label>
									<input type="text" name="age" id="age" value="<?php echo $HockeyLoyal->profile_age ?>">
									<?php $HockeyLoyal->display_checkbox('age'); ?>
								</div>
								<div class="profile-heading">
									<?php
										$genders = $HockeyLoyal->like_acf_get_field_object('gender');
										$genders = $genders['choices'];
										$current_gender = $HockeyLoyal->profile_gender;
										$current_gender_longname = $genders[$current_gender];
										$selected = array($current_gender => $current_gender_longname);
										/* Move current state to front by removing it then adding it to front */
										unset($genders[$current_gender]);
										$genders = array_merge($selected, $genders);
									?>
									<span class="profile-label">Gender: </span>
									<select name="gender" id="gender">
										<?php foreach($genders as $gender => $gender_longname) : ?>
											<option value="<?php echo $gender ?>"><?php echo $gender_longname ?></option>
										<?php endforeach; ?>
									</select>
									<?php $HockeyLoyal->display_checkbox('gender'); ?>
								</div>
								
								<div class="profile-heading">
									<label for="favorite-team"><span class="profile-label">Favorite Team: </span></label>
									<input type="text" name="favorite-team" id="favorite-team" value="<?php echo $HockeyLoyal->profile_favorite_team ?>">
								</div>
								<div class="profile-heading">
									<span class="profile-label">HOCKEYLOYAL is: </span>
								</div>
								<textarea name="hockeyloyal-is" id="hockeyloyal-is"><?php echo $HockeyLoyal->profile_hockeyloyal_is ?></textarea>
								<?php wp_nonce_field('fan-edit-profile') ?>
								<p><input type="submit" value="Save Profile" class="read-more"></p>	
							</div>
						</form>
						<?php else : ?>
							<?php $HockeyLoyal->errors() ?>
						<?php endif; ?>
					</div>
				<?php else :  ?>
					<div id="profile" class="cf">
						<p>You must be logged in to view this page. Perhaps you should <a href="<?php echo home_url() ?>">browse for other users?</a></p>
					</div>
				<?php endif; ?>
			</div>
		</div>
		<div class="content-right cf">
			<?php get_sidebar() ?>
		</div>
	</div>
</div>
<?php get_footer(); ?>