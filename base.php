<?php
/**
 * The base template file.
 *
 * This is the theme wrapper template file
 * The entire site structure is included here: header, hero, content, sidebar, and footer
 * If you don't want/need the hero section, get rid of the if(is_front_page) conditional and delete hero.php
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Base_Install
 */
?>

<!DOCTYPE html>
<!--[if lt IE 7]><html <?php language_attributes(); ?> class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if (IE 7)&!(IEMobile)]><html <?php language_attributes(); ?> class="no-js lt-ie9 lt-ie8"><![endif]-->
<!--[if (IE 8)&!(IEMobile)]><html <?php language_attributes(); ?> class="no-js lt-ie9"><![endif]-->
<!--[if gt IE 8]><!--> <html <?php language_attributes(); ?> class="no-js"><!--<![endif]-->
	<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>">
		<meta name="HandheldFriendly" content="True">
		<meta name="MobileOptimized" content="320">
		<meta name="viewport" content="width=device-width, initial-scale=1"/>
		<link rel="profile" href="http://gmpg.org/xfn/11">
		<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>">

		<?php 
		/*
		if (!isset($_SERVER['HTTP_USER_AGENT']) || stripos($_SERVER['HTTP_USER_AGENT'], 'Speed Insights') === false): ?>
			<!-- Replace with your own Google Analytics code -->
			<script>
				(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
				(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
				m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
				})(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

				ga('create', 'UA-XXXXX-Y', 'auto');
				ga('send', 'pageview');
			</script>
		<?php endif; 
		*/
		?>
		
		<?php wp_head(); ?>
	</head>
	<body <?php body_class('sideNavBody'); ?>>
		<div id="page" class="site">

		<h2 class="screen-reader-text"><?php bloginfo( 'name' ); ?></h2>

			<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'baseinstall' ); ?></a>
			<?php get_header( baseinstall_template_base() ); ?>

			<div id="content-wrap" class="site-content-wrap">
			
				<?php get_template_part('template-parts/hero'); ?>

				<?php if ( is_home() && is_front_page() ) : // if front page is set to show latest posts, show this content ?>
					<div id="content" class="site-content">
						<div id="primary" class="content-area">
							<main id="main" class="site-main">
								<?php include baseinstall_template_path(); ?>
							</main>
						</div>
						<?php get_sidebar( baseinstall_template_base() ); ?>

				<?php elseif ( is_front_page() ) : // if front page is set to show static page, get front-page.php markup ?>
					<div id="content">
						<?php include baseinstall_template_path(); ?>

				<?php else : // all other pages ?>
					<div id="content" class="site-content">
						<div id="primary" class="content-area">
							<main id="main" class="site-main">
								<?php include baseinstall_template_path(); ?>
							</main>
						</div>
						<?php get_sidebar( baseinstall_template_base() ); ?>
				<?php endif; ?>

					</div><?php // close #content ?>

			</div><?php // close #content-wrap ?>

			<?php get_footer( baseinstall_template_base() ); ?>

		</div><?php // close #page ?>
		<?php wp_footer(); ?>
		<a id="scroll-to-top" href="#page"></a>
	</body>
</html>

