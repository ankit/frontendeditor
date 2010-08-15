<?php
/**
 * @version		$Id: module.php 388 2009-06-02 15:06:55Z ankit.ahuja $
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
 * Frontendeditor Module Controller
 * 
 * @package Joomla.Site
 * @subpackage Frontendeditor
 */

defined('_JEXEC') or die('Restricted Access');

jimport('joomla.application.component.controller');

class FrontendeditorControllerModule extends JController{

	/**
	 * Save module title
	 * @access public
	 */
	
	function saveTitle()
	{		
		//Check for request forgeries
		JRequest::checkToken('get') or jexit(JText::_('false'));
		
		//Get the current logged in user
		$user = &JFactory::getUser();
		
		//Get the model
		$model = &$this->getModel('module');
		
		//Create the response
		$view = &$this->getView('response','raw');
		
		//Create a user access object for the user
		$access = new stdClass();
		
		//for j!1.5
		$access->canEdit = $user->authorize('com_modules','manage');
		
		if(!$access->canEdit)
		{
			$view->sendResponse(false);
		}
				
		//Get the data from the request
		$post = JRequest::get('post');
		
		$post['value'] = JRequest::getVar('value', '', 'post', 'string', JREQUEST_ALLOWHTML);
		$post['value'] = addslashes($post['value']);
		
		$response = $model->storeTitle($post);
		
		//Display the view
		$view->sendResponse($response);
	}
	
	/**
	 * Edit a module
	 * @param string The current GET/POST option
	 * @param int 	 The unique id of the record to edit
	 */
	function edit()
	{
		// Initialize some variables
		$db 	=& JFactory::getDBO();
		$user 	=& JFactory::getUser();

		//Create a user access object for the user
		$access = new stdClass();
		
		//for j!1.5
		$access->canEdit = $user->authorize('com_modules','manage');
		
		if(!$access->canEdit)
		{
			JError::raiseError(403,JText::_("ALERTNOTAUTH"));
		}

		$client	=& JApplicationHelper::getClientInfo(JRequest::getVar('client', '0', '', 'int'));
		$module = JRequest::getVar( 'module', '', '', 'cmd' );
		$id 	= JRequest::getVar( 'id', 0, 'method', 'int' );
		$cid 	= JRequest::getVar( 'cid', array( $id ), 'method', 'array' );
		JArrayHelper::toInteger($cid, array(0));

		$model	= &$this->getModel('module');
		$model->setState( 'id',			$cid[0] );
		$model->setState( 'clientId',	$client->id );

		$lists 	= array();
		$row 	=& JTable::getInstance('module');
		// load the row from the db table
		$row->load( (int) $cid[0] );
		// fail if checked out not by 'me'
		if ($row->isCheckedOut( $user->get('id') )) {
			$this->setRedirect( 'index.php');
			return JError::raiseWarning( 500, JText::sprintf( 'DESCBEINGEDITTED', JText::_( 'The module' ), $row->title ) );
		}

		$row->content = htmlspecialchars($row->content, ENT_COMPAT, 'UTF-8');

		if ( $cid[0] ) {
			$row->checkout( $user->get('id') );
		}
		// if a new record we must still prime the JTableModel object with a default
		// position and the order; also add an extra item to the order list to
		// place the 'new' record in last position if desired
		if ($cid[0] == 0) {
			$row->position 	= 'left';
			$row->showtitle = true;
			$row->published = 1;
			//$row->ordering = $l;

			$row->module 	= $module;
		}

		if ($client->id == 1)
		{
			$where 				= 'client_id = 1';
			$lists['client_id'] = 1;
			$path				= 'mod1_xml';
		}
		else
		{
			$where 				= 'client_id = 0';
			$lists['client_id'] = 0;
			$path				= 'mod0_xml';
		}

		$query = 'SELECT position, ordering, showtitle, title'
		. ' FROM #__modules'
		. ' WHERE '. $where
		. ' ORDER BY ordering'
		;
		$db->setQuery( $query );
		$orders = $db->loadObjectList();
		if ($db->getErrorNum()) {
			echo $db->stderr();
			return false;
		}

		$orders2 	= array();

		$l = 0;
		$r = 0;
		for ($i=0, $n=count( $orders ); $i < $n; $i++) {
			$ord = 0;
			if (array_key_exists( $orders[$i]->position, $orders2 )) {
				$ord =count( array_keys( $orders2[$orders[$i]->position] ) ) + 1;
			}

			$orders2[$orders[$i]->position][] = JHTML::_('select.option',  $ord, $ord.'::'.addslashes( $orders[$i]->title ) );
		}

		// get selected pages for $lists['selections']
		if ( $cid[0] ) {
			$query = 'SELECT menuid AS value'
			. ' FROM #__modules_menu'
			. ' WHERE moduleid = '.(int) $row->id
			;
			$db->setQuery( $query );
			$lookup = $db->loadObjectList();
			if (empty( $lookup )) {
				$lookup = array( JHTML::_('select.option',  '-1' ) );
				$row->pages = 'none';
			} elseif (count($lookup) == 1 && $lookup[0]->value == 0) {
				$row->pages = 'all';
			} else {
				$row->pages = null;
			}
		} else {
			$lookup = array( JHTML::_('select.option',  0, JText::_( 'All' ) ) );
			$row->pages = 'all';
		}

		if ( $row->access == 99 || $row->client_id == 1 || $lists['client_id'] ) {
			$lists['access'] 			= 'Administrator';
			$lists['showtitle'] 		= 'N/A <input type="hidden" name="showtitle" value="1" />';
			$lists['selections'] 		= 'N/A';
		} else {
			if ( $client->id == '1' ) {
				$lists['access'] 		= 'N/A';
				$lists['selections'] 	= 'N/A';
			} else {
				$lists['access'] 		= JHTML::_('list.accesslevel',  $row );

				$selections				= JHTML::_('menu.linkoptions');
				$lists['selections']	= JHTML::_('select.genericlist',   $selections, 'selections[]', 'class="inputbox" size="15" multiple="multiple"', 'value', 'text', $lookup, 'selections' );
			}
			$lists['showtitle'] = JHTML::_('select.booleanlist',  'showtitle', 'class="inputbox"', $row->showtitle );
		}

		// build the html select list for published
		$lists['published'] = JHTML::_('select.booleanlist',  'published', 'class="inputbox"', $row->published );

		$row->description = '';

		$lang =& JFactory::getLanguage();
		if ( $client->id != '1' ) {
			$lang->load( trim($row->module), JPATH_SITE );
		} else {
			$lang->load( trim($row->module) );
		}

		// xml file for module
		if ($row->module == 'custom') {
			$xmlfile = JApplicationHelper::getPath( $path, 'mod_custom' );
		} else {
			$xmlfile = JApplicationHelper::getPath( $path, $row->module );
		}

		$data = JApplicationHelper::parseXMLInstallFile($xmlfile);
		if ($data)
		{
			foreach($data as $key => $value) {
				$row->$key = $value;
			}
		}

		// get params definitions
		$params = new JParameter( $row->params, $xmlfile, 'module' );
		
		$view = &$this->getView('module','html');
		$view->setModel($model,true);
		$view->edit($row,$orders2,$lists,$params,$client,$model);
	}
	
	/**
	 * Saves the module after an edit form submit
	 */
	function save()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		global $mainframe;

		// Initialize some variables
		$db		=& JFactory::getDBO();
		$client	=& JApplicationHelper::getClientInfo(JRequest::getVar('client', '0', '', 'int'));

		$post	= JRequest::get( 'post' );
		// fix up special html fields
		$post['content']   = JRequest::getVar( 'content', '', 'post', 'string', JREQUEST_ALLOWRAW );
		$post['client_id'] = $client->id;
		
		$this->setRedirect('index.php');

		$row =& JTable::getInstance('module');

		if (!$row->bind( $post, 'selections' )) {
			return JError::raiseWarning( 500, $row->getError() );
		}

		if (!$row->check()) {
			return JError::raiseWarning( 500, $row->getError() );
		}

		// if new item, order last in appropriate group
		if (!$row->id) {
			$where = 'position='.$db->Quote( $row->position ).' AND client_id='.(int) $client->id ;
			$row->ordering = $row->getNextOrder( $where );
		}

		if (!$row->store()) {
			return JError::raiseWarning( 500, $row->getError() );
		}
		$row->checkin();

		$menus = JRequest::getVar( 'menus', '', 'post', 'word' );
		$selections = JRequest::getVar( 'selections', array(), 'post', 'array' );
		JArrayHelper::toInteger($selections);

		// delete old module to menu item associations
		$query = 'DELETE FROM #__modules_menu'
		. ' WHERE moduleid = '.(int) $row->id
		;
		$db->setQuery( $query );
		if (!$db->query()) {
			return JError::raiseWarning( 500, $db->getError() );
		}

		// check needed to stop a module being assigned to `All`
		// and other menu items resulting in a module being displayed twice
		if ( $menus == 'all' ) {
			// assign new module to `all` menu item associations
			$query = 'INSERT INTO #__modules_menu'
			. ' SET moduleid = '.(int) $row->id.' , menuid = 0'
			;
			$db->setQuery( $query );
			if (!$db->query()) {
				return JError::raiseWarning( 500, $db->getError() );
			}
		}
		else
		{
			foreach ($selections as $menuid)
			{
				// this check for the blank spaces in the select box that have been added for cosmetic reasons
				if ( (int) $menuid >= 0 ) {
					// assign new module to menu item associations
					$query = 'INSERT INTO #__modules_menu'
					. ' SET moduleid = '.(int) $row->id .', menuid = '.(int) $menuid
					;
					$db->setQuery( $query );
					if (!$db->query()) {
						return JError::raiseWarning( 500, $db->getError() );
					}
				}
			}
		}

		// clean cache for all 3 front-end user groups (guest, reg, special)
		$cache =& JFactory::getCache();
		$cache->remove($row->id . '0', $row->module);
		$cache->remove($row->id . '1', $row->module);
		$cache->remove($row->id . '2', $row->module);
		// clean content cache because of loadposition plugin
		$cache->clean( 'com_content' );
	}	
	
	/**
	 * Reset the order of the positions affected by the new ordering of the module
	 * @param string The current GET/POST option
	 * @param string The ordered string visible modules' ids for a position ( separated by , )
	 */
	function saveOrder()
	{
		// Check for request forgeries
		JRequest::checkToken('get') or jexit(JText::_('false'));
		
		// Initialize some variables
		$db		=& JFactory::getDBO();
		
		$model  =& $this->getModel('module');
		
		$view = &$this->getView('response','raw');
				
		$response = false;
		
		$client	=& JApplicationHelper::getClientInfo(JRequest::getVar('client', '0', '', 'int'));

		$cid 	= JRequest::getInt('cid');
		$position = JRequest::getString('position');
		
		$row =& JTable::getInstance('module');
		
		$order 	= JRequest::getString('order');
		$order  = explode(',',$order);
		JArrayHelper::toInteger($order);
	
		$modules = $model->getModulesForPosition($position);
		
		// Reset the ordering values of the modules starting from 0
		$modules = $model->resetOrder($modules,$position);
		
		// get the visible modules in the current view
		$visible = array();
		foreach($modules as $key => $module)
		{
			foreach($order as $modid)
			{
				if($module->id == $modid)
				{
					array_push($visible,$module);
					break;
				}
			}
		}
		
		$row->load((int) $cid);
		// if the module is reordered in the same position
		if($row->position == $position)
		{
			foreach($order as $key => $module_id)
			{
				$row->load((int) $module_id);
				$row->ordering = $visible[$key]->ordering;
				if (!$row->store()) {
					$view->sendResponse(false);
				}
				$response = true;
			}
		}
		// if the module is moved to a new position
		else
		{	
			$old_position = null;
			$prev_mod_id = null;
			// Place the module in the new position with appropriate ordering value
			foreach($order as $key => $module_id)
			{
				if( $module_id == $cid )
				{	
					$row->load((int) $cid);
					$old_position = $row->position;
					$row->position = $position;
					if($key == 0)
					{
						$row->ordering = 0;
						$previous_mod_id = null;
					}
					else
					{
						$row->ordering = $visible[$key - 1]->ordering + 1;
						$prev_mod_id = $visible[$key - 1]->id;
					}
					
					if (!$row->store()) {
						$view->sendResponse(false);
					}
					break;
				}
			}	

			$len = count($modules);
			$new_key = -1;
			if($prev_mod_id)
			{
				foreach($modules as $key => $module)
				{
					if($module->id == $prev_mod_id)
					{
						$new_key = $key;
						break;
					}
				}
			}
			$response = $new_key;
			// Increase the ordering value of all the following modules by 1			
			for($i = $new_key + 1; $i < $len ; $i ++)
			{
				$module = $modules[$i];
				$row->load((int) $module->id);
				$row->ordering += 1;
				if (!$row->store()) {
					$view->sendResponse(false);
				}
			}
			//Reset the ordering values of the modules in the previous position
			$modules = $model->getModulesForPosition($old_position);
			$model->resetOrder($modules,$old_position);
			
			$response = true;
		}
		$view->sendResponse($response); 
	}
	
	/**
	 * Cancels an edit operation
	 */
	function cancel()
	{
		global $mainframe;

		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// Initialize some variables
		$db		=& JFactory::getDBO();
		$client	=& JApplicationHelper::getClientInfo(JRequest::getVar('client', '0', '', 'int'));

		$row =& JTable::getInstance('module');
		// ignore array elements
		$row->bind(JRequest::get('post'), 'selections params' );
		$row->checkin();
	}
}
?>