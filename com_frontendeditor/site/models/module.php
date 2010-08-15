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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.model');

/**
 * Frontendeditor Component Module Model
 *
 * @package Joomla.Site
 * @subpackage Frontendeditor
 */

class FrontendeditorModelModule extends JModel
{
	
	/**
	* Module ID
	*
	* @var int
	*/
	var $_id = null;
	
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
	 * Set Module ID
	 */
	function setId()
	{
		$this->_id = JRequest::getInt('id',0,'');
	}
	
	/**
	 * Store the Module title
	 * @param array POST/GET data
	 * @access public
	 * @return string Module title
	 */
	function storeTitle($data)
	{
		// Create database object
		$db = &JFactory::getDBO();

		// Getting the new value of title from request
 		$newtitle = $data['value'];

		// Creating the query to update module title
		$query = "UPDATE #__modules SET title = '".$newtitle."' WHERE id = ".$this->_id;
		$db->setQuery($query);
		if(!$db->query()){
			$this->setError($db->getErrorMsg());
			return false;
		}
		return $this->getTitle();
	}
	
	/**
	 * Get the Module title
	 * @access public
	 * @return string Module title
	 */
	function getTitle()
	{
		$db = &JFactory::getDBO();
		$query = "SELECT title FROM #__modules WHERE id = ".$this->_id;
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
	 * Get all positions
	 * @access public
	 */
	function getPositions()
	{
		jimport('joomla.filesystem.folder');

		$client =& JApplicationHelper::getClientInfo($this->getState('clientId'));
		if ($client === false) {
			return false;
		}

		//Get the database object
		$db	=& JFactory::getDBO();

		// template assignment filter
		$query = 'SELECT DISTINCT(template) AS text, template AS value'.
				' FROM #__templates_menu' .
				' WHERE client_id = '.(int) $client->id;
		$db->setQuery( $query );
		$templates = $db->loadObjectList();

		// Get a list of all module positions as set in the database
		$query = 'SELECT DISTINCT(position)'.
				' FROM #__modules' .
				' WHERE client_id = '.(int) $client->id;
		$db->setQuery( $query );
		$positions = $db->loadResultArray();
		$positions = (is_array($positions)) ? $positions : array();

		// Get a list of all template xml files for a given application

		// Get the xml parser first
		for ($i = 0, $n = count($templates); $i < $n; $i++ )
		{
			$path = $client->path.DS.'templates'.DS.$templates[$i]->value;

			$xml =& JFactory::getXMLParser('Simple');
			if ($xml->loadFile($path.DS.'templateDetails.xml'))
			{
				$p =& $xml->document->getElementByPath('positions');
				if (is_a($p, 'JSimpleXMLElement') && count($p->children()))
				{
					foreach ($p->children() as $child)
					{
						if (!in_array($child->data(), $positions)) {
							$positions[] = $child->data();
						}
					}
				}
			}
		}

		if(defined('_JLEGACY') && _JLEGACY == '1.0')
		{
			$positions[] = 'left';
			$positions[] = 'right';
			$positions[] = 'top';
			$positions[] = 'bottom';
			$positions[] = 'inset';
			$positions[] = 'banner';
			$positions[] = 'header';
			$positions[] = 'footer';
			$positions[] = 'newsflash';
			$positions[] = 'legals';
			$positions[] = 'pathway';
			$positions[] = 'breadcrumb';
			$positions[] = 'user1';
			$positions[] = 'user2';
			$positions[] = 'user3';
			$positions[] = 'user4';
			$positions[] = 'user5';
			$positions[] = 'user6';
			$positions[] = 'user7';
			$positions[] = 'user8';
			$positions[] = 'user9';
			$positions[] = 'advert1';
			$positions[] = 'advert2';
			$positions[] = 'advert3';
			$positions[] = 'debug';
			$positions[] = 'syndicate';
		}

		$positions = array_unique($positions);
		sort($positions);

		return $positions;
	}
	
	/** 
	 * Get modules for a specific position
	 * @param string Position for which to get the modules
	 * @access public	
	 * @return array Modules for the position
	 */
	function getModulesForPosition($position)
	{
		$db =& JFactory::getDBO();
		$query = "SELECT id,ordering,access".
				 " FROM #__modules".
				 " WHERE position = '".$position."'" .
				 " ORDER BY ordering ASC";
		$db->setQuery($query);
		$modules = $db->loadObjectList();
		return $modules;
	}
	
	/**
	 * Reset the order values of modules in an incremental fashion
	 * @param array Modules for which to reset the order value
	 * @param string Position for which to return the modules
	 * @access public		
	 * @return array Modules for the position
	 */
	function resetOrder($modules,$position)
	{
		$row =& JTable::getInstance('module');
		foreach($modules as $key => $module)
		{
			$row->load((int) $module->id);
			$row->ordering = $key;
			if (!$row->store()) {
				return JError::raiseWarning( 500, $db->getErrorMsg() );
			}
		}
		return $this->getModulesForPosition($position);
	}
	
}