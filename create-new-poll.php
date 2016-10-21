<?php

### Load WP-Config File If This File Is Called Directly
// if (!function_exists('add_action')) {
// 	$wp_root = '../../..';
// 	if (file_exists($wp_root.'/wp-load.php')) {
// 		require_once($wp_root.'/wp-load.php');
// 	} else {
// 		require_once($wp_root.'/wp-config.php');
// 	}
// }

### Check Whether User Can Manage Polls
if(!current_user_can('manage_polls')) {
	die('Access Denied');
}

### Poll Manager
$base_name = plugin_basename('wp-polls/polls-manager.php');
$base_page = 'admin.php?page='.$base_name;

### Form Processing
if(!empty($_POST['do'])) {
	// Decide What To Do
	switch($_POST['do']) {
		// Add Poll
		case __('Add Poll', 'wp-polls'):
			check_admin_referer('wp-polls_add-poll');
			// Poll Question
			$pollq_question = addslashes(trim($_POST['pollq_question']));
			if( ! empty( $pollq_question ) ) {
				// Poll Start Date
				$timestamp_sql = '';
				$pollq_timestamp_day = intval($_POST['pollq_timestamp_day']);
				$pollq_timestamp_month = intval($_POST['pollq_timestamp_month']);
				$pollq_timestamp_year = intval($_POST['pollq_timestamp_year']);
				$pollq_timestamp_hour = intval($_POST['pollq_timestamp_hour']);
				$pollq_timestamp_minute = intval($_POST['pollq_timestamp_minute']);
				$pollq_timestamp_second = intval($_POST['pollq_timestamp_second']);
				$pollq_timestamp = gmmktime($pollq_timestamp_hour, $pollq_timestamp_minute, $pollq_timestamp_second, $pollq_timestamp_month, $pollq_timestamp_day, $pollq_timestamp_year);
				if ($pollq_timestamp > current_time('timestamp')) {
					$pollq_active = -1;
				} else {
					$pollq_active = 1;
				}
				// Poll End Date
				$pollq_expiry_no = intval($_POST['pollq_expiry_no']);
				if ($pollq_expiry_no == 1) {
					$pollq_expiry = '';
				} else {
					$pollq_expiry_day = intval($_POST['pollq_expiry_day']);
					$pollq_expiry_month = intval($_POST['pollq_expiry_month']);
					$pollq_expiry_year = intval($_POST['pollq_expiry_year']);
					$pollq_expiry_hour = intval($_POST['pollq_expiry_hour']);
					$pollq_expiry_minute = intval($_POST['pollq_expiry_minute']);
					$pollq_expiry_second = intval($_POST['pollq_expiry_second']);
					$pollq_expiry = gmmktime($pollq_expiry_hour, $pollq_expiry_minute, $pollq_expiry_second, $pollq_expiry_month, $pollq_expiry_day, $pollq_expiry_year);
					if ($pollq_expiry <= current_time('timestamp')) {
						$pollq_active = 0;
					}
				}
				// Mutilple Poll
				$pollq_multiple_yes = intval($_POST['pollq_multiple_yes']);
				$pollq_multiple = 0;
				if ($pollq_multiple_yes == 1) {
					$pollq_multiple = intval($_POST['pollq_multiple']);
				} else {
					$pollq_multiple = 0;
				}
				// Insert Poll
				$add_poll_question = $wpdb->query("INSERT INTO $wpdb->pollsq VALUES (0, '$pollq_question', '$pollq_timestamp', 0, $pollq_active, '$pollq_expiry', $pollq_multiple, 0)");
				if (!$add_poll_question) {
					$text .= '<p style="color: red;">' . sprintf(__('Error In Adding Poll \'%s\'.', 'wp-polls'), stripslashes($pollq_question)) . '</p>';
				}
				// Add Poll Answers
				$polla_answers = $_POST['polla_answers'];
				$polla_qid = intval($wpdb->insert_id);
				foreach ($polla_answers as $polla_answer) {
					$polla_answer = addslashes(trim($polla_answer));
					if( ! empty( $polla_answer ) ) {
						$add_poll_answers = $wpdb->query("INSERT INTO $wpdb->pollsa VALUES (0, $polla_qid, '$polla_answer', 0)");
						if (!$add_poll_answers) {
							$text .= '<p style="color: red;">' . sprintf(__('Error In Adding Poll\'s Answer \'%s\'.', 'wp-polls'), stripslashes($polla_answer)) . '</p>';
						}
					} else {
						$text .= '<p style="color: red;">' . __( 'Poll\'s Answer is empty.', 'wp-polls' ) . '</p>';
					}
				}
				// Update Lastest Poll ID To Poll Options
				$latest_pollid = polls_latest_id();
				$update_latestpoll = update_option('poll_latestpoll', $latest_pollid);
				if ( empty( $text ) ) {
					$text = '<p style="color: green;">' . sprintf( __( 'Poll \'%s\' (ID: %s) added successfully. Embed this poll with the shortcode: %s or go back to <a href="%s">Manage Polls</a>', 'wp-polls' ), stripslashes( $pollq_question ), $latest_pollid, '<input type="text" value=\'[poll id="' . $latest_pollid . '"]\' readonly="readonly" size="10" />', $base_page ) . '</p>';
				} else {
					if( $add_poll_question ) {
						$text .= '<p style="color: green;">' . sprintf( __( 'Poll \'%s\' (ID: %s) (Shortcode: %s) added successfully, but there are some errors with the Poll\'s Answers. Embed this poll with the shortcode: %s or go back to <a href="%s">Manage Polls</a>', 'wp-polls' ), stripslashes( $pollq_question ), $latest_pollid, '<input type="text" value=\'[poll id="' . $latest_pollid . '"]\' readonly="readonly" size="10" />' ) .'</p>';
					}
				}
				do_action( 'wp_polls_add_poll', $latest_pollid );
				cron_polls_place();
			} else {
				$text .= '<p style="color: red;">' . __( 'Poll Question is empty.', 'wp-polls' ) . '</p>';
			}
			break;
	}
}

$response = (object)array();
$response->msg = $text;
if (!empty($latest_pollid)) 
	$response->id = $latest_pollid;

?>