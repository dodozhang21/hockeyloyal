<?php
/*
   Author: Todd Motto | @toddmotto
   URL: html5blank.com | @html5blank
   Custom functions, support, custom post types and more.
*/

/* =============================================================================
   External Modules/Files
   ========================================================================== */

	// Load any external files you have here

/* =============================================================================
   Theme Support
   ========================================================================== */

	if (!isset($content_width))
	    $content_width = 900;
	
	if (function_exists('add_theme_support')) {
	    
	    // Add Thumbnail Theme Support
	    add_theme_support('post-thumbnails');
	    add_image_size('large', 700, '', true); // Large Thumbnail
	    add_image_size('medium', 250, '', true); // Medium Thumbnail
	    add_image_size('small', 120, '', true); // Small Thumbnail
	    add_image_size('hockeyloyal-profile', 200, 200, true);
		add_image_size('hockeyloyal-grid', 55, 55, true);
	    
	    // Localisation Support
	    load_theme_textdomain('html5blank', get_template_directory() . '/languages');
	}

/* =============================================================================
   Functions
   ========================================================================== */

	// Load Custom Theme Scripts using Enqueue
	function html5blank_scripts()
	{
	    if (!is_admin()) {
	        wp_deregister_script('jquery'); // Deregister WordPress jQuery
	        wp_register_script('jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js', 'jquery', '1.8.2'); // Load Google CDN jQuery
	        wp_enqueue_script('jquery'); // Enqueue it!
			
			wp_register_script('jquery-ui-custom', get_bloginfo('template_directory').'/js/jquery-ui-1.9.0.custom.min.js', 'jquery', '1.9.0');
			wp_enqueue_script('jquery-ui-custom'); // Enqueue it!
			
			wp_register_script('filestyle', get_bloginfo('template_directory').'/js/filestyle.js', 'jquery');
			wp_enqueue_script('filestyle'); // Enqueue it!
	    }
	}
	
	// Loading Conditional Scripts
	function conditional_scripts()
	{
	}
	
	// jQuery Fallbacks load in the footer
	function add_jquery_fallback()
	{
		echo "<script>window.jQuery || document.write('<script src=\"".get_bloginfo('template_url')."/js/vendor/jquery-1.8.0.min.js\"><\/script>')</script>";
	}
	
	// Threaded Comments
	function enable_threaded_comments()
	{
	    if (!is_admin()) {
	        if (is_singular() AND comments_open() AND (get_option('thread_comments') == 1))
	            wp_enqueue_script('comment-reply');
	    }
	}
	
	// Theme Stylesheets using Enqueue
	function html5blank_styles()
	{
	    wp_register_style('html5blank', get_template_directory_uri() . '/style.css', array(), '1.0', 'all');
	    wp_enqueue_style('html5blank'); // Enqueue it!
	}
	
	// Register HTML5 Blank's Navigation
	function register_html5_menu()
	{
	    register_nav_menus(array( // Using array to specify more menus if needed
	        'header-menu' => __('Header Menu', 'html5blank'), // Main Navigation
	        'sidebar-menu' => __('Sidebar Menu', 'html5blank'), // Sidebar Navigation
	        'extra-menu' => __('Extra Menu', 'html5blank') // Extra Navigation if needed (duplicate as many as you need!)
	    ));
	}
	
	// Remove the <div> surrounding the dynamic navigation to cleanup markup
	function my_wp_nav_menu_args($args = '')
	{
	    $args['container'] = false;
	    return $args;
	}
	
	// Remove Injected classes, ID's and Page ID's from Navigation <li> items
	function my_css_attributes_filter($var)
	{
	    return is_array($var) ? array() : '';
	}
	
	// Remove invalid rel attribute values in the categorylist
	function remove_category_rel_from_category_list($thelist)
	{
	    return str_replace('rel="category tag"', 'rel="tag"', $thelist);
	}
	
	// Add page slug to body class, love this - Credit: Starkers Wordpress Theme
	function add_slug_to_body_class($classes)
	{
	    global $post;
	    if (is_home()) {
	        $key = array_search('blog', $classes);
	        if ($key > -1) {
	            unset($classes[$key]);
	        }
	        ;
	    } elseif (is_page()) {
	        $classes[] = sanitize_html_class($post->post_name);
	    } elseif (is_singular()) {
	        $classes[] = sanitize_html_class($post->post_name);
	    }
	    ;
	    
	    return $classes;
	}
	
	// If Dynamic Sidebar Exists
	if (function_exists('register_sidebar')) {
	    // Define Sidebar Widget Area 1
	    register_sidebar(array(
	        'name' => __('Widget Area 1', 'html5blank'),
	        'description' => __('Discription for this widget-area...', 'html5blank'),
	        'id' => 'widget-area-1',
	        'before_widget' => '<div id="%1$s" class="%2$s">',
	        'after_widget' => '</div>',
	        'before_title' => '<h3>',
	        'after_title' => '</h3>'
	    ));
	    
	    // Define Sidebar Widget Area 2
	    register_sidebar(array(
	        'name' => __('Widget Area 2', 'html5blank'),
	        'description' => __('Discription for this widget-area...', 'html5blank'),
	        'id' => 'widget-area-2',
	        'before_widget' => '<div id="%1$s" class="%2$s">',
	        'after_widget' => '</div>',
	        'before_title' => '<h3>',
	        'after_title' => '</h3>'
	    ));
	}
	
	// Remove wp_head() injected Recent Comment styles
	function my_remove_recent_comments_style()
	{
	    global $wp_widget_factory;
	    remove_action('wp_head', array(
	        $wp_widget_factory->widgets['WP_Widget_Recent_Comments'],
	        'recent_comments_style'
	    ));
	}
	
	// Pagination for paged posts, Page 1, Page 2, Page 3, with Next and Previous Links, No plugin
	function html5wp_pagination()
	{
	    global $wp_query;
	    $big = 999999999;
	    echo paginate_links(array(
	        'base' => str_replace($big, '%#%', get_pagenum_link($big)),
	        'format' => '?paged=%#%',
	        'current' => max(1, get_query_var('paged')),
	        'total' => $wp_query->max_num_pages
	    ));
	}
	
	// Custom Excerpts
	function html5wp_index($length) // Create 20 Word Callback for Index page Excerpts, call using html5wp_excerpt('html5wp_index');
	{
	    return 20;
	}
	function html5wp_custom_post($length) // Create 40 Word Callback for Custom Post Excerpts, call using html5wp_excerpt('html5wp_custom_post');
	{
	    return 40;
	}
	
	// Create the Custom Excerpts callback
	function html5wp_excerpt($length_callback = '', $more_callback = '')
	{
	    global $post;
	    if (function_exists($length_callback)) {
	        add_filter('excerpt_length', $length_callback);
	    }
	    if (function_exists($more_callback)) {
	        add_filter('excerpt_more', $more_callback);
	    }
	    $output = get_the_excerpt();
	    $output = apply_filters('wptexturize', $output);
	    $output = apply_filters('convert_chars', $output);
	    $output = '<p>' . $output . '</p>';
	    echo $output;
	}
	
	// Custom View Article link to Post
	function html5wp_view_article($more)
	{
	    return '... <a class="view-article" href="' . get_permalink($post->ID) . '">' . __('View Article', 'html5blank') . '</a>';
	}
	
	// Remove Admin bar
	function remove_admin_bar()
	{
		if(current_user_can( 'activate_plugins')) {
			return true;
		}
	    else {
			return false;
		}
	}
	
	// Remove 'text/css' from our enqueued stylesheet
	function html5_style_remove($tag)
	{
	    return preg_replace('~\s+type=["\'][^"\']++["\']~', '', $tag);
	}

/* =============================================================================
   Actions + Filters + ShortCodes
   ========================================================================== */

	// Add Actions
	add_action('init', 'html5blank_scripts'); // Add Custom Scripts
	add_action('wp_print_scripts', 'conditional_scripts'); // Add Conditional Page Scripts
	add_action('wp_footer', 'add_jquery_fallback'); // jQuery fallbacks loaded through footer
	add_action('get_header', 'enable_threaded_comments'); // Enable Threaded Comments
	add_action('wp_enqueue_scripts', 'html5blank_styles'); // Add Theme Stylesheet
	add_action('init', 'register_html5_menu'); // Add HTML5 Blank Menu
	add_action('widgets_init', 'my_remove_recent_comments_style'); // Remove inline Recent Comment Styles from wp_head()
	add_action('init', 'html5wp_pagination'); // Add our HTML5 Pagination
	
	// Remove Actions
	remove_action('wp_head', 'feed_links_extra', 3); // Display the links to the extra feeds such as category feeds
	remove_action('wp_head', 'feed_links', 2); // Display the links to the general feeds: Post and Comment Feed
	remove_action('wp_head', 'rsd_link'); // Display the link to the Really Simple Discovery service endpoint, EditURI link
	remove_action('wp_head', 'wlwmanifest_link'); // Display the link to the Windows Live Writer manifest file.
	remove_action('wp_head', 'index_rel_link'); // index link
	remove_action('wp_head', 'parent_post_rel_link', 10, 0); // prev link
	remove_action('wp_head', 'start_post_rel_link', 10, 0); // start link
	remove_action('wp_head', 'adjacent_posts_rel_link', 10, 0); // Display relational links for the posts adjacent to the current post.
	remove_action('wp_head', 'wp_generator'); // Display the XHTML generator that is generated on the wp_head hook, WP version
	remove_action('wp_head', 'start_post_rel_link', 10, 0);
	remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);
	remove_action('wp_head', 'rel_canonical');
	remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0);
	
	// Add Filters
	add_filter('body_class', 'add_slug_to_body_class'); // Add slug to body class (Starkers build)
	add_filter('widget_text', 'do_shortcode'); // Allow shortcodes in Dynamic Sidebar
	add_filter('widget_text', 'shortcode_unautop'); // Remove <p> tags in Dynamic Sidebars (better!)
	add_filter('wp_nav_menu_args', 'my_wp_nav_menu_args'); // Remove surrounding <div> from WP Navigation
	add_filter('nav_menu_css_class', 'my_css_attributes_filter', 100, 1); // Remove Navigation <li> injected classes
	add_filter('nav_menu_item_id', 'my_css_attributes_filter', 100, 1); // Remove Navigation <li> injected ID
	add_filter('page_css_class', 'my_css_attributes_filter', 100, 1); // Remove Navigation <li> Page ID's
	add_filter('the_category', 'remove_category_rel_from_category_list'); // Remove invalid rel attribute
	add_filter('the_excerpt', 'shortcode_unautop'); // Remove auto <p> tags in Excerpt (Manual Excerpts only)
	add_filter('the_excerpt', 'do_shortcode'); // Allows Shortcodes to be executed in Excerpt (Manual Excerpts only)
	add_filter('excerpt_more', 'html5wp_view_article'); // Add 'View Article' button instead of [...] for Excerpts
	add_filter('show_admin_bar', 'remove_admin_bar'); // Remove Admin bar
	add_filter('style_loader_tag', 'html5_style_remove'); // Remove 'text/css' from enqueued stylesheet
	
	// Remove Filters
	remove_filter('the_excerpt', 'wpautop'); // Remove <p> tags from Excerpt altogether
	
	// Shortcodes
	add_shortcode('hockeyloyal_tagline', 'hockeyloyal_tagline'); // You can place [html5_shortcode_demo] in Pages, Posts now.
	function hockeyloyal_tagline() {
		return '<h1 class="hockeyloyal-tagline">Are you HOCKEY<span class="bold">LOYAL</span> ?</h1>';
	}
	
function my_login_logo() { ?>
<style type="text/css">
	body.login { background: url('<?php echo get_bloginfo( 'template_directory' ) ?>/images/body-background.png'); font-family: Calibri, Helvetica, sans-serif; font-size: 16px; }
	body.login div#login h1 a {
		background-image: url(<?php echo get_bloginfo( 'template_directory' ) ?>/images/logo-header.png);
		padding-bottom: 10px;
	}
	.login #nav a, .login #backtoblog a { color: white !important; text-shadow: none; }
	.login form { -webkit-box-shadow: none !important; -moz-box-shadow: none !important; box-shadow: none !important; }
	div.error, .login #login_error, div.updated, .login .message { color: black !important; }
</style>
<?php }
add_action( 'login_enqueue_scripts', 'my_login_logo' );

function my_login_url() { return home_url(); }
add_filter('login_headerurl', 'my_login_url');

function my_login_title() { return get_option('blogname'); }
add_filter('login_headertitle', 'my_login_title');

function my_login_redirect() { return home_url().'/profile'; }
add_filter('login_redirect', 'my_login_redirect');

function hockey_profile_nav() {
global $HockeyLoyal; ?>
	<?php if($HockeyLoyal->is_viewing_own_profile) : ?>
		<div id="own-profile-nav" class="cf">
			<?php if(get_pages( 'child_of=36' )) : ?><ul class="cf">
				<li><a href="<?php echo home_url() ?>/profile">View Profile</a></li>
				<?php wp_list_pages( array('child_of'=> 36, 'title_li'=>'') ); ?>
			</ul><?php endif; ?>
		</div>
	<?php endif; ?>
<?php } //end hockey_profile_nav()

add_shortcode('change_password_form', 'loyal_change_password_form');
function loyal_change_password_form() {
	$html .= '<form name="change-password" method="POST" action="'.home_url().'/profile/change-password/">';
	
	$html .= '<div class="profile-heading">';
	$html .= '<span class="profile-label"><label for="new-password">New Password: </label></span>
	<input type="password" name="new-password" id="new-password">';
	$html .= '</div>';
	
	$html .= '<div class="profile-heading">';
	$html .= '<span class="profile-label"><label for="new-password-again">Password Again: </label></span>
	<input type="password" name="new-password-again" id="new-password-again">';
	$html .= '</div>';
	
	$html .= '<p><input type="submit" value="Change Password" class="read-more"></p>';
	
	$html .= '<input type="hidden" name="change-password" value="1">';
	$html .= wp_nonce_field( 'loyal_change_password', '_wpnonce', true, false );
	
	$htlm .= '</form>';
	return $html;
}

?>