<?php
/**
 * @version		$Id: view.html.php 388 2009-06-02 15:06:55Z ankit.ahuja $
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

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

/**
 * HTML Module View class for the Frontendeditor Component
 * 
 * @package Joomla.Site
 * @subpackage Frontendeditor
 */
class FrontendeditorViewModule extends JView
{
	function edit(&$row,&$orders2,&$lists,&$params,$client,$model)
	{
		$uri = JFactory::getURI();
		$editor = JFactory::getEditor();
		$data = $this->getFRSettings();
		
		$this->assignRef('adv_params',$data->moduleparams_visible);
		$this->assignRef('lists', $lists);
		$this->assignRef('row', $row);
		$this->assignRef('orders2', $orders2);
		$this->assignRef('params', $params);
		$this->assignRef('client', $client);
		$this->assignRef('editor', $editor);
		$this->assignRef('model', $model);
		$this->assign('action', $uri->toString());
		
		parent::display();
	}
	
	function getFRSettings()
	{
		$db =& JFactory::getDBO();
		$query = "SELECT * FROM #__frsettings WHERE id = 1";
		$db->setQuery($query);
 		$data = $db->loadObject();
		return $data;
	}
}
?>