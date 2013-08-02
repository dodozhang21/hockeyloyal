<?php
/*
Plugin Name: HOCKEYLOYAL Core
Plugin URI: 
Description: Core functionality for HOCKEYLOYAL
Author: Webspec Design
Version: 1
Author URI: http://www.webspecdesign.com/
*/

error_reporting(0);

class HockeyLoyal {
	public $is_viewing_own_profile;
	public $plugin_path;
	public $plugin_url;
	public $purifier;
	public $MediaUpload;
	
	public $fields;
	public $all_field_names = array(
		'age',
		'gender',
		'city',
		'province/state',
		'favorite_team',
		'fan_type',
		'team_played_for',
		'organization_name',
		'hockey_loyal_is',
		'street_address',
		'postal_code',
		'country',
		'publicly_visible',
		'profile_picture'
	);
	
	public $current_user_id;
	
	public $usermeta;
	
	public $publicly_visible;
	
	public $has_profile_pic;
	public $profile_pic_id;
	public $profile_picture_url;
	public $profile_picture_alt;
	public $profile_screen_name;
	public $profile_age;
	public $profile_street_address;
	public $profile_city;
	public $profile_state;
	public $profile_postal_code;
	
	public $profile_country;
	public $profile_country_longname;
	
	public $profile_favorite_team;
	public $profile_fan_type;
	public $profile_organization_name;
	public $profile_team_played_for;
	public $profile_hockeyloyal_is;
	public $conditionals;
	
	public $errors;
	public $messages;
	
	//Special user to not show up around the site. By ID
	public $special_users = array(
		//Admin
		1
	);
	
	function __construct() {
		
		$this->plugin_path = plugin_dir_path(__FILE__);
		$this->plugin_url = plugin_dir_url(__FILE__);
		
		$this->errors = false;
		
		require_once($this->plugin_path.'HTMLPurifier.standalone.php');
		$config = HTMLPurifier_Config::createDefault();
		$config->set('HTML','Allowed',''); //No HTML allowed
		$this->purifier = new HTMLPurifier($config);
		
		register_activation_hook( __FILE__, array($this, 'activate'));
		
		add_action('template_redirect', array($this, 'profile'));
		
		//AJAX
		//Users can filter grids regardless if they're logged in.
		add_action('wp_ajax_filter_grid', array($this,'filter_grid'));
		add_action('wp_ajax_nopriv_filter_grid', array($this,'filter_grid'));
		add_action( 'init', array($this, 'my_script_enqueuer') );
		
		//User to user
		add_action( 'wpcf7_before_send_mail', array($this, 'user_to_user'), 5 );
		//User to user shortcodes
		add_shortcode('hockeyloyal_user_to',array($this, 'hockeyloyal_user_to'));
		add_shortcode('hockeyloyal_user_from',array($this, 'hockeyloyal_user_from'));
		
		//Changes the email addresses.
		add_filter('wp_mail_from', array($this, 'wp_mail_from'));
		add_filter('wp_mail_from_name', array($this, 'wp_mail_from_name'));
		
		add_action('edit_user_profile', array($this, 'edit_user_profile'));
		
		add_action('init',array($this,'handle_admin_user_save'));
		
		$this->make_fields();
	}
	
	public function wp_mail_from() {
		return 'users@hockeyloyal.com';
	}
	public function wp_mail_from_name() {
		return 'HOCKEYLOYAL';
	}
	
	private function make_fields() {
		$fields = array(
			'gender' => array(
				'choices' => array(
					'Male' => 'Male',
					'Female' => 'Female'
				),
				'label' => 'Gender',
				'name' => 'gender'
			),
			'province/state' => array(
				'choices' => array(
					'AL'=>'Alabama',  
					'AK'=>'Alaska',  
					'AZ'=>'Arizona',  
					'AR'=>'Arkansas',  
					'CA'=>'California',  
					'CO'=>'Colorado',  
					'CT'=>'Connecticut',  
					'DE'=>'Delaware',  
					'DC'=>'District Of Columbia',  
					'FL'=>'Florida',  
					'GA'=>'Georgia',  
					'HI'=>'Hawaii',  
					'ID'=>'Idaho',  
					'IL'=>'Illinois',  
					'IN'=>'Indiana',  
					'IA'=>'Iowa',  
					'KS'=>'Kansas',  
					'KY'=>'Kentucky',  
					'LA'=>'Louisiana',  
					'ME'=>'Maine',  
					'MD'=>'Maryland',  
					'MA'=>'Massachusetts',  
					'MI'=>'Michigan',  
					'MN'=>'Minnesota',  
					'MS'=>'Mississippi',  
					'MO'=>'Missouri',  
					'MT'=>'Montana',
					'NE'=>'Nebraska',
					'NV'=>'Nevada',
					'NH'=>'New Hampshire',
					'NJ'=>'New Jersey',
					'NM'=>'New Mexico',
					'NY'=>'New York',
					'NC'=>'North Carolina',
					'ND'=>'North Dakota',
					'OH'=>'Ohio',  
					'OK'=>'Oklahoma',  
					'OR'=>'Oregon',  
					'PA'=>'Pennsylvania',  
					'RI'=>'Rhode Island',  
					'SC'=>'South Carolina',  
					'SD'=>'South Dakota',
					'TN'=>'Tennessee',  
					'TX'=>'Texas',  
					'UT'=>'Utah',  
					'VT'=>'Vermont',  
					'VA'=>'Virginia',  
					'WA'=>'Washington',  
					'WV'=>'West Virginia',  
					'WI'=>'Wisconsin',  
					'WY'=>'Wyoming',
					'BC'=>'British Columbia', 
					'ON'=>'Ontario', 
					'NL'=>'Newfoundland and Labrador', 
					'NS'=>'Nova Scotia', 
					'PE'=>'Prince Edward Island', 
					'NB'=>'New Brunswick', 
					'QC'=>'Quebec', 
					'MB'=>'Manitoba', 
					'SK'=>'Saskatchewan', 
					'AB'=>'Alberta', 
					'NT'=>'Northwest Territories', 
					'NU'=>'Nunavut',
					'YT'=>'Yukon Territory'
				),
				'label' => 'Province/State',
				'name' => 'province/state'
			),
			'fan_type' => array(
				'choices' => array(
					'player' => 'Player',
					'fan' => 'Fan',
					'parent' => 'Parent',
					'organization' => 'Organization'
				),
				'label' => 'Fan Type',
				'name' => 'fan_type'
			),
			'country' => array(
				'choices' => array(
					'usa' => 'United States of America',
					'canada' => 'Canada'
				),
				'label' => 'Country',
				'name' => 'country'
			)
		);
		$this->fields = $fields;
	}
	
	public function activate() {
		add_role('hockeyloyal-fan', 'HOCKEYLOYAL Fan', NULL);
	}
	
	public function profile() {
		global $wpdb;
		
		$this->current_user_id = wp_get_current_user()->ID;
		
		if(is_page('profile') || is_page('edit-profile')) {
			$tmp = wp_get_current_user();
			if(is_user_logged_in() && (!$_GET['fan'] || $tmp->user_nicename == $_GET['fan'])) {
				$this->is_viewing_own_profile = true;
				$user = wp_get_current_user();
				$user = $user->data;
			}
			else {
				$this->is_viewing_own_profile = false;
				$tosanitize = $_GET['fan'];
				$sanitized = $this->purifier->purify($tosanitize);
				$query = "SELECT ID,user_nicename FROM `$wpdb->users` WHERE user_nicename = '$sanitized'";
				$user = $wpdb->get_row($query, OBJECT);
				if(count($user) < 1 || in_array($user->ID, $this->special_users)) { 
					$this->errors[] = 'That is not a valid user.'; 
				}
			}
			
			if(!$this->errors) {
				$this->usermeta = get_user_meta($user->ID);
				
				$user_id = 'user_'.$user->ID;
				
				//Load publicly visible
				$this->load_publicly_visible($this->usermeta);
				
				$this->profile_screen_name = $user->user_nicename;
				$this->profile_age = $this->like_acf_get_field('age', $this->usermeta);
				$this->profile_city = $this->like_acf_get_field('city', $this->usermeta);
				$this->profile_state = $this->like_acf_get_field('province/state', $this->usermeta);
				$this->profile_favorite_team = $this->like_acf_get_field('favorite_team', $this->usermeta);
				$this->profile_street_address = $this->like_acf_get_field('street_address', $this->usermeta);
				$this->profile_postal_code = $this->like_acf_get_field('postal_code', $this->usermeta);
				
				$this->profile_country = $this->like_acf_get_field('country', $this->usermeta);
				if($this->profile_country) {
					$this->profile_country_longname = $this->fields['country']['choices'][$this->profile_country];
				}
				
				$fan_types = $this->like_acf_get_field('fan_type', $this->usermeta);
				$fan_types = $this->return_like_acf($fan_types, 'multi');
				if($fan_types) {
					foreach($fan_types as $key => $fan_type) {
						$this->profile_fan_type[$key] = ucwords($fan_type);
					}
				}
				$this->profile_team_played_for = $this->like_acf_get_field('team_played_for', $this->usermeta);
				$this->profile_organization_name = $this->like_acf_get_field('organization_name', $this->usermeta);
				
				$this->profile_hockeyloyal_is = $this->like_acf_get_field('hockey_loyal_is', $this->usermeta);
				$this->profile_gender = $this->like_acf_get_field('gender', $this->usermeta);
				
				$this->has_profile_pic = $this->like_acf_get_field('profile_picture', $this->usermeta);
				if($this->has_profile_pic) {
					$this->has_profile_pic = $this->return_like_acf($this->has_profile_pic,'image');
					$profile_pic = $this->has_profile_pic;
					$this->profile_pic_id = $profile_pic['id'];
					$this->profile_picture_url = $profile_pic['sizes']['hockeyloyal-profile'];
				} else {
					$this->profile_picture_url = get_bloginfo('template_directory').'/images/profile-placeholder.jpg';
				}
				$this->profile_picture_alt = $this->profile_screen_name;
			}
		}
		
		if(is_page('change-password') && is_user_logged_in()) {
			$this->is_viewing_own_profile = true;
		}
	}
	
	public function profile_fan_type($fan_types) {
		if($fan_types) {
			echo '<ul class="fantype_listing">';
				foreach($fan_types as $fan_type) {
					echo '<li>';
						echo $fan_type;
						if($fan_type == 'Player') {
							if($this->profile_team_played_for) {
								echo '<br><span class="conditional-label bold">Team Played For: </span> '.$this->profile_team_played_for;
							}
						}
						if($fan_type == 'Organization') {
							if($this->profile_organization_name) {
								echo '<br><span class="conditional-label bold">Organization Name: </span> '.$this->profile_organization_name;
							}
						}
					echo '</li>';
				}
			echo '</ul>';
		}
	}
	
	public function like_acf_get_field($field_name, $usermeta) {
		if(array_key_exists($field_name, $usermeta)) {
			$output = $usermeta[$field_name];
			$output = $output[0];
		} else {
			$output = false;
		}
		
		return $output;
	}
	
	public function like_acf_get_field_object($field_name) {
		return $this->fields[$field_name];
	}
	
	public function return_like_acf($input, $field_type) {
		if($input) {
			if($field_type == 'image') {
				//Taken from ACF
				$attachment = get_post( $input );
					
				// create array to hold value data
				$value = array(
					'id' => $attachment->ID,
					'alt' => get_post_meta($attachment->ID, '_wp_attachment_image_alt', true),
					'title' => $attachment->post_title,
					'caption' => $attachment->post_excerpt,
					'description' => $attachment->post_content,
					'url' => wp_get_attachment_url( $attachment->ID ),
					'sizes' => array(),
				);
				
				// find all image sizes
				$image_sizes = get_intermediate_image_sizes();
				
				if( $image_sizes )
				{
					foreach( $image_sizes as $image_size )
					{
						// find src
						$src = wp_get_attachment_image_src( $attachment->ID, $image_size );
						
						// add src
						$value['sizes'][$image_size] = $src[0];
					}
					// foreach( $image_sizes as $image_size )
				}
			} elseif($field_type == 'multi') {
				$i = 0;
				//Use i for a sanity check
				while(gettype($input) == 'string' && $i < 5) {
					$input = unserialize($input);
					$i++;
				}
				$value = $input;
			}
		
			return $value;
			
		} else {
			return false;
		}
	}
	
	//Adds a checkbox for profile elements if they should be visible
	public function display_checkbox($field_name) {
		$checked = '';
		if(in_array($field_name, $this->publicly_visible)) {
			$checked = 'checked="checked"';
		}
		$output = '<div class="visibility-checkbox">';
			$output .= '<input type="checkbox" name="publicly_visible[]" id="publicly_visible_'.$field_name.'" value="'.$field_name.'" '.$checked.'>';
			$output .= '<label for="publicly_visible_'.$field_name.'">Display to the Public</label>';
		$output .= '</div>';
		
		echo $output;
	}
	//Loads and defaults the publicly visible array
	public function load_publicly_visible($usermeta) {
		$publicly_visible = $this->like_acf_get_field('publicly_visible', $usermeta);
		$publicly_visible = $this->return_like_acf($publicly_visible, 'multi');
		
		if(gettype($publicly_visible) != 'array') {
			$publicly_visible = array();
		}
		
		//Load the defaults
		$defaults = array(
			'screen_name',
			'i_am_a',
			'state_province',
			'favorite_team',
			'hockeyloyal_is'
		);
		
		//Merge the two
		$this->publicly_visible = array_merge($defaults, $publicly_visible);
	}
	//Asks if something is visible to the public
	public function is_publicly_visible($field_name) {
		$publicly_visible = $this->publicly_visible;
		if(in_array($field_name, $publicly_visible)) {
			return true;
		}
		else {
			return false;
		}
	}
	
	//Filter the front page grid
	public function filter_grid() {
		if ( !wp_verify_nonce( $_REQUEST['nonce'], 'filter_grid_nonce')) {
			exit('Please filter the grid from the front page.');
		}
		
		//If there isn't a state or fan_type, error
		if(!$_REQUEST['state'] && !$_REQUEST['fan_type']) {
			$result['type'] = 'error';
		} else {
			$html;
			
			//Else go on
			$result['type'] = 'success';
			
			//Sanitize results
			$state = $this->purifier->purify($_REQUEST['state']);
			$result['state'] = $state;
			$fan_type = $this->purifier->purify($_REQUEST['fan_type']);
			$result['fan_type'] = $fan_type;
			
			$queries;
			
			if($state != 'CLEAR') {
				$queries[0] = array(
					'key' => 'province/state',
					'value' => $state
				);
			}
			if($fan_type != 'CLEAR') {
				$queries[1] = array(
					'key' => 'fan_type',
					'value' => $fan_type,
					'compare' => 'LIKE'
				);
			}
			
			//Get the users
			$returned_users = new WP_User_Query(array(
				'meta_query' => array(
					$queries[0],
					$queries[1]
				),
				'number' => 64,
				'orderby' => 'user_registered',
				'order' => 'DESC',
				'role' => 'hockeyloyal-fan',
				'fields' => 'all_with_meta'
			));
			//Return them
			$returned_users = $returned_users->get_results();
			
			if($returned_users) {
				foreach($returned_users as $grid_user) {
					$usermeta = get_user_meta($grid_user->ID);
					$profile_pic = $this->return_like_acf($this->like_acf_get_field('profile_picture',$usermeta), 'image');
					if($profile_pic) {
						$profile_pic = '<img src="'.$profile_pic['sizes']['hockeyloyal-grid'].'" alt="'.$grid_user->user_nicename.'">';
					}
					else {
						$profile_pic = '<img src="'.get_bloginfo('template_directory').'/images/profile-placeholder.jpg'.'" alt="'.$grid_user->user_nicename.'">';
					}
					$html .= '<a href="'.home_url().'/profile/?fan='.$grid_user->user_nicename.'">';
						$html .= $profile_pic;
					$html .=  '</a>';
				}
			}
			else {
				$html .= '<p>Your search did not produce any fans. Perhaps you should try a different search?</p>';
			}
			
			$result['html'] = $html;

			if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
				$result = json_encode($result);
				echo $result;
			}
			else {
				header("Location: ".$_SERVER["HTTP_REFERER"]);
				$result = json_encode($result);
				echo $result;
			}
		}
		
		die();
	}
	
	public function user_to_user($cf7) {
		global $wpdb;
		//Only the user to user
		if ($cf7->id == 84) {
			//Remove filters
			remove_filter('wp_mail_from', array($this, 'wp_mail_from'));
			remove_filter('wp_mail_from_name', array($this, 'wp_mail_from_name'));
			
			//Is the user logged in?
			if(is_user_logged_in()) {
				//Purify all inputs
				$tosanitize = $cf7->posted_data;
				foreach($tosanitize as $key => $value) {
					$cf7->posted_data[$key] = $this->purifier->purify($value);
				}
				
				//Change the to: Email address
				$usernameto = $cf7->posted_data['userto'];
				$query = "SELECT ID FROM `$wpdb->users` WHERE user_nicename = '$usernameto'";
				$userdatato = $wpdb->get_row($query, OBJECT);
				$userdatato = get_userdata($userdatato->ID);
				$emailto = $userdatato->user_email;
				
				$usernamefrom = $cf7->posted_data['userfrom'];
				
				$cf7->mail['recipient'] = "$usernameto <$emailto>";
				$cf7->mail['sender'] = "$usernamefrom (HOCKEYLOYAL) <users@hockeyloyal.com>";
				
				$cf7->mail['body'] .= "\r\n\r\nThis message is from the HockeyLoyal user: $usernamefrom \r\nTo contact them, visit their profile by going to ".$this->profile_url($usernamefrom, 'return');
				$cf7->mail['body'] .= "\r\nDo not reply to this message. Please visit the user's profile to contact them.";
			}
			//Else don't send the mail
			else {
				$cf7->skip_mail = true;
			}
		}
		//$cf7->skip_mail = true;
	}
	public function hockeyloyal_user_to() {
		if(is_user_logged_in()) {
			return $_GET['fan'];
		} else {
			return false;
		}
	}
	public function hockeyloyal_user_from() {
		if(is_user_logged_in()) {
			$user = wp_get_current_user();
			return $user->user_nicename;
		} else {
			return false;
		}
	}
	public function profile_url($user, $method = NULL) {
		$output = home_url().'/profile/?fan='.$user;
		if($method == 'return') {
			return $output;
		}
	}
	
	public function handleusermeta($field_name, $update_value, $user_id) {
		//Update it. If it doesn't exist, create it
		if(!update_user_meta($user_id, $field_name, $update_value)) {
			add_user_meta($user_id, $field_name, $update_value, true);
		}
	}
	
	public function my_script_enqueuer() {
		if(!is_admin()) {
		   wp_register_script( 'filter_grid_script', $this->plugin_url.'js/hockey-loyal-ajax.js', array('jquery') );
		   wp_localize_script( 'filter_grid_script', 'myAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));        

		   wp_enqueue_script( 'filter_grid_script' );
		}
	}
	
	public function errors() { ?>
		<div class="errors">
			<p>The following errors have occurred:</p>
			<ul>
				<?php foreach($this->errors as $error) : ?>
					<li><?php echo $error ?></li>
				<?php endforeach; ?>
			</ul>
			<p><a href="<?php echo home_url() ?>">Please return to the home page.</a></p>
		</div>
	<?php } //end errors()
	
	public function messages() { ?>
		<div class="messages">
			<ul>
				<?php foreach($this->messages as $message) : ?>
					<li><?php echo $message ?></li>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php }
	
	public function edit_user_profile($user) {
		$hockeyfields = get_user_meta($user->data->ID);
		echo '<h3>HockeyLoyal Profile</h3>';
		echo '<input type="hidden" name="hockey-bypass-edit" value="1">';
			foreach($hockeyfields as $name => $hockeyfield) {
				if(in_array($name, $this->all_field_names)) {
					echo '<label for="hl_'.$name.'">'.$name.':</label> <input type="text" value="'.$hockeyfield[0].'" name="hl_'.$name.'" id="hl_'.$name.'"><br>';
				}
			}
	}
	
	public function handle_admin_user_save() {
		if(current_user_can('delete_plugins')) {
			if($_POST['hockey-bypass-edit']) {
				foreach($_POST as $name => $posted) {
					if(strpos($name, 'hl_') === false) {
						continue;
					} else {
						$this->handleusermeta(substr($name, 3), $posted, $_POST['user_id']);
					}
				}
			}
		}
	}
}

$HockeyLoyal = new HockeyLoyal();
?>