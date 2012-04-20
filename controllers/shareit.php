<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Z_Share Controller
 * Facebook Cross Domain Receiver File
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author	   Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module	   XD_Receiver Controller	
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
* 
*/

class Shareit_Controller extends Controller {
	
	public function index()
	{
		$view = new View("facebook-share");
		$view->render(TRUE);
	}
}