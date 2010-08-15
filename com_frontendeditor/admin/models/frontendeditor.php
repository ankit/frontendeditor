<?php
/**
 * @version		$Id: frontendeditor.php 388 2009-06-02 15:06:55Z ankit.ahuja $
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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.model');

/**
 * Frontendeditor Component Frontendeditor Model
 *
 * @package 	Joomla.Administrator
 * @subpackage	Frontendeditor
 */

class FrontendeditorModelFrontendeditor extends JModel
{
	
	/**
	 * Static value of id
	 * 
	 * @var int
	 */
	var $_id = 1;
	
	/**
	 * Settings data
	 *
	 * @var array
	 */
	var $_data = null;
	
	/**
	 * Constructor
	 */
	function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * Get the settings data
	 *
	 * @access public
	 * @return array
	 */
	function &getData()
	{
		if(empty($this->_data))
		{
			$db =& JFactory::getDBO();
			$query = 'SELECT * FROM #__frsettings WHERE id = '.$this->_id;
			$db->setQuery($query);
			$this->_data = $db->loadObject();	
		}
		return $this->_data;
	}
	
	/** 
	 * Save the settings
	 *
	 * @access public
	 * @return bool
	 */
	function store()
	{
		$row =& $this->getTable();
		
		$data = JRequest::get('post');
		if(!$row->bind($data)){
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		if(!$row->check()){
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		/* Save the row to database */
		if(!$row->store()){
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		
		return true;
	}
}