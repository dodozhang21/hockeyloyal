<?php get_header(); ?>
<div id="content">
	<div class="container cf">
		<div class="content-left cf">
			<div class="content-left-container cf">
				<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
					<?php the_content() ?>
				<?php endwhile; else: ?>
					<p><?php _e('Sorry, no posts matched your criteria.'); ?></p>
				<?php endif; ?>
			</div>
		</div>
		<div class="content-right cf">
			<?php get_sidebar() ?>
		</div>
	</div>
</div>
<?php get_footer(); ?>