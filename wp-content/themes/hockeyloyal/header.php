<!DOCTYPE html>
<html <?php language_attributes(); ?>>
    <head>
        <meta charset="utf-8">
        <title><?php bloginfo('name'); ?></title>
		<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0;">

        <link rel="stylesheet" href="<?php bloginfo('template_directory') ?>/css/normalize.css">
        <link rel="stylesheet" href="<?php bloginfo('template_directory') ?>/style.css">
		<!--[if lte IE 7]><link rel="stylesheet" href="<?php bloginfo('template_directory') ?>/css/ie7.css"><![endif]-->
		<link rel="stylesheet" href="<?php bloginfo('template_directory') ?>/css/flick/jquery-ui-1.9.0.custom.min.css">
		
		<?php wp_head(); ?>
		
		<script type="text/javascript">

		  var _gaq = _gaq || [];
		  _gaq.push(['_setAccount', 'UA-17304596-46']);
		  _gaq.push(['_trackPageview']);

		  (function() {
			var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
			ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
			var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		  })();

		</script>
    </head>
    <body <?php body_class(); ?>>
	
		<div class="header-bg"></div>

		<header>
			<div id="header">
				<div class="container cf">
					<div class="header-top cf">
						<div class="header-left">
							<a href="<?php echo home_url() ?>"><img src="<?php bloginfo('template_directory') ?>/images/logo-header.png" alt="HOCKEYLOYAL logo"></a>
						</div>
						<div class="header-right">
							<nav>
								<div id="nav">
									<ul class="parent">
										<li class="nav-button">
											<a href="<?php echo home_url() ?>/about">About</a>
											<?php if(get_pages( 'child_of=8' )) : ?><ul class="child"><?php wp_list_pages( array('child_of'=> 8, 'title_li'=>'') ); ?></ul><?php endif; ?>
										</li>
										<li class="nav-button">
											<a href="<?php echo home_url() ?>/shop">Shop</a>
											<?php if(get_pages( 'child_of=110' )) : ?><ul class="child"><?php wp_list_pages( array('child_of'=> 110, 'title_li'=>'') ); ?></ul><?php endif; ?>
										</li>
										<?php if(is_user_logged_in()) : ?>
											
											<li class="nav-button">
												<a href="<?php echo home_url() ?>/profile">Profile</a>
												<?php if(get_pages( 'child_of=36' )) : ?><ul class="child"><?php wp_list_pages( array('child_of'=> 36, 'title_li'=>'') ); ?>
												</ul><?php endif; ?>
											</li>
										<?php else: ?>
											<li class="nav-button">
												<a href="<?php echo home_url() ?>/wp-login.php?redirect_to=<?php echo home_url() ?>/profile">Sign In</a>
												<?php if(get_pages( 'child_of=10' )) : ?><ul class="child"><?php wp_list_pages( array('child_of'=> 10, 'title_li'=>'') ); ?></ul><?php endif; ?>
											</li>
										<?php endif; ?>
										<?php if(is_user_logged_in()) : ?>
											<li class="nav-button">
												<a href="<?php echo wp_logout_url( home_url() ); ?> ">Sign Out</a>
												<?php if(get_pages( 'child_of=10' )) : ?><ul class="child"><?php wp_list_pages( array('child_of'=> 10, 'title_li'=>'') ); ?></ul><?php endif; ?>
											</li>
										<?php else : ?>
											<li class="nav-button">
												<a href="<?php echo home_url() ?>/wp-login.php?action=register">Join</a>
											</li>
										<?php endif; ?>
									</ul>
								</div>
							</nav>
						</div>
					</div>
				</div>
			</div>
		</header>
		
		<div class="red-stripe"></div>