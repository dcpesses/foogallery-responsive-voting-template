//FooGallery Responsive Voting template script
//Add any javascript that will be needed by your gallery template. This will be output to the frontend

// Variables
var poll_id = 0;
var poll_answer_id = '';
var is_being_voted = false;
pollsL10n.show_loading = parseInt(pollsL10n.show_loading, 10);
pollsL10n.show_fading = parseInt(pollsL10n.show_fading, 10);

// When User Vote For Poll
function poll_vote(current_poll_id) {
	jQuery(document).ready(function($) {
		if(!is_being_voted) {
			set_is_being_voted(true);
			poll_id = current_poll_id;
			poll_answer_id = '';
			$('#polls_form_' + poll_id + ' input:checkbox, #polls_form_' + poll_id + ' input:radio, #polls_form_' + poll_id + ' option').each(function(i){
				if ($(this).is(':checked') || $(this).is(':selected')) {
					poll_answer_id = parseInt($(this).val(), 10);
				}
			});
			if(poll_answer_id > 0) {
				poll_process();
			} else {
				set_is_being_voted(false);
				alert(pollsL10n.text_valid);
			}
		} else {
			alert(pollsL10n.text_wait);
		}
	});
}

// Process Poll (User Click "Vote" Button)
function poll_process() {
	jQuery(document).ready(function($) {
		poll_nonce = $('#poll_' + poll_id + '_nonce').val();
		if(pollsL10n.show_fading) {
			$('#polls-' + poll_id).fadeTo('def', 0);
		}
		if(pollsL10n.show_loading) {
			$('#polls-' + poll_id + '-loading').show();
		}
		$.ajax({type: 'POST', xhrFields: {withCredentials: true}, url: pollsL10n.ajax_url, data: 'action=polls&view=process&poll_id=' + poll_id + '&poll_' + poll_id + '=' + poll_answer_id + '&poll_' + poll_id + '_nonce=' + poll_nonce, cache: false, success: poll_process_success});
	});
}

// Poll's Result (User Click "View Results" Link)
function poll_result(current_poll_id) {
	jQuery(document).ready(function($) {
		if(!is_being_voted) {
			set_is_being_voted(true);
			poll_id = current_poll_id;
			poll_nonce = $('#poll_' + poll_id + '_nonce').val();
			if(pollsL10n.show_fading) {
				$('#polls-' + poll_id).fadeTo('def', 0);
			}
			if(pollsL10n.show_loading) {
				$('#polls-' + poll_id + '-loading').show();
			}
			$.ajax({type: 'POST', xhrFields: {withCredentials: true}, url: pollsL10n.ajax_url, data: 'action=polls&view=result&poll_id=' + poll_id + '&poll_' + poll_id + '_nonce=' + poll_nonce, cache: false, success: poll_result_success});
		} else {
			alert(pollsL10n.text_wait);
		}
	});
}

// Poll's Voting Booth  (User Click "Vote" Link)
function poll_booth(current_poll_id) {
	jQuery(document).ready(function($) {
		if(!is_being_voted) {
			set_is_being_voted(true);
			poll_id = current_poll_id;
			poll_nonce = $('#poll_' + poll_id + '_nonce').val();
			if(pollsL10n.show_fading) {
				$('#polls-' + poll_id).fadeTo('def', 0);
			}
			if(pollsL10n.show_loading) {
				$('#polls-' + poll_id + '-loading').show();
			}
			$.ajax({type: 'POST', xhrFields: {withCredentials: true}, url: pollsL10n.ajax_url, data: 'action=polls&view=booth&poll_id=' + poll_id + '&poll_' + poll_id + '_nonce=' + poll_nonce, cache: false, success: poll_booth_success});
		} else {
			alert(pollsL10n.text_wait);
		}
	});
}

// Poll Process Successfully
function poll_process_success(data) {
	jQuery(document).ready(function($) {
		
		$('.voting_submit_btn').hide();
		$('#thanks_for_voting').show();
		
		var baseurl = pollsL10n.ajax_url.substring(0, pollsL10n.ajax_url.indexOf("/wp-admin/admin-ajax.php")) 
						+ "/wp-content/plugins/foogallery-responsive-voting-template/";
		
		// data = data.replace(/__(\d+?)__/gi, plugins"")
		$('#polls-' + poll_id).replaceWith(data);
		if(pollsL10n.show_loading) {
			$('#polls-' + poll_id + '-loading').hide();
		}
		if(pollsL10n.show_fading) {
			$('#polls-' + poll_id).fadeTo('def', 1);
		}
		set_is_being_voted(false);

		if (!!window['results_callback']) window.results_callback();
	});
}

// Poll Process Successfully
function poll_result_success(data) {
	jQuery(document).ready(function($) {
		
		$('.voting_submit_btn').hide();
		
		var baseurl = pollsL10n.ajax_url.substring(0, pollsL10n.ajax_url.indexOf("/wp-admin/admin-ajax.php")) 
						+ "/wp-content/plugins/foogallery-responsive-voting-template/";
		
		// data = data.replace(/__(\d+?)__/gi, plugins"")
		$('#polls-' + poll_id).replaceWith(data);
		if(pollsL10n.show_loading) {
			$('#polls-' + poll_id + '-loading').hide();
		}
		if(pollsL10n.show_fading) {
			$('#polls-' + poll_id).fadeTo('def', 1);
		}
		set_is_being_voted(false);

		if (!!window['results_callback']) window.results_callback();
	});
}

// Poll Process Successfully
function poll_booth_success(data) {
	
	$('.voting_submit_btn').hide();
	$('#thanks_for_voting').show();
	
	jQuery(document).ready(function($) {
		var baseurl = pollsL10n.ajax_url.substring(0, pollsL10n.ajax_url.indexOf("/wp-admin/admin-ajax.php")) 
						+ "/wp-content/plugins/foogallery-responsive-voting-template/";
		
		// data = data.replace(/__(\d+?)__/gi, plugins"")
		$('#polls-' + poll_id).replaceWith(data);
		if(pollsL10n.show_loading) {
			$('#polls-' + poll_id + '-loading').hide();
		}
		if(pollsL10n.show_fading) {
			$('#polls-' + poll_id).fadeTo('def', 1);
		}
		set_is_being_voted(false);
		if (!!window['results_callback']) window.results_callback();
	});
}

// Set is_being_voted Status
function set_is_being_voted(voted_status) {
	is_being_voted = voted_status;
}

/*
original voting code

var jqForm = "form[name=votingForm]",
	jqCostume = "div.costume",
	jqCostumeNameContainer = "label",
	jqCostumePercentContainer = ".percent",
	jqCostumeRadio = jqCostume + " input[type=radio]",
	//
	requestGetAnswers = {
		data: {
			pollresult: wpPoll_id
		},
		success: processResults,
		type: "GET",
		url: serviceURL
	},
	requestGetOptions = {
		data: {
			pollbooth: wpPoll_id
		},
		success: processOptions,
		type: "GET",
		url: serviceURL
	},
	requestPostAnswer = {
		data: {
			poll_id: wpPoll_id,
			vote: "true"
		},
		success: processResults,
		type: "POST",
		url: serviceURL
	};
if (!$ && !!jQuery) window.$ = jQuery;


function processOptions(data) {
	"use strict";
	$("input[type=radio]", data).each(function() {
		var characterName = $(this).siblings("label").html(),
			inputValue = $(this).val();

		$(jqCostume + " " + jqCostumeNameContainer + ":contains(" + characterName + ")").siblings("input").val(inputValue);
	});
}



function processResults(data) {
	"use strict";
	$("div", data).each(function() {
		var
		characterName = $(this).text(),
			pollPercent = $(this).find("small").html();
		characterName = characterName.substr(0, characterName.length - pollPercent.length - 1);
		pollPercent = pollPercent.substring(1, pollPercent.length - 2);

		var el = $(jqCostume + " " + jqCostumeNameContainer + ":contains(" + characterName + ")");
		el.siblings(jqCostumePercentContainer).html(pollPercent + "%").hide().fadeIn(null, function() {
			pollPercent = pollPercent == '1' ? '2' : pollPercent; // lets help out the 1%, visually
			el.siblings(".percent-bar").find('div').animate({
				width: pollPercent + '%',
				opacity: 1
			}, 1000);
		});
	});
}

function getCookie(c_name) {
	var i, x, y, ARRcookies = document.cookie.split(";");
	for (i = 0; i < ARRcookies.length; i++) {
		x = ARRcookies[i].substr(0, ARRcookies[i].indexOf("="));
		y = ARRcookies[i].substr(ARRcookies[i].indexOf("=") + 1);
		x = x.replace(/^\s+|\s+$/g, "");
		if (x == c_name) {
			return unescape(y);
		}
	}
}

function initPoll() {
	"use strict";
	var cookie = getCookie("voted_"+wpPoll_id);
	if (cookie != null) {

		$(jqCostumeRadio).hide();
		$(jqForm + " input[type=submit]").hide();
		$.ajax(requestGetAnswers);
	} else {
		$(jqCostumeRadio).attr("name", "poll_" + wpPoll_id);
		$(jqCostumeRadio).click(function() {
			$(this).unbind('click');
			$(jqForm + " input[type=submit]").removeAttr('disabled');
		});
		$(jqForm).submit(function(event) {
			"use strict";
			event.preventDefault();
			$(jqCostumeRadio).fadeOut();
			$(jqForm + " input[type=submit]").fadeOut(null, function() {
				$('img.end-message').fadeIn();
			});
			requestPostAnswer.data["poll_" + wpPoll_id] = $(jqForm + " input[name=poll_" + wpPoll_id + "]:checked").val();
			$.ajax(requestPostAnswer);
		});

		$.ajax(requestGetOptions);
	}
}
*/

function updateResults(poll_answer) {
	
	var poll_answer_selected = jQuery('li.poll-answer-' + poll_answer).attr('data-selected'),
		poll_answer_percentage = jQuery('li.poll-answer-' + poll_answer).attr('data-percentage'),
		poll_answer_votes = jQuery('li.poll-answer-' + poll_answer).attr('data-votes');
	
	if (poll_answer_selected) 
		jQuery('#item-' + poll_answer + ' label').css({'font-style':'italic'});
	
	jQuery('#result-block-' + poll_answer + ' .percent')
		.html(poll_answer_percentage+'%')
		.css({'display':'block'});
	jQuery('#result-block-' + poll_answer + ' .percent-bar .pollbar')
		.css({'width': poll_answer_percentage+'%', 'opacity': 1})
		.attr('title', ((poll_answer_selected) ? 'You Have Voted For This Choice - ' : '') + '(' +poll_answer_percentage+'% | '+poll_answer_votes+' Votes)'); 
	jQuery('li.poll-answer-' + poll_answer + ' span')
		.text( jQuery('#item-' + poll_answer + ' label').text() );
	if (document.getElementById('poll-answer-' + poll_answer))
		document.getElementById('poll-answer-' + poll_answer).remove();

}

jQuery(function ($) {
    $('.foogallery-responsive-voting').each(function() {
        var $gallery = $(this);
		$gallery.on('click', '.item .poll-answer-radio', function(e){
			
			if (e.target.checked==false) e.target.checked=true;
			// e.preventDefault();
			// e.stopPropagation();
			$('.highlighted').removeClass('highlighted');
			// console.log(e);
			$(this)
				.closest('.item').addClass('highlighted');
				// .find('input[type=radio]').prop("checked", true);
			$('.voting_submit_btn.btn').prop('disabled', false);
		});
        $gallery.imagesLoaded( function() {
			if (!!window['Masonry'] && $gallery.attr('data-masonry-options')) {
				$gallery.removeClass('foogallery-responsive-voting-loading').masonry( $gallery.data('masonry-options') );
			//	console.log('masonry init');
			} else {
				$gallery.removeClass('foogallery-responsive-voting-loading');
			}
        });
		// if (!!wpPoll_id) initPoll();
    });
});

