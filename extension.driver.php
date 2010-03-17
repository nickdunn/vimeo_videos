<?php
	
	class extension_vimeo_videos extends Extension {
		public function about() {
			return array(
				'name'			=> 'Vimeo Videos',
				'version'		=> '0.1',
				'release-date'	=> '2009-02-23',
				'author'		=> array(
					'name'			=> 'Nick Dunn',
					'website'		=> 'http://airlock.com',
					'email'			=> 'nick.dunn@airlock.com'
				),
				'description'	=> 'Cache a video from Vimeo.'
			);
		}
		
		public function getSubscribedDelegates(){
			return array(
				array(
					'page' => '/administration/',
					'delegate' => 'AdminPagePreGenerate',
					'callback' => '__appendJavaScript'
				)
			);
		}
		
		public function __appendJavaScript($context){

			if(isset(Administration::instance()->Page->_context['section_handle']) && in_array(Administration::instance()->Page->_context['page'], array('new', 'edit'))){
				Administration::instance()->Page->addStylesheetToHead(URL . '/extensions/vimeo_videos/assets/vimeo_video.css', 'screen', 190);
				Administration::instance()->Page->addScriptToHead(URL . '/extensions/vimeo_videos/assets/vimeo_video.js', 195);
			}
		}
		
		public function fetchNavigation(){			
			return array(
				array(
					'location'	=> __('System'),
					'name'	=> 'Vimeo Videos',
					'link'	=> '/videos/'
				)
			);		
		}
		
		public function uninstall() {
			$this->_Parent->Database->query("DROP TABLE `tbl_fields_vimeo_video`");
		}
		
		public function install() {
			return $this->_Parent->Database->query("
				CREATE TABLE `tbl_fields_vimeo_video` (
					`id` int(11) unsigned NOT NULL auto_increment,
					`field_id` int(11) unsigned NOT NULL,
					`refresh` int(11) unsigned NOT NULL,
					PRIMARY KEY (`id`),
					KEY `field_id` (`field_id`)
				)
			");
		}
	}
	
?>