<?php
/*
Plugin Name: Follow us on widget
Plugin URI: http://vijayakumar.org/web-development/easy-follow-us-on-wordpress-widget-plugin/
Description: Follow us on widget helps to share the Social Media buttons as widget
Author: Vijaya Kumar S
Version: 1
Author URI: http://vijayakumar.org/
*/ 
 
class FollowusonWidget extends WP_Widget
{
  function FollowusonWidget()
  {
    $widget_ops = array('classname' => 'FollowusonWidget', 'description' => 'Displays a Follow us on buttons' );
    $this->WP_Widget('FollowusonWidget', 'Follow us on Widget', $widget_ops);
  }
 
  function form($instance)
  {
    $instance = wp_parse_args( (array) $instance, array( 'title' => 'Follow us on' ) );
    $title = $instance['title'];
	$fbwidget = $instance['fbwidget'];
	$twitterwidget = $instance['twitterwidget'];
	$flickrwidget = $instance['flickrwidget'];
	$linkedinwidget = $instance['linkedinwidget'];
	$youtubewidget = $instance['youtubewidget'];
?>

<p>
  <label for="<?php echo $this->get_field_id('title'); ?>">Title:
    <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo attribute_escape($title); ?>" />
  </label>
</p>
<p>
  <label for="<?php echo $this->get_field_id('fbwidget'); ?>">Facebook:
    <input class="widefat" id="<?php echo $this->get_field_id('fbwidget'); ?>" name="<?php echo $this->get_field_name('fbwidget'); ?>" type="text" value="<?php echo attribute_escape($fbwidget); ?>" />
  </label>
  <em style="float:right; font-size:11px; padding-top:3px;">Ex: <a href="http://vijayakumar.com/facebook" target="_blank">http://vijayakumar.com/facebook</a></em></p>
<p style="padding-top:10px;"></p>
<p>
  <label for="<?php echo $this->get_field_id('twitterwidget'); ?>">Twitter:
    <input class="widefat" id="<?php echo $this->get_field_id('twitterwidget'); ?>" name="<?php echo $this->get_field_name('twitterwidget'); ?>" type="text" value="<?php echo attribute_escape($twitterwidget); ?>" />
  </label>
  <em style="float:right; font-size:11px; padding-top:3px;">Ex: <a href="http://vijayakumar.com/twitter" target="_blank">http://vijayakumar.com/twitter</a></em>
<p style="padding-top:10px;"></p>
<p>
  <label for="<?php echo $this->get_field_id('flickrwidget'); ?>">Flickr:
    <input class="widefat" id="<?php echo $this->get_field_id('flickrwidget'); ?>" name="<?php echo $this->get_field_name('flickrwidget'); ?>" type="text" value="<?php echo attribute_escape($flickrwidget); ?>" />
  </label>
  <em style="float:right; font-size:11px; padding-top:3px;">Ex: <a href="http://vijayakumar.com/flickr" target="_blank">http://vijayakumar.com/flickr</a></em>
<p style="padding-top:10px;"></p>
<p>
  <label for="<?php echo $this->get_field_id('linkedinwidget'); ?>">Linkedin:
    <input class="widefat" id="<?php echo $this->get_field_id('linkedinwidget'); ?>" name="<?php echo $this->get_field_name('linkedinwidget'); ?>" type="text" value="<?php echo attribute_escape($linkedinwidget); ?>" />
  </label>
  <em style="float:right; font-size:11px; padding-top:3px;">Ex: <a href="http://vijayakumar.com/linkedin" target="_blank">http://vijayakumar.com/linkedin</a></em>
<p style="padding-top:10px;"></p>
<p>
  <label for="<?php echo $this->get_field_id('youtubewidget'); ?>">Youtube:
    <input class="widefat" id="<?php echo $this->get_field_id('youtubewidget'); ?>" name="<?php echo $this->get_field_name('youtubewidget'); ?>" type="text" value="<?php echo attribute_escape($youtubewidget); ?>" />
  </label>
  <em style="float:right; font-size:11px; padding-top:3px;">Ex: <a href="http://vijayakumar.com/youtube" target="_blank">http://vijayakumar.com/youtube</a></em>
  <?php
  }
 
  function update($new_instance, $old_instance)
  {
    $instance = $old_instance;
    $instance['title'] = $new_instance['title'];
	$instance['fbwidget'] = $new_instance['fbwidget'];
	$instance['twitterwidget'] = $new_instance['twitterwidget'];
	$instance['flickrwidget'] = $new_instance['flickrwidget'];
	$instance['linkedinwidget'] = $new_instance['linkedinwidget'];
	$instance['youtubewidget'] = $new_instance['youtubewidget'];
    return $instance;
  }
 
  function widget($args, $instance)
  {
    extract($args, EXTR_SKIP);
 
    echo $before_widget;
    $title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);
    if (!empty($title))
		echo $before_title . $title . $after_title;
		$arraysplit = explode("/",plugin_basename(__FILE__));
		echo "<ul class='follow'>";
		if($instance['fbwidget'] != '') echo "<li><a href='$instance[fbwidget]' target='_blank'><img alt='Facebook' src='".plugins_url()."/".$arraysplit[0]."/images/facebook.jpg' /></a></li>";
		if($instance['twitterwidget'] != '') echo "<li><a href='$instance[twitterwidget]' target='_blank'><img alt='Twitter' src='".plugins_url()."/".$arraysplit[0]."/images/twitter.jpg' /></a></li>";
		if($instance['flickrwidget'] != '') echo "<li><a href='$instance[flickrwidget]' target='_blank'><img alt='Flickr' src='".plugins_url()."/".$arraysplit[0]."/images/flickr.jpg' /></a></li>";
		if($instance['linkedinwidget'] != '') echo "<li><a href='$instance[linkedinwidget]' target='_blank'><img alt='Linkedin' src='".plugins_url()."/".$arraysplit[0]."/images/linkedin.jpg' /></a></li>";
		if($instance['youtubewidget'] != '') echo "<li><a href='$instance[youtubewidget]' target='_blank'><img alt='Youtube' src='".plugins_url()."/".$arraysplit[0]."/images/youtube.jpg' /></a></li>";
		echo "</ul>";
		echo $after_widget;
  }
}
add_action( 'widgets_init', create_function('', 'return register_widget("FollowusonWidget");') );?>
