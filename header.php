<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
<title><?php
	global $page, $paged;

	wp_title( '|', true, 'right' );

	bloginfo( 'name' );

	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) )
		echo " | $site_description";

	if ( $paged >= 2 || $page >= 2 )
		echo ' | ' . sprintf( __( '%s страница'), max( $paged, $page ) );

	?></title>
<link rel="shortcut icon" href="<?php bloginfo('template_url'); ?>/images/favicon.ico" type="image/x-icon" />
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />

<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/js/jquery.tools.min.js"></script>
<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/js/conferens.js"></script>
<link type="text/css" href="<?php bloginfo('template_url'); ?>/style.css" rel="stylesheet" />



<!-- Лайки -->

	<!-- G+ -->
	<script type="text/javascript" src="https://apis.google.com/js/plusone.js"></script>

	<!-- VK -->
	<script type="text/javascript" src="http://userapi.com/js/api/openapi.js?34"></script>
	<script type="text/javascript">
	  VK.init({apiId: 2418636, onlyWidgets: true});
	</script>

<?php wp_head(); ?>