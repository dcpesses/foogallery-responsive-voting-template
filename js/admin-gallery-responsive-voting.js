//Use this file to inject custom javascript behaviour into the foogallery edit page
//For an example usage, check out wp-content/foogallery/extensions/default-templates/js/admin-gallery-default.js

// (function (RESPONSIVE_VOTING_TEMPLATE_FOOGALLERY_EXTENSION, $, undefined) {
// 
// 	RESPONSIVE_VOTING_TEMPLATE_FOOGALLERY_EXTENSION.doSomething = function() {
// 		//do something when the gallery template is changed to responsive-voting
// 	};
// 
// 	RESPONSIVE_VOTING_TEMPLATE_FOOGALLERY_EXTENSION.adminReady = function () {
// 		$('body').on('foogallery-gallery-template-changed-responsive-voting', function() {
// 			RESPONSIVE_VOTING_TEMPLATE_FOOGALLERY_EXTENSION.doSomething();
// 		});
// 	};
// 
// }(window.RESPONSIVE_VOTING_TEMPLATE_FOOGALLERY_EXTENSION = window.RESPONSIVE_VOTING_TEMPLATE_FOOGALLERY_EXTENSION || {}, jQuery));
// 
// jQuery(function () {
// 	RESPONSIVE_VOTING_TEMPLATE_FOOGALLERY_EXTENSION.adminReady();
// });

if (!window['QTags']) {
	window.QTags = {};
	window.QTags.addButton = function(args1, args2, args3){};
	window.insertContent = function(args){};
}
(function (RESPONSIVE_VOTING_TEMPLATE_FOOGALLERY_EXTENSION, $, undefined) {

	RESPONSIVE_VOTING_TEMPLATE_FOOGALLERY_EXTENSION.setPreviewClasses = function() {

		var $previewImage = $('.foogallery-thumbnail-preview'),
			border_style = $('input[name="foogallery_settings[responsive-voting_border-style]"]:checked').val(),
			hover_effect = $('input[name="foogallery_settings[responsive-voting_hover-effect]"]:checked').val(),
		    hover_effect_type = $('input[name="foogallery_settings[responsive-voting_hover-effect-type]"]:checked').val();

		$previewImage.attr('class' ,'foogallery-thumbnail-preview foogallery-container foogallery-default foogallery-responsive-voting' + hover_effect + ' ' + border_style + ' ' + hover_effect_type);

		var $hoverEffectrow = $('.gallery_template_field-responsive-voting-hover-effect');
		if ( hover_effect_type === '' ) {
			$hoverEffectrow.show();
		} else {
			$hoverEffectrow.hide();
		}
	};

	RESPONSIVE_VOTING_TEMPLATE_FOOGALLERY_EXTENSION.adminReady = function () {
		$('body').on('foogallery-gallery-template-changed-responsive-voting', function() {
			RESPONSIVE_VOTING_TEMPLATE_FOOGALLERY_EXTENSION.setPreviewClasses();
		});

		$('input[name="foogallery_settings[responsive-voting_border-style]"], input[name="foogallery_settings[responsive-voting_hover-effect]"], input[name="foogallery_settings[responsive-voting_hover-effect-type]"]').change(function() {
			RESPONSIVE_VOTING_TEMPLATE_FOOGALLERY_EXTENSION.setPreviewClasses();
		});

		$('select[name="foogallery_settings[responsive-voting_voting-options]"]').change(function() {
			RESPONSIVE_VOTING_TEMPLATE_FOOGALLERY_EXTENSION.setPreviewClasses();
		});

		$('.foogallery-thumbnail-preview').click(function(e) {
			e.preventDefault();
		});
		
		$('input#FooGallerySettings_responsive-voting_voting-options').change(function() {
			if ( $(this).val() == "new" ) {
				alert('Please create a new poll question with the WP-Polls plugin and return here. (Functionality not yet implemented.)');
			/*	
				var result = prompt( "Please enter the name of your new poll:" );
				
				if ( result!=null && result!="" ) {
					
					// create ajax listener for endpoint (/photos/wp-admin/admin.php?page=wp-polls/polls-add.php)
					
					// submit to endpoint
					
					// on complete, inject option:
					
						// $("select#FooGallerySettings_responsive-voting_voting-options")
						// 	.prepend('<option value="'  + poll.question_id +  '">' + poll.question_name + '</option>');

				}
				*/
			}

		});
	};

}(window.RESPONSIVE_VOTING_TEMPLATE_FOOGALLERY_EXTENSION = window.RESPONSIVE_VOTING_TEMPLATE_FOOGALLERY_EXTENSION || {}, jQuery));

jQuery(function () {
	RESPONSIVE_VOTING_TEMPLATE_FOOGALLERY_EXTENSION.adminReady();
});