<?php
/**
 * @version		$Id: view.html.php 388 2009-06-02 15:06:55Z ankit.ahuja $
 * @package		Joomla.Administrator
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
 * HTML Frontendeditor View Class for the Frontendeditor Component
 * 
 * @package 	Joomla.Administrator
 * @subpackage 	Frontendeditor
 */
class FrontendeditorViewFrontendeditor extends JView
{
	function display($tpl = null)
	{
		$data =& $this->get('data');
		$lists = $this->_buildEditLists($data);
		$document =& JFactory::getDocument();
		JToolBarHelper::title(JText::_('Enhanced Frontend Editing Manager'),'generic.png');
	 	JToolBarHelper::save();
		JToolBarHelper::cancel();
		JToolBarHelper::help('help','help');
        $document->addScript(JURI::root(true)."/administrator/components/com_frontendeditor/assets/jscolor.js");
		$this->assignRef('data', $data);
		$this->assignRef('lists', $lists);
		parent::display($tpl);
	}
	
	function _buildEditLists($data)
	{
		$lists['toggle'] = JHTML::_('select.booleanlist', 'toggle', '',$data->toggle,'On','Off', '');
		$lists['module'] = JHTML::_('select.booleanlist', 'module_edit', '', $data->module_edit);
		$lists['menuitem'] = JHTML::_('select.booleanlist', 'menuitem_edit', '', $data->menuitem_edit);
		$lists['dragdrop'] = JHTML::_('select.booleanlist', 'dragdrop_edit', '', $data->dragdrop_edit);
		$lists['color'] = JHTML::_('select.booleanlist', 'color_edit', '',$data->color_edit,'Choose your own color:','Let template decide', '');
		$lists['module_params'] = JHTML::_('select.booleanlist', 'moduleparams_visible', '', $data->moduleparams_visible);
		$lists['alias'] = JHTML::_('select.booleanlist', 'alias_edit', '', $data->alias_edit);
		return $lists;
	}
}
?>