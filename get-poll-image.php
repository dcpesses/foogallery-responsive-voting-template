<?php

### Load WP-Config File If This File Is Called Directly
if (!function_exists('add_action')) {
	$wp_root = '../../..';
	if (file_exists($wp_root.'/wp-load.php')) {
		require_once($wp_root.'/wp-load.php');
	} else {
		require_once($wp_root.'/wp-config.php');
	}
}

$image_src = wp_get_attachment_image_src($_GET['image'], 'full');
if ( !empty( $image_src[0] )) {
	header("Location: ".$image_src[0], TRUE, 301);
}
?>