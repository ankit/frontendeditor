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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.model');

/**
 * Frontendeditor Component Menuitem Model
 * @package Joomla.Site
 * @subpackage Frontendeditor
 */

class FrontendeditorModelMenuitem extends JModel
{
	
	/**
	* Menu Item ID
	*
	* @var int
	*/
	var $_id = null;
	
	/**
	 * JTable object
	 *
	 * @var JTable
	 */
	var $_table = null;

	/**
	* Constructor
	* @access protected
	*/
	function __construct()
	{
		parent::__construct();
		$this->setId();
	}
	
	/** 
	 * Set Menu Item ID
	 */
	function setId()
	{
		$this->_id = JRequest::getInt('id',0,'');
	}
	
	/**
	 * Returns the internal table object
	 * @return JTable
	 */
	function &_getTable()
	{
		if ($this->_table == null) {
			$this->_table = &JTable::getInstance('menu');
		}
		return $this->_table;
	}

	/**
	 * Returns the Menu Item table object
	 * @return JTable
	 */
	function &getItem()
	{
		$table = &$this->_getTable();
		//Load the current menu item if it has been defined
		$table->load($this->_id);
		$item = $table;
		return $item;
	}

	/** 
	 * Get System parameters of Menu Item
	 * @return array System params of Menu Item
	 */
	function &getSystemParams()
	{
		// Initialize variables
		$params	= null;
		$item	= &$this->getItem();

		$params = new JParameter( $item->params );
		if ($item->type == 'component') {
			$path = JPATH_BASE.DS.'components'.DS.'com_menus'.DS.'models'.DS.'metadata'.DS.'component.xml';
			if (file_exists( $path )) {
				$xml =& JFactory::getXMLParser('Simple');
				if ($xml->loadFile($path)) {
					$document =& $xml->document;
					$params->setXML($document->getElementByPath('state/params'));
				}
			}
		}
		return $params;
	}

	/**
	 * Store the Menu Item title
	 * @access public
	 * @return string Menu Item title
	 */
	function storeTitle($data)
	{
		// Create database object
		$db = &JFactory::getDBO();
		
		// Getting the new title value
		$newtitle = $data['value'];

		// Creating the query to update menu item title
		$query = "UPDATE #__menu SET name = '".$newtitle."' WHERE id = ".$this->_id;
		$db->setQuery($query);
		if(!$db->query()){
			$this->setError($db->getErrorMsg());
			return false;
		}
		
		return $this->getTitle();
	}
	
	/**
	 * Get the Menu Item title
	 *
	 * @access public
	 * @return string Menu Item title
	 */
	function getTitle()
	{
		$db = &JFactory::getDBO();
		$query = "SELECT name FROM #__menu WHERE id = ".$this->_id;
		$db->setQuery($query);
		$response = $db->loadResult();
		if(!$response)
		{
			$this->setError($db->getErrorMsg());
			return false;
		}
		else
		{
			return $response;
		}
	}
	
	/**
	 * Store Page title
	 *
	 * @access public
	 * @return string Page title
	 */
	function storePageTitle($data)
	{
		// Create database object
		$db = &JFactory::getDBO();
		
		// Getting the new page title value
		$newtitle = $data['value'];

		$params = &$this->getSystemParams();

		if(!$params)
		{
			return false;
		}
		$params->set('page_title',$newtitle);
	
		// Get the raw text back from $params
		$params = $params->toString();

		$query = "UPDATE #__menu SET params = '".$params."' WHERE id = ".$this->_id;
		$db->setQuery($query);
		if(!$db->query())
		{
			return false;
		}
		else
		{
			return $this->getPageTitle();
		}
	}
	
	/**
	 * Get Page title
	 *
	 * @access public
	 * @return string Page title
	 */
	function getPageTitle()
	{
		$db = &JFactory::getDBO();
		
		$params = &$this->getSystemParams();
		if(!$params)
		{
			return false;
		}
		return $params->get('page_title');
	}
	
}

?>