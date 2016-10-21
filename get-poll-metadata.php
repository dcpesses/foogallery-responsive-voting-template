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

$image_url = wp_get_attachment_image_src($_GET['image'], 'full');
$image_url = wp_get_attachment_metadata( $_GET['image'] );
// $image_url = get_children( $_GET['image'] );



var_dump($image_url);

$poll_id = $_GET['poll'];

die();

### Polls Table
global $wpdb;

$poll_img = $wpdb->get_var(
	$wpdb->prepare(
		"SELECT pollk_aid, pollk_qid, pollk_postid FROM {$wpdb->prefix}pollsk WHERE polla_qid = %d AND pollk_postid = %d", 
		$question_id, 
		$attachment_id
	)
);


?>