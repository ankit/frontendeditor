<?php
/**
 * @version		$Id: menuitem.php 388 2009-06-02 15:06:55Z ankit.ahuja $
 * @package		Joomla.Site
 * @subpackage	Frontendeditor
 * @copyright	Copyright (C) 2009 Open Source Matters. All rights reserved.
 * @author 		Ankit Ahuja ( as part of Google Summer Of Code 2009 )
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open 
 * source software licenses. See COPYRIGHT.php for copyright notices and
 * details.
 */

/**
 * Frontendeditor Menuitem Controller
 * 
 * @package Joomla.Site
 * @subpackage Frontendeditor
 */

defined('_JEXEC') or die('Restricted Access');

jimport('joomla.application.component.controller');

class FrontendeditorControllerMenuitem extends JController
{
	/**
	 * Save menu item title
	 * @access public
	 */
	
	function saveTitle()
	{			
		//Check for request forgeries
		JRequest::checkToken('get') or jexit(JText::_('false'));
		
		//Get the current logged in user
		$user = &JFactory::getUser();
		
		//Get the model object
		$model = &$this->getModel('menuitem');
		
		//Get the view object
		$view = &$this->getView('response','raw');

		//Create a user access object for the user and check if the user has the necessary rights
		$access = new stdClass();
		
		//for j!1.5	
		$access->canEdit = $user->authorize('com_menus','manage');

		if(!$access->canEdit)
		{
			$view->sendResponse(false);
		}
		
		//get data from request
		$post = JRequest::get('post');

		$post['value'] = JRequest::getVar('value', '', 'post', 'string', JREQUEST_ALLOWHTML);
		$post['value'] = addslashes($post['value']);

		$response = $model->storeTitle($post);

		//Display the view
		$view->sendResponse($response);
	}
	
	/**
	 * Save page title 
	 * @access public
	 */
	
	function savePageTitle()
	{		
		//Check for request forgeries
		JRequest::checkToken('get') or jexit(JText::_('false'));
		
		//Get the current logged in user
		$user = &JFactory::getUser();
		
		//Get the model object
		$model = &$this->getModel('menuitem');		
		
		//Get the view object
		$view = &$this->getView('response','raw');
				
		//Create a user access object for the user and check if the user has the necessary rights
		$access = new stdClass();
		
		//for j!1.5	
		$access->canEdit = $user->authorize('com_menus','manage');

		if(!$access->canEdit)
		{
			$view->sendResponse(false);
		}
		
		//get data from request
		$post = JRequest::get('post');
		
		$post['value'] = JRequest::getVar('value', '', 'post', 'string', JREQUEST_ALLOWHTML);
		$post['value'] = addslashes($post['value']);

		$response = $model->storePageTitle($post);
				
		//Display the view
		$view->sendResponse($response);
	}
}
?>