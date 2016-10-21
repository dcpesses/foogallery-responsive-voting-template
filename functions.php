<?php



### Function: Check Voted By Cookie
if(!function_exists('check_voted_cookie')) {
	function check_voted_cookie($poll_id) {
		if(!empty($_COOKIE["voted_$poll_id"])) {
			$get_voted_aids = explode(',', $_COOKIE["voted_$poll_id"]);
		} else {
			$get_voted_aids = 0;
		}
		return $get_voted_aids;
	}
}