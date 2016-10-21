<?php
/**
 * FooGallery Responsive Voting Extension
 *
 * Responsive thumbnail template with wp-polls plugin integration
 *
 * @package   Responsive_Voting_Template_FooGallery_Extension
 * @author    Danny Pesses
 * @license   GPL-2.0+
 * @link
 * @copyright 2014-2016 Danny Pesses
 *
 * @wordpress-plugin
 * Plugin Name: FooGallery - Responsive Voting
 * Description: Responsive thumbnail template with wp-polls plugin integration
 * Version:     1.1.1
 * Author:      Danny Pesses
 * Author URI:
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

if ( !class_exists( 'Responsive_Voting_Template_FooGallery_Extension' ) ) {

	define('RESPONSIVE_VOTING_TEMPLATE_FOOGALLERY_EXTENSION_URL', plugin_dir_url( __FILE__ ));
	define('RESPONSIVE_VOTING_TEMPLATE_FOOGALLERY_EXTENSION_VERSION', '1.1.1');

	require_once( 'foogallery-responsive-voting-init.php' );

	class Responsive_Voting_Template_FooGallery_Extension {
		/**
		 * Wire up everything we need to run the extension
		 */
		function __construct() {
			add_filter( 'foogallery_gallery_templates', array( $this, 'add_template' ) );
			add_filter( 'foogallery_gallery_templates_files', array( $this, 'register_myself' ) );
			add_filter( 'foogallery_located_template-responsive-voting', array( $this, 'enqueue_responsive_voting_dependencies' ) );
			// add_action( 'foogallery_render_gallery_template_field_custom', array( $this, 'render_thumbnail_preview' ), 10, 3 );	// already loaded by default
			// add_filter( 'foogallery_located_template-default', array( $this, 'enqueue_default_dependencies' ) );
			add_filter( 'foogallery_located_template-masonry', array( $this, 'enqueue_masonry_dependencies' ) );
			// add_filter( 'foogallery_attachment_html_link',  array( $this, 'attach_html_caption' ) );
			// add_action( 'admin_footer-post-new.php', 'poll_footer_admin_qtags' );
			// add_action( 'admin_footer-post.php', 'poll_footer_admin_qtags' );
			// add_action( 'admin_footer-page-new.php', 'poll_footer_admin_qtags' );
			// add_action( 'admin_footer-page.php', 'poll_footer_admin_qtags' );
			// add_action( 'foogallery_admin_print_scripts', 'quicktags_fix' );
			// add_action( 'foogallery_admin_menu_before', 'quicktags_fix' );

			// foogallery_save_gallery_settings
			// foogallery_save_gallery_attachments
			add_filter( 'foogallery_save_gallery_settings', array( $this, 'update_poll_settings' ) );
			// add_filter( 'foogallery_save_gallery_attachments', array( $this, 'update_poll_answers' ) );
			add_filter( 'foogallery_after_save_gallery', array( $this, 'update_poll_on_save' ) );


			add_filter('poll_template_resultbody2', array( $this, 'poll_template_vote_markup' ), 10, 3);
			add_filter('poll_template_resultbody', array( $this, 'poll_template_vote_markup' ), 10, 3);
		}


		function poll_template_vote_markup($template, $poll_db_object, $variables) {

			foreach($variables as $placeholder => $value) {
				if ($placeholder == "%POLL_ANSWER_TEXT%") {
					$attachment = wp_get_attachment_metadata($value);
					$label = $attachment->ID;
					if ( ! empty( $attachment->caption ) ) {
						$label = $attachment->caption;
					} else if ( !empty( $attachment->title ) ) {
						$label = $attachment->title;
					} else if ( !empty( $attachment->alt ) ) {
						$label = $attachment->alt;
					}
					$value = $label;
				}
				$template = str_replace($placeholder, $value, $template);
			}

			return $template;
		}

		function update_poll_answers( $attachments ) {
			global $wpdb, $post;
			/*
			foreach ($attachments as $key => $attachment) {
				$wpdb->query('UPDATE '.$wpdb->prefix.'nggv_settings SET criteria_id = "'.$criteriaId.'", force_login = "'.$login.'", force_once = "'.$once.'", user_results = "'.$user_results.'", enable = "'.$enable.'", voting_type = "'.$voting_type.'" WHERE pid = "'.$wpdb->escape($pid).'" AND criteria_id = "'.$wpdb->escape($criteriaId).'"');

				$wpdb->update(
					$wpdb->pollsa,
					array(
						'polla_answers' => $attachment,
						'polla_qid' => $attachment,
					),
					$where,
					$format = null,
					$where_format = null
				);
			}*/

			// var_dump($current_foogallery_arguments);
			return $attachments;
		}

		function update_poll_settings( $settings ) {
			global $wpdb;

			//var_dump($settings);
			//$post_id = var_dump($settings);

			// $post_meta = get_post_meta( $post_id, FOOGALLERY_META_SETTINGS);

			// var_dump($this);

			// if ()
			// remove all existing lookup entries
		//	$wpdb->query( 'DELETE FROM '.$wpdb->prefix.'pollsk WHERE pollk_postid = '.$post_id );

			/*
			foreach ($attachments as $key => $attachment) {
				$wpdb->query('UPDATE '.$wpdb->prefix.'nggv_settings SET criteria_id = "'.$criteriaId.'", force_login = "'.$login.'", force_once = "'.$once.'", user_results = "'.$user_results.'", enable = "'.$enable.'", voting_type = "'.$voting_type.'" WHERE pid = "'.$wpdb->escape($pid).'" AND criteria_id = "'.$wpdb->escape($criteriaId).'"');

				$wpdb->update(
					$wpdb->pollsa,
					array(
						'polla_answers' => $attachment,
						'polla_qid' => $attachment,
					),
					$where,
					$format = null,
					$where_format = null
				);
			}*/
			// var_dump($post);
			// var_dump($settings);
			return $settings;
		}

		function update_poll_on_save( $post_id ) {
			global $wpdb, $post;

			$gallery = FooGallery::get_by_id( $post_id );
			$attachments = $gallery->attachments();
			$question_id = intval( $gallery->settings['responsive-voting_voting-options'] );

			// // remove all existing lookup entries
			// $wpdb->query( 'DELETE FROM '.$wpdb->prefix.'pollsk WHERE pollk_postid = '.$post_id );

			$k_values = array();
			$a_values = array();

			foreach ($attachments as $attachment) {
				$attachment_id = $attachment->ID;

				// $k_values[] = $wpdb->prepare( "(%d,%d,%d)", $attachment_id, $question_id, $post_id );
				//
				// $a_values[] = $wpdb->prepare( "(%d,%d)", $question_id, $attachment_id );
				//
				$wpdb->insert(
					$wpdb->prefix.'pollsk',
					array(
						'pollk_aid' => $attachment_id,
						'pollk_qid' => $question_id,
						'pollk_postid' => $post_id
					),
					array(
						'%d',
						'%d',
						'%d'
					)
				);

				$polla_aid = $wpdb->get_var(
					$wpdb->prepare(
						"SELECT polla_aid, polla_qid, polla_answers FROM {$wpdb->prefix}pollsa WHERE polla_qid = %d AND polla_answers = %d",
						$question_id,
						$attachment_id
					)
				);

				if ($polla_aid!==false && $polla_aid > 0) {
					// already exists in db
					$wpdb->update(
						$wpdb->prefix.'pollsa',
						array(
							'polla_answers' => $attachment_id,
							'polla_qid' => $question_id
						),
						array(
							'polla_aid' => $polla_aid
						),
						array(
							'%d',
							'%d'
						)
					);
				} else {
					// add to db
					$wpdb->insert(
						$wpdb->prefix.'pollsa',
						array(
							'polla_answers' => $attachment_id,
							'polla_qid' => $question_id
						),
						array(
							'%d',
							'%d'
						)
					);
				}




				/*
				$wpdb->update(
					'table',
					array(
						'column1' => 'value1',	// string
						'column2' => 'value2'	// integer (number)
					),
					array( 'ID' => 1 ),
					array(
						'%s',	// value1
						'%d'	// value2
					),
					array( '%d' )
				);
				*/

				// $queryk = "INSERT INTO {$wpdb->prefix}item_info (post_id,item_stock) VALUES (%d,%s) ON DUPLICATE KEY UPDATE item_stock = %s";
				// var_dump($sql); // debug
				// $sqlk = $wpdb->prepare($query,$post_id,$item_stock,$item_stock);

				// $wpdb->query('INSERT INTO '.$wpdb->pollsk.' SET pollk_aid = "'.$wpdb->escape($attachment_id).'", pollk_qid = "'.$wpdb->escape($question_id).'", pollk_postid = "'.$wpdb->escape($post_id).'" WHERE pollk_postid = "'.$wpdb->escape($post_id).'" AND pollk_postid = "'.$wpdb->escape($post_id).'"');

				/*$wpdb->update(
					$wpdb->pollsa,
					array(
						'polla_answers' => $attachment,
						'polla_qid' => $attachment,
					),
					$where,
					$format = null,
					$where_format = null
				);*/
			}


			// $values = array();
			//
			// // We're preparing each DB item on it's own. Makes the code cleaner.
			// foreach ( $items as $key => $value ) {
			//     $values[] = $wpdb->prepare( "(%d,%d,%d)", $key, $value, $postid );
			// }

/*
			$k_query = "INSERT INTO ".$wpdb->prefix."pollsk (pollk_aid, pollk_qid, pollk_postid) VALUES ";
			$k_query .= implode( ",\n", $k_values );

			$a_query = "UPDATE ".$wpdb->prefix."pollsa (polla_qid, polla_answers) VALUES ";
			$a_query .= implode( ",", $a_values );

			// var_dump($sql); // debug
			$wpdb->query($k_query);
			$wpdb->query($a_query);
*/

			/*// Example From: detach_gallery_from_all_posts
			$gallery = FooGallery::get_by_id( $post_id );
            $posts = $gallery->find_usages();

            foreach ( $posts as $post ) {
                delete_post_meta( $post->ID, FOOGALLERY_META_POST_USAGE_CSS );
            }*/

		}


		function attach_html_caption() {
		/*	if ( ! empty( $this->caption ) ) {
				$attr['data-caption-title'] = $this->caption;
			}

			if ( !empty( $this->description ) ) {
				$attr['data-caption-desc'] = $this->description;
			}*/

			return '<div><i>foo!</i></div>';
		}

		// function poll_footer_admin_qtags(){
		// 	$js =  RESPONSIVE_VOTING_TEMPLATE_FOOGALLERY_EXTENSION_URL . 'js/temp.js';
		// 	wp_enqueue_script( 'responsive-voting', $js, array('quicktags'));
		// }

		/**
		 * Register myself so that all associated JS and CSS files can be found and automatically included
		 * @param $extensions
		 *
		 * @return array
		 */
		function register_myself( $extensions ) {
			$extensions[] = __FILE__;
			return $extensions;
		}



		function quicktags_fix( $hook ){
			// wp-polls bug workaround for ReferenceError: "QTags is not defined."
			$qtjs = includes_url() . 'js/quicktags.min.js';
			wp_enqueue_script( 'quicktags_fix', $qtjs );
		}

		/**
		 * Enqueue any script or stylesheet file dependencies that your gallery template relies on
		 */
		function enqueue_responsive_voting_dependencies() {
			wp_enqueue_script( 'jquery' );
			// wp_enqueue_script( 'masonry' );
			$js = RESPONSIVE_VOTING_TEMPLATE_FOOGALLERY_EXTENSION_URL . 'js/imagesloaded.pkgd.min.js';
			wp_enqueue_script( 'responsive-voting', $js, array('jquery'), RESPONSIVE_VOTING_TEMPLATE_FOOGALLERY_EXTENSION_VERSION );
			// $css = RESPONSIVE_VOTING_TEMPLATE_FOOGALLERY_EXTENSION_URL . 'css/admin-gallery-responsive-voting.css';
			// foogallery_enqueue_style( 'responsive-voting', $css, array(), RESPONSIVE_VOTING_TEMPLATE_FOOGALLERY_EXTENSION_VERSION );

			// override wp-polls script
			// wp_dequeue_script('wp-polls');
			// wp_enqueue_script('responsive-voting-polls', plugins_url('wp-polls/polls-js.js'), array('jquery'), WP_POLLS_VERSION, true);
		}

		/**
		 * Add our gallery template to the list of templates available for every gallery
		 * @param $gallery_templates
		 *
		 * @return array
		 */
		function add_template( $gallery_templates ) {
			global $wpdb;
			$poll_choices = array('-'=>'No Polls Found');
			if ( defined("WP_POLLS_VERSION") && WP_POLLS_VERSION > 2 ){

				// var_dump($poll_questions = $wpdb->get_results("SELECT * FROM $wpdb->pollsq  ORDER BY pollq_timestamp DESC", "OBJECT_K"));

				$poll_questions = $wpdb->get_results("SELECT * FROM $wpdb->pollsq  ORDER BY pollq_timestamp DESC", "OBJECT_K");
				$poll_choices = array();
				foreach ($poll_questions as $poll_question) {
					$status = ($poll_question->pollq_active=='1') ? '' : ' [INACTIVE]';
					$poll_choices[ $poll_question->pollq_id ] = $poll_question->pollq_question . $status;
				}

			}

			if (!defined('RESPONSIVE_VOTING_TEMPLATE_FOOGALLERY_ADMIN_IMG_URL')) {
				if (defined('FOOGALLERY_DEFAULT_TEMPLATES_EXTENSION_SHARED_URL')) {
					define('RESPONSIVE_VOTING_TEMPLATE_FOOGALLERY_ADMIN_IMG_URL', constant("FOOGALLERY_DEFAULT_TEMPLATES_EXTENSION_SHARED_URL") . 'img/admin/');
				} else {
					define('RESPONSIVE_VOTING_TEMPLATE_FOOGALLERY_ADMIN_IMG_URL', constant("FOOGALLERY_DEFAULT_TEMPLATES_EXTENSION_URL") . 'assets/');
				}
			}

			$poll_choices['new'] = '-- Create New Poll --';

			$gallery_templates[] = array(
				'slug'        => 'responsive-voting',
				'name'        => __( 'Responsive Voting', 'foogallery-responsive-voting'),
				'preview_css' => RESPONSIVE_VOTING_TEMPLATE_FOOGALLERY_EXTENSION_URL . 'css/gallery-responsive-voting.css',
				'admin_js'	  => RESPONSIVE_VOTING_TEMPLATE_FOOGALLERY_EXTENSION_URL . 'js/admin-gallery-responsive-voting.js',
				'fields'	  => array(
					array(
						'id'      => 'enable-voting',
						'title'   => __( 'Enable Voting', 'foogallery-responsive-voting' ),
						'desc'    => __( 'Enable voting on thumbnails inside the gallery.', 'foogallery-responsive-voting' ),
						'section' => __( 'Voting Settings', 'foogallery-responsive-voting' ),
						'default' => 'enable-voting-off',
						'type'    => 'radio',
						'choices' => array(
							'enable-voting-on' => __( 'Enabled', 'foogallery-responsive-voting' ),
							'enable-voting-off' => __( 'Disabled', 'foogallery-responsive-voting' ),
						)
					),
					array(
						'id'      => 'voting-options',
						'title'   => __( 'Select Voting Poll', 'foogallery-responsive-voting' ),
						'desc'    => __( 'Choose the poll to use for voting. (Must have wp-polls installed and activated to use.)', 'foogallery-responsive-voting' ),
						'section' => __( 'Voting Settings', 'foogallery-responsive-voting' ),
						'default' => 'voting-gallery',
						'type'    => 'select',
						'choices' => $poll_choices
					),
					array(
						'id'      => 'thumbnail_dimensions',
						'title'   => __( 'Size', 'foogallery-responsive-voting' ),
						'desc'    => __( 'Choose the size of your thumbnails.', 'foogallery-responsive-voting' ),
						'section' => __( 'Thumbnail Settings', 'foogallery-responsive-voting' ),
						'type'    => 'thumb_size',
						'default' => array(
							'width' => get_option( 'thumbnail_size_w' ),
							'height' => get_option( 'thumbnail_size_h' ),
							'crop' => true,
						),
					),
					array(
						'id'      => 'thumbnail_link',
						'title'   => __( 'Link', 'foogallery-responsive-voting' ),
						'section' => __( 'Thumbnail Settings', 'foogallery-responsive-voting' ),
						'default' => 'image',
						'type'    => 'thumb_link',
						'spacer'  => '<span class="spacer"></span>',
						'desc'	  => __( 'You can choose to link each thumbnail to the full size image, the image\'s attachment page, a custom URL, or you can choose to not link to anything.', 'foogallery-responsive-voting' ),
					),
					array(
						'id'      => 'border-style',
						'title'   => __( 'Border Style', 'foogallery-responsive-voting' ),
						'desc'    => __( 'The border style for each thumbnail in the gallery.', 'foogallery-responsive-voting' ),
						'section' => __( 'Thumbnail Settings', 'foogallery-responsive-voting' ),
						'type'    => 'icon',
						'default' => 'border-style-square-white',
						'choices' => array(
							'border-style-square-white' => array( 'label' => __( 'Square white border with shadow' , 'foogallery-responsive-voting' ), 'img' => RESPONSIVE_VOTING_TEMPLATE_FOOGALLERY_ADMIN_IMG_URL . 'border-style-icon-square-white.png' ),
							'border-style-circle-white' => array( 'label' => __( 'Circular white border with shadow' , 'foogallery-responsive-voting' ), 'img' => RESPONSIVE_VOTING_TEMPLATE_FOOGALLERY_ADMIN_IMG_URL . 'border-style-icon-circle-white.png' ),
							'border-style-square-black' => array( 'label' => __( 'Square Black' , 'foogallery-responsive-voting' ), 'img' => RESPONSIVE_VOTING_TEMPLATE_FOOGALLERY_ADMIN_IMG_URL . 'border-style-icon-square-black.png' ),
							'border-style-circle-black' => array( 'label' => __( 'Circular Black' , 'foogallery-responsive-voting' ), 'img' => RESPONSIVE_VOTING_TEMPLATE_FOOGALLERY_ADMIN_IMG_URL . 'border-style-icon-circle-black.png' ),
							'border-style-inset' => array( 'label' => __( 'Square Inset' , 'foogallery-responsive-voting' ), 'img' => RESPONSIVE_VOTING_TEMPLATE_FOOGALLERY_ADMIN_IMG_URL . 'border-style-icon-square-inset.png' ),
							'border-style-rounded' => array( 'label' => __( 'Plain Rounded' , 'foogallery-responsive-voting' ), 'img' => RESPONSIVE_VOTING_TEMPLATE_FOOGALLERY_ADMIN_IMG_URL . 'border-style-icon-plain-rounded.png' ),
							'' => array( 'label' => __( 'Plain' , 'foogallery-responsive-voting' ), 'img' => RESPONSIVE_VOTING_TEMPLATE_FOOGALLERY_ADMIN_IMG_URL . 'border-style-icon-none.png' ),
						)
					),
					array(
						'id'      => 'hover-effect-type',
						'title'   => __( 'Hover Effect Type', 'foogallery-responsive-voting' ),
						'section' => __( 'Thumbnail Settings', 'foogallery-responsive-voting' ),
						'default' => '',
						'type'    => 'radio',
						'choices' => apply_filters( 'foogallery_gallery_template_hover-effect-types', array(
							''  => __( 'Icon', 'foogallery-responsive-voting' ),
							'hover-effect-tint'   => __( 'Dark Tint', 'foogallery-responsive-voting' ),
							'hover-effect-color' => __( 'Colorize', 'foogallery-responsive-voting' ),
							'hover-effect-none' => __( 'None', 'foogallery-responsive-voting' )
						) ),
						'spacer'  => '<span class="spacer"></span>',
						'desc'	  => __( 'The type of hover effect the thumbnails will use.', 'foogallery-responsive-voting' ),
					),
					array(
						'id'      => 'hover-effect',
						'title'   => __( 'Icon Hover Effect', 'foogallery-responsive-voting' ),
						'desc'    => __( 'When the hover effect type of Icon is chosen, you can choose which icon is shown when you hover over each thumbnail.', 'foogallery-responsive-voting' ),
						'section' => __( 'Thumbnail Settings', 'foogallery-responsive-voting' ),
						'type'    => 'icon',
						'default' => 'hover-effect-zoom',
						'choices' => array(
							'hover-effect-zoom' => array( 'label' => __( 'Zoom' , 'foogallery-responsive-voting' ), 'img' => RESPONSIVE_VOTING_TEMPLATE_FOOGALLERY_ADMIN_IMG_URL . 'hover-effect-icon-zoom.png' ),
							'hover-effect-zoom2' => array( 'label' => __( 'Zoom 2' , 'foogallery-responsive-voting' ), 'img' => RESPONSIVE_VOTING_TEMPLATE_FOOGALLERY_ADMIN_IMG_URL . 'hover-effect-icon-zoom2.png' ),
							'hover-effect-zoom3' => array( 'label' => __( 'Zoom 3' , 'foogallery-responsive-voting' ), 'img' => RESPONSIVE_VOTING_TEMPLATE_FOOGALLERY_ADMIN_IMG_URL . 'hover-effect-icon-zoom3.png' ),
							'hover-effect-plus' => array( 'label' => __( 'Plus' , 'foogallery-responsive-voting' ), 'img' => RESPONSIVE_VOTING_TEMPLATE_FOOGALLERY_ADMIN_IMG_URL . 'hover-effect-icon-plus.png' ),
							'hover-effect-circle-plus' => array( 'label' => __( 'Cirlce Plus' , 'foogallery-responsive-voting' ), 'img' => RESPONSIVE_VOTING_TEMPLATE_FOOGALLERY_ADMIN_IMG_URL . 'hover-effect-icon-circle-plus.png' ),
							'hover-effect-eye' => array( 'label' => __( 'Eye' , 'foogallery-responsive-voting' ), 'img' => RESPONSIVE_VOTING_TEMPLATE_FOOGALLERY_ADMIN_IMG_URL . 'hover-effect-icon-eye.png' )
						),
					),
					// array(
					// 	'id' => 'thumb_preview',
					// 	'title' => __( 'Preview', 'foogallery-responsive-voting' ),
					// 	'desc' => __( 'This is what your gallery thumbnails will look like.', 'foogallery-responsive-voting' ),
					// 	'section' => __( 'Thumbnail Settings', 'foogallery-responsive-voting' ),
					// 	'type' => 'thumb_preview',
					// ),
					array(
						'id'      => 'lightbox',
						'title'   => __( 'Lightbox', 'foogallery-responsive-voting' ),
						'section' => __( 'Gallery Settings', 'foogallery-responsive-voting' ),
						'desc'    => __( 'Choose which lightbox you want to use. The lightbox will only work if you set the thumbnail link to "Full Size Image".', 'foogallery-responsive-voting' ),
						'type'    => 'lightbox',
					),
					array(
						'id'      => 'spacing',
						'title'   => __( 'Spacing', 'foogallery-responsive-voting' ),
						'desc'    => __( 'The spacing or gap between thumbnails in the gallery.', 'foogallery-responsive-voting' ),
						'type'    => 'select',
						'section' => __( 'Gallery Settings', 'foogallery-responsive-voting' ),
						'default' => 'spacing-width-10',
						'choices' => array(
							'spacing-width-0' => __( 'None', 'foogallery-responsive-voting' ),
							'spacing-width-5' => __( '5 pixels', 'foogallery-responsive-voting' ),
							'spacing-width-10' => __( '10 pixels', 'foogallery-responsive-voting' ),
							'spacing-width-15' => __( '15 pixels', 'foogallery-responsive-voting' ),
							'spacing-width-20' => __( '20 pixels', 'foogallery-responsive-voting' ),
							'spacing-width-25' => __( '25 pixels', 'foogallery-responsive-voting' ),
						),
					),
					array(
						'id'      => 'alignment',
						'title'   => __( 'Alignment', 'foogallery-responsive-voting' ),
						'desc'    => __( 'The horizontal alignment of the thumbnails inside the gallery.', 'foogallery-responsive-voting' ),
						'section' => __( 'Gallery Settings', 'foogallery-responsive-voting' ),
						'default' => 'alignment-center',
						'type'    => 'select',
						'choices' => array(
							'alignment-left' => __( 'Left', 'foogallery-responsive-voting' ),
							'alignment-center' => __( 'Center', 'foogallery-responsive-voting' ),
							'alignment-right' => __( 'Right', 'foogallery-responsive-voting' ),
						)
					),
					array(
						'id'      => 'enable-masonry',
						'title'   => __( 'Enable Masonry', 'foogallery-responsive-voting' ),
						'desc'    => __( 'Enable Masonry layout on thumbnails inside the gallery. (Experimental)', 'foogallery-responsive-voting' ),
						'section' => __( 'Masonry Settings', 'foogallery-responsive-voting' ),
						'default' => 'enable-masonry-off',
						'type'    => 'radio',
						'class'   => 'enable-masonry',
						'choices' => array(
							'enable-masonry-on' => __( 'Enabled', 'foogallery-responsive-voting' ),
							'enable-masonry-off' => __( 'Disabled', 'foogallery-responsive-voting' ),
						)
					),
					// array(
					// 	'id'      => 'thumbnail_width',
					// 	'title'   => __( 'Thumbnail Width', 'foogallery-responsive-voting' ),
					// 	'desc'    => __( 'Choose the width of your thumbnails. Thumbnails will be generated on the fly and cached once generated.', 'foogallery-responsive-voting' ),
					// 	'section' => __( 'Masonry Settings', 'foogallery-responsive-voting' ),
					// 	'type'    => 'number',
					// 	'class'   => 'small-text',
					// 	'default' => 150,
					// 	'step'    => '1',
					// 	'min'     => '0',
					// ),
					array(
						'id'      => 'gutter_width',
						'title'   => __( 'Gutter Width', 'foogallery-responsive-voting' ),
						'desc'    => __( 'The spacing between your thumbnails.', 'foogallery-responsive-voting' ),
						'section' => __( 'Masonry Settings', 'foogallery-responsive-voting' ),
						'type'    => 'number',
						'class'   => 'small-text',
						'default' => 10,
						'step'    => '1',
						'min'     => '0',
					),
					array(
						'id'      => 'center_align',
						'title'   => __( 'Image Alignment', 'foogallery-responsive-voting' ),
						'desc'    => __( 'You can choose to center align your images or leave them at the default.', 'foogallery-responsive-voting' ),
						'section' => __( 'Masonry Settings', 'foogallery-responsive-voting' ),
						'type'    => 'radio',
						'choices' => array(
							'default'  => __( 'Left Alignment', 'foogallery-responsive-voting' ),
							'center'   => __( 'Center Alignment', 'foogallery-responsive-voting' )
						),
						'spacer'  => '<span class="spacer"></span>',
						'default' => 'default'
					)
					//available field types available : html, checkbox, select, radio, textarea, text, checkboxlist, icon
					//an example of a icon field used in the default gallery template
					//array(
					//	'id'      => 'border-style',
					//	'title'   => __('Border Style', 'foogallery-responsive-voting'),
					//	'desc'    => __('The border style for each thumbnail in the gallery.', 'foogallery-responsive-voting'),
					//	'type'    => 'icon',
					//	'default' => 'border-style-square-white',
					//	'choices' => array(
					//		'border-style-square-white' => array('label' => 'Square white border with shadow', 'img' => RESPONSIVE_VOTING_TEMPLATE_FOOGALLERY_ADMIN_IMG_URL . 'border-style-icon-square-white.png'),
					//		'border-style-circle-white' => array('label' => 'Circular white border with shadow', 'img' => RESPONSIVE_VOTING_TEMPLATE_FOOGALLERY_ADMIN_IMG_URL . 'border-style-icon-circle-white.png'),
					//		'border-style-square-black' => array('label' => 'Square Black', 'img' => RESPONSIVE_VOTING_TEMPLATE_FOOGALLERY_ADMIN_IMG_URL . 'border-style-icon-square-black.png'),
					//		'border-style-circle-black' => array('label' => 'Circular Black', 'img' => RESPONSIVE_VOTING_TEMPLATE_FOOGALLERY_ADMIN_IMG_URL . 'border-style-icon-circle-black.png'),
					//		'border-style-inset' => array('label' => 'Square Inset', 'img' => RESPONSIVE_VOTING_TEMPLATE_FOOGALLERY_ADMIN_IMG_URL . 'border-style-icon-square-inset.png'),
					//		'border-style-rounded' => array('label' => 'Plain Rounded', 'img' => RESPONSIVE_VOTING_TEMPLATE_FOOGALLERY_ADMIN_IMG_URL . 'border-style-icon-plain-rounded.png'),
					//		'' => array('label' => 'Plain', 'img' => RESPONSIVE_VOTING_TEMPLATE_FOOGALLERY_ADMIN_IMG_URL . 'border-style-icon-none.png'),
					//	)
					//),
				)
			);

			return $gallery_templates;
		}
	}
}
