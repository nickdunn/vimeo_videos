<?php

	require_once(EXTENSIONS . '/asdc/lib/class.asdc.php');
	
	require_once(EXTENSIONS . '/vimeo_videos/lib/vimeo_helper.php');

	require_once(TOOLKIT . '/class.administrationpage.php');
	require_once(TOOLKIT . '/class.sectionmanager.php');
	require_once(TOOLKIT . '/class.fieldmanager.php');
	

	Class contentExtensionVimeo_VideosVideos extends AdministrationPage{

		protected $driver;

		function __construct(&$parent){
			parent::__construct($parent);
			$this->setTitle('Symphony &ndash; Vimeo Videos');
		}
		
		public function Database($enableProfiling=false){
			return ASDCLoader::instance($enableProfiling);
		}
		
		function comparePlays($a, $b) {
		    return $b->plays - $a->plays;
		}
	
		function view(){
			
			$vimeo_videos = array();
			
			$mode = 'view';
			if (isset($_POST['update'])) {
				$mode = 'update';
			}
			
			try {
				
				$vimeo_fields = $this->Database()->query("SELECT field_id FROM tbl_fields_vimeo_video");
				foreach($vimeo_fields as $field){

					$videos = $this->Database()->query(
						sprintf(
							"SELECT entry_id, clip_id, title, caption, plays, user_name, user_url, thumbnail_url FROM tbl_entries_data_%d",
							$field->field_id
						)
					);
					
					foreach($videos as $video){
						if ($mode == 'update') {
							VimeoHelper::updateClipInfo($video->clip_id, $field->field_id, $video->entry_id, $this->Database());							
						} else {
							array_push($vimeo_videos, $video);
						}						
					}
					
				}
				
			} catch(Exception $e) {
				print_r($this->Database()->lastError());
				die();
			}
			
			if ($mode == 'update') header('location: ' . URL . '/symphony/extension/vimeo_videos/videos/');
			
			usort($vimeo_videos, array($this, 'comparePlays'));
			
			$this->setPageType('table');
			
			$this->addStylesheetToHead(URL . '/extensions/vimeo_videos/assets/vimeo_video.css', 'screen', 190);
			
			$this->appendSubheading(__('Vimeo Videos (ordered by most plays)'));
			
			$aTableHead = array(
				array('', 'col'),
				array('Plays', 'col'),
				array('Description', 'col'),
				array('User', 'col'),
			);	

			$aTableBody = array();

			if (count($vimeo_videos) == 0) {
				$aTableBody = array(
					Widget::TableRow(array(Widget::TableData(__('None Found.'), 'inactive', NULL, count($aTableHead))))
				);
			} else {
				
				foreach($vimeo_videos as $video) {
					
					$thumbnail = Widget::TableData(
						Widget::Anchor(
						'<img src="' . URL . '/image/2/75/75/5/1/' . str_replace('http://', '', $video->thumbnail_url) .'" alt="' . $video->title .'" width="75" height="75"/>',
						"http://vimeo.com/{$video->clip_id}/",
						'View video'
						)
					);
					$description = Widget::TableData("<strong>" . $video->title . "</strong><br />" . $video->caption);
					$user = Widget::TableData(
						Widget::Anchor(
						$video->user_name,
						$video->user_url,
						'View user profile'
						)
					);
					$plays = Widget::TableData($video->plays);

					$aTableBody[] = Widget::TableRow(array($thumbnail, $plays, $description, $user));

				}
			}
						
			$table = Widget::Table(
				Widget::TableHead($aTableHead), 
				NULL, 
				Widget::TableBody($aTableBody)
			);

			$this->Form->appendChild($table);
			
			$actions = new XMLElement('div');
			$actions->setAttribute('class', 'actions');
			
			$actions->appendChild(Widget::Input(
				'update', __('Update video info'), 'submit'
			));

			$this->Form->appendChild($actions);
		}
	
	}
	
?>