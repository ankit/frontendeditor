<?php
/**
 * @version		$Id: article.php 388 2009-06-02 15:06:55Z ankit.ahuja $
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
 * Frontendeditor Article Controller
 * 
 * @package Joomla.Site
 * @subpackage Frontendeditor
 */

defined('_JEXEC') or die('Restricted Access');

jimport('joomla.application.component.controller');

class FrontendeditorControllerArticle extends JController{

	/**
	 * Save article title 
	 * 
	 * @access public 
	 */
	
	function saveTitle()
	{
		//Check for request forgeries
		JRequest::checkToken('get') or jexit(JText::_('false'));
		
		//Get the current logged in user
		$user = &JFactory::getUser();
		
		//Get the view
		$view = &$this->getView('response','raw');
		
		//Create a user access object for the user
		$access = new stdClass();
		
		$access->canEdit		= $user->authorize('com_content', 'edit', 'content', 'all');
		$access->canEditOwn		= $user->authorize('com_content', 'edit', 'content', 'own');
		
		//Get the model
		$model = &$this->getModel('article');
		
		//Get the data from the request
		$post = JRequest::get('post');
		
		$post['value'] = JRequest::getVar('value', '', 'post', 'string', JREQUEST_ALLOWHTML);
		$post['value'] = addslashes($post['value']);		
		
		if(!($access->canEdit || $access->canEditOwn))
		{
			$view->sendResponse(false);
		}
		
		$response = $model->storeTitle($post);
		
		$view->sendResponse($response);
	}
}
?>