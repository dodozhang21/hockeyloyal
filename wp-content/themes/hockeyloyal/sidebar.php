<div class="content-right-container cf">
	<div id="sidebar">
		<div class="sidebar-top cf">
			<?php
				//Sidebar Top
				$id = 14;
				$sidebar_top = get_page($id);
				echo apply_filters('the_content', $sidebar_top->post_content);
			?>
		</div>
	</div>
</div>
<div class="sidebar-bottom cf">
	<?php
		//Sidebar Bottom
		$id = 18;
		$sidebar_bottom = get_page($id);
		echo do_shortcode('[hockeyloyal_tagline]');
		echo apply_filters('the_content', $sidebar_bottom->post_content);
	?>
</div>