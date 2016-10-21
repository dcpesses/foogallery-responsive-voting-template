<?php
//This init class is used to add the extension to the extensions list while you are developing them.
//When the extension is added to the supported list of extensions, this file is no longer needed.

if ( !class_exists( 'Responsive_Voting_Template_FooGallery_Extension_Init' ) ) {
	class Responsive_Voting_Template_FooGallery_Extension_Init {

		function __construct() {
			add_filter( 'foogallery_available_extensions', array( $this, 'add_to_extensions_list' ) );
		}

		function add_to_extensions_list( $extensions ) {
			$extensions[] = array(
				'slug'=> 'responsive-voting',
				'class'=> 'Responsive_Voting_Template_FooGallery_Extension',
				'title'=> __('Responsive Voting Gallery', 'foogallery-responsive-voting'),
				'file'=> 'foogallery-responsive-voting-extension.php',
				'description'=> __('Responsive thumbnail template with wp-polls plugin integration', 'foogallery-responsive-voting'),
				'author'=> 'Danny Pesses',
				'author_url'=> '',
				'thumbnail'=> RESPONSIVE_VOTING_TEMPLATE_FOOGALLERY_EXTENSION_URL . '/assets/extension_bg.png',
				'tags'=> array( __('template', 'foogallery') ),	//use foogallery translations
				'categories'=> array( __('Build Your Own', 'foogallery') ), //use foogallery translations
				'source'=> 'generated'
			);

			return $extensions;
		}
	}

	new Responsive_Voting_Template_FooGallery_Extension_Init();
}