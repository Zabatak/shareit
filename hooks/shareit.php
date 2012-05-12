<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Facebook Social Hook - Load All Events
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author	   Ushahidi Team <team@ushahidi.com> 
 * @package	   Ushahidi - http://source.ushahididev.com
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license	   http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */

class ShareIt {
	/**
	 * Registers the main event add method
	 */
	public function __construct()
	{	
		// Hook into routing
		Event::add('system.pre_controller', array($this, 'add'));
	}
	
	/**
	 * Adds all the events to the main Ushahidi application
	 */
	public function add()
	{
		// Only add the events if we are on that controller
		if (Router::$controller == 'reports' AND Router::$method == 'view')
		{
			Event::add('ushahidi_action.report_extra', array($this, 'embed_addthis'));
			//Event::add('ushahidi_action.report_extra', array($this, 'embed_twitter'));
			Event::add('ushahidi_action.report_display_media', array($this, 'open_graph'));
			
			
			// Event::add('ushahidi_action.main_sidebar', array($this, 'hello'));
			
			// Overwrite current comments block and comments form block
			//Event::add('ushahidi_filter.comment_block', array($this, '_overwrite_comments'));
			//Event::add('ushahidi_filter.comment_form_block', array($this, '_overwrite_comments_form'));
		}
	}
	
	public function embed_addthis(){
		$view = View::factory('addthis');
		$view->render(TRUE);
	}
	
	public function embed_facebook()
	{
		$view = View::factory('facebook-share');
		$view->facebook_app_id = Kohana::config('facebook-social.facebook_api_key');
		$view->render(TRUE);
	}
	
	public function embed_twitter()
	{
		$view = View::factory('twitter-share');
		$view->render(TRUE);
	}
	
	
	public function open_graph(){
		
		$id = Event::$data;
		if ($id > 0 AND Incident_Model::is_valid_incident($id,TRUE))
		{
			$incident = ORM::factory('incident')
				->where('id',$id)
				->where('incident_active',1)
				->find();

			if ( ! $incident->loaded){
				return;
			}
		}
		
		
		//media variables
		$incident_news = array();
		$incident_video = array();
		$incident_photo = array();
		
		foreach($incident->media as $media)
			{
				if ($media->media_type == 4)
				{
					$incident_news[] = $media->media_link;
				}
				elseif ($media->media_type == 2)
				{
					$incident_video[] = $media->media_link;
				}
				elseif ($media->media_type == 1)
				{
					$incident_photo[] = array(
						'large' => url::convert_uploaded_to_abs($media->media_link),
						'thumb' => url::convert_uploaded_to_abs($media->media_thumb)
						);
				}
			}
		
		$og = array(
			'og:title' => $incident->incident_title,
			'og:url' => url::site() . 'reports/view/'.$id,
			'og:site_name' => Kohana::config('settings.site_name'),
		);		
		
		//create head meta markup
		$markup = '';
		echo '<script>';
		foreach($og as  $key => $value){
			$markup .= '<meta property="'.$key.'" content="'.$value.'" />';
		}
		
		
		//add image to head meta
		foreach ($incident_photo as $photo) {
			$markup .= '<meta property="og:image" content="'.$photo['large'].'" />';
		}
		
		//add video to head meta
		foreach ($incident_video as $video) {
			$markup .= '<meta property="og:video" content="'.$video.'" />';
		}
		
		echo "$('head').append('$markup');";
		echo '</script>';
	}
	
	
	public function scripts(){
		echo 'opengraph script ' . time();
	}
}

new ShareIt;