<?php
/**
 * FooGallery Responsive Voting gallery template
 * This is the template that is run when a FooGallery shortcode is rendered to the frontend
 */
//the current FooGallery that is currently being rendered to the frontend
global $current_foogallery;
//the current shortcode args
global $current_foogallery_arguments;
//wordpress database object
global $wpdb;
// define our lookup table
$wpdb->pollsk   = $wpdb->prefix.'pollsk';

//get our thumbnail sizing args
$args = foogallery_gallery_template_setting( 'thumbnail_dimensions', 'thumbnail' );

//add the link setting to the args
$args['link'] = foogallery_gallery_template_setting( 'thumbnail_link', 'image' );

//get which lightbox we want to use
$lightbox = foogallery_gallery_template_setting( 'lightbox', 'unknown' );
$spacing = foogallery_gallery_template_setting( 'spacing', '' );
$hover_effect = foogallery_gallery_template_setting( 'hover-effect', 'hover-effect-zoom' );
$border_style = foogallery_gallery_template_setting( 'border-style', 'border-style-square-white' );
$alignment = foogallery_gallery_template_setting( 'alignment', 'alignment-center' );
$hover_effect_type = foogallery_gallery_template_setting( 'hover-effect-type', '' );

// voting options
$enable_voting = foogallery_gallery_template_setting( 'enable-voting', '' );
$voting_poll_id = foogallery_gallery_template_setting( 'voting-options', '' );

// caption options
// $caption_style = foogallery_gallery_template_setting( 'caption_style', 'caption-simple' );
// $caption_bgcolor = foogallery_gallery_template_setting( 'caption_bgcolor', 'rgba(0, 0, 0, 0.8)' );
// $caption_color = foogallery_gallery_template_setting( 'caption_color', '#fff' );

// Masonry specific settings
$enable_masonry = foogallery_gallery_template_setting( 'enable-masonry', '' );


require "functions.php";




$hasAttachments = (count( $current_foogallery->attachment_ids ) > 0);
$poll_answers = array();

$voted_answer = $_COOKIE["voted_$voting_poll_id"];

$voted_image = 0;

if ($enable_voting == 'enable-voting-on' && defined("WP_POLLS_VERSION") && WP_POLLS_VERSION > 2) {
	// get wp-polls question 
	$pollq = $wpdb->get_results("SELECT pollq_id, pollq_question, pollq_active FROM $wpdb->pollsq WHERE pollq_id = $voting_poll_id LIMIT 1");
	// try {
	// 	echo '<script> var is_active = '.$pollq[0]->pollq_active.'; </script>';
	// } catch (Error $e) {
	// 	echo '<script> var is_active = -1; </script>';
	// }
	
	// $pollq->pollq_active
	
	if (!empty($pollq)) {
		$polla = $wpdb->get_results("SELECT polla_answers AS image_id, polla_aid AS answer_id FROM $wpdb->pollsa WHERE polla_qid = $voting_poll_id");
		// var_dump($polla);
		
		foreach ( $polla as $key => $answer ) {
			// echo $poll_answer;
			// var_dump($poll_answer);
			$poll_answers[$answer->image_id] = $answer->answer_id;
			if ($voted_answer == $answer->answer_id) {
				$voted_image = $answer->image_id;
			}
		}
		
		/*
		
		Get All Voting Results:
		
		SELECT `polla_answers` AS image_id, `polla_aid` AS answer_id, `wp_posts`.`post_title` , COALESCE( x.cnt, 0 ) AS vote_count
		FROM `wp_pollsa`
		LEFT JOIN `wp_posts` ON `polla_answers` = `wp_posts`.`id`
		LEFT OUTER JOIN (

			SELECT `pollip_aid` , count( * ) cnt
			FROM `wp_pollsip`
			WHERE `pollip_qid` = '4'
			GROUP BY `pollip_aid`
			
		) x ON `polla_aid` = `x`.`pollip_aid`
		WHERE `polla_qid` = 4
		AND `polla_votes` > 0
		ORDER BY `vote_count` DESC
		
		*/

	}
	
}

if ($enable_masonry == 'enable-masonry-on') {
	$gutter_width = foogallery_gallery_template_setting( 'gutter_width', '' );
	$center_align = foogallery_gallery_template_setting( 'center_align', '' );
	// $width = '.item-sizer';
	$width = $args['width'].'px';
	unset( $args['height'] );
	$masonry_settings = 'data-masonry-options=\'{ "itemSelector" : ".item", "columnWidth" : "'.$width.'", "percentPosition": "true", "gutter" : '.$gutter_width.', "isFitWidth" : '.($center_align ? 'true':'false').' }\''; 
}

?>
<style>
	/* Masonry styles */
	#foogallery-gallery-<?php echo $current_foogallery->ID; ?> .item {
		margin-bottom: <?php echo $gutter_width; ?>px;
		width: <?php echo $width; ?>px;
	}
	<?php if ( $center_align ) { ?>
	#foogallery-gallery-<?php echo $current_foogallery->ID; ?> {
		margin: 0 auto;
	}
	<?php } ?>
/*	#foogallery-gallery-<?php echo $current_foogallery->ID; ?>:after {
		display:table;
		clear:both;
		content:"";
	}*/
</style>
<script type="text/javascript" charset="utf-8">
function results_callback () {
	/*jQuery.each('li.poll-answer', function(){
		
		$(this).find('span').eq(0).html()
	});*/
	jQuery('#foogallery-gallery-<?php echo $current_foogallery->ID; ?>').addClass('voted');
	console.log('results callback!');
}
<?php 
	echo "\n\t var wpPoll_id = $voting_poll_id,\n\t\t serviceURL = '".esc_attr($_SERVER['SCRIPT_NAME'])."';";	//plugins_url("wp-polls/wp-polls.php")
	
?>
</script>
<?php
	$canVote = null;
	$voted = check_voted_cookie($voting_poll_id);
	switch( gettype($voted) ) {
		case "array":
			try {
				if ($voted[0]===0) $canVote = true;
			} catch (Error $e) {
				$canVote = false;
			}
		case "integer":
			if ($voted===0) $canVote = true;
			break;
		default:
			$canVote = false;
			break;
	}
	if ($pollq[0]->pollq_active==0) $canVote = false;
	
	//(check_voted_cookie($voting_poll_id)==0) ;
	// if (check_voted_cookie($voting_poll_id)==0)
	
	
	if ( $hasAttachments ) {
		echo '<form id="polls_form_'.$voting_poll_id.'" class="votingForm wp-polls-form" name="votingForm" method="post">'
			. '<input type="hidden" name="wp-polls-nonce" value="'.wp_create_nonce('poll_'.$voting_poll_id.'-nonce').'" id="poll_'.$voting_poll_id.'_nonce">'
			. '<input type="hidden" name="poll_id" value="'.$voting_poll_id.'" />'
			. '';
	}
?>
<div class="debug">
	<?php 
		// if (is_admin()) {
		// 	echo "canVote: " . ( ($canVote===true) ? "yes" : "no");
		// }
		$voted_classname = ($canVote===true) ? "" : "voted";
		
		echo "<div id=\"polls-$voting_poll_id-loading\" class=\"wp-polls-loading\"><img src=\"".plugins_url('wp-polls/images/loading.gif')."\" width=\"16\" height=\"16\" alt=\"".__('Loading', 'wp-polls')." ...\" title=\"".__('Loading', 'wp-polls')." ...\" class=\"wp-polls-image\" />&nbsp;".__('Loading', 'wp-polls')." ...</div>\n";
	?>
</div>
<div id="foogallery-gallery-<?php echo $current_foogallery->ID; ?>" <?php echo $masonry_settings; ?> class="wp-polls <?php echo foogallery_build_class_attribute( $current_foogallery, 'foogallery-link-' . $link, 'foogallery-lightbox-' . esc_attr($lightbox), $spacing, $hover_effect, $hover_effect_type, $border_style, $alignment, $enable_voting, $voting_poll_id, $enable_masonry, $voted_classname, 'foogallery-responsive-voting-loading' ); ?> ">
	<div class="item-sizer"></div>
	<?php
	
	$attachment_metadata = array();
	
	
	
	if ( $hasAttachments ) {
		foreach ( $current_foogallery->attachments() as $attachment ) {
			$_post = $attachment;
			
			// Determine Text for Caption
			$label = $attachment->ID;
			if ( ! empty( $attachment->caption ) ) {
				$label = $attachment->caption;
			} else if ( !empty( $attachment->title ) ) {
				$label = $attachment->title;
			} else if ( !empty( $attachment->alt ) ) {
				$label = $attachment->alt;
			}
			
			
			echo '<div id="item-'.$attachment->ID.'" class="item" data-label="'.$label.'">';
				// Display Image
				// echo $attachment->html_img( $args );
				echo $attachment->html( $args, true, false );
				
				echo '</a><div class="caption">';	//.$label.'</div>'
				
				echo '<div id="result-block-'.$attachment->ID.'" class="result-block">'
					 	. '<div class="percent"></div>'
						. '<div class="percent-bar">'
							. '<div class="pollbar" style="opacity:0;width:0;">&nbsp;</div>'
						. '</div>'
					. '</div>';
				if ($canVote===true) {
					echo '<input type="radio" id="poll-answer-'.$attachment->ID.'" class="poll-answer-radio" name="poll_'.$voting_poll_id.'" value="'.$poll_answers[$attachment->ID].'" />';
				}
				echo '<label for="poll-answer-'.$attachment->ID.'">'.$label.'</label>'
				 	;
				// if ($canVote==false){
				// 	echo  '<div class="percent" style="display: block;">0%</div>'
				// 		. '<div class="percent-bar">'
				// 			. '<div style="opacity: 1; width: 0%;" class="pollbar" title="(0% | 0 Votes)">&nbsp;</div>'
				// 		. '</div>';
				// } else {

				// }
				echo  '</div>';
				// echo '</a>';
			echo '</div>';
		}
	} 
	?>
</div>
<?php

if ( $hasAttachments ) {
	// echo '<input type="hidden" name="wp-polls-nonce" value="'.wp_create_nonce('poll_'.$voting_poll_id.'-nonce').'" id="nonce">'
	// 	. '<input type="hidden" name="poll_id" value="'.$voting_poll_id.'" />';


	echo '<div id="poll_actions" class="text-center">'
		// .'<button name="vote" class="Buttons voting_submit_btn btn" onclick="poll_vote('.$voting_poll_id.');">Vote Now</button>'
		.'<div id="thanks-for-voting">Thanks for voting!</div>';
	if ($canVote===true) {
		echo '<input id="vote" type="button" name="vote" value="   Vote   " class="Buttons voting_submit_btn btn" onclick="poll_vote('.$voting_poll_id.');" disabled="disabled" />';
	}
	echo '<button id="view-results" class="Buttons btn" onclick="poll_result('.$voting_poll_id.'); return false;">View Results</button>'
		.'<button id="refresh-results" class="Buttons btn" onclick="poll_result('.$voting_poll_id.'); return false;">Refresh Results</button>'
	.'</div>';
	
	
	// if($display_loading) {
		// $poll_ajax_style = get_option('poll_ajax_style');
		// if(intval($poll_ajax_style['loading']) == 1) {
			echo "<div id=\"polls-$voting_poll_id-loading\" class=\"wp-polls-loading\"><img src=\"".plugins_url('wp-polls/images/loading.gif')."\" width=\"16\" height=\"16\" alt=\"".__('Loading', 'wp-polls')." ...\" title=\"".__('Loading', 'wp-polls')." ...\" class=\"wp-polls-image\" />&nbsp;".__('Loading', 'wp-polls')." ...</div>\n";
		// }
	// }
}
?>
</form>
<div id="view-results-<?php echo $voting_poll_id; ?>" class="view-poll-results">
<div id="polls-<?php echo $voting_poll_id; ?>" class="wp-polls">
	
</div>
</div>

<script type="text/javascript">
/* <![CDATA[ */
var plugins_url = <?php echo '"'.plugins_url('/', __FILE__).'"'; ?>;
var pollsL10n = {
	"ajax_url": <?php echo json_encode(site_url("/wp-admin/admin-ajax.php")); ?>,
	"text_wait": "Your last request is still being processed. Please wait...",
	"text_valid": "Please choose a valid poll answer.",
	"text_multiple": "Maximum number of choices allowed: ",
	"show_loading": "1",
	"show_fading": "1",
	"process_callback": function() {
		if ( !!window['console'] && !!console['log'] ) console.log('callback processed!');
	}
};
/* ]]> */
</script>
<?php

if ($canVote!==true) :

?>
<script type="text/javascript" charset="utf-8">
	jQuery(document).ready(function() {
		poll_result(<?php echo $voting_poll_id; ?>);
		poll_answer_id = "<?php echo $_COOKIE["voted_$voting_poll_id"]; ?>";
		poll_image_id = "<?php echo $voted_image; ?>";
		jQuery("#item-"+poll_image_id).addClass("highlighted").attr('title','You voted for this image.');
	});
</script>
<?php

endif;

?>
<!-- <scriipt type="text/javascript" src="<?php echo plugins_url("wp-polls/wp-polls.php"); ?>"></scriipt> -->
