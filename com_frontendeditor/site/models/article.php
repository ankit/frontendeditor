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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.model');

/**
 * Frontendeditor Component Article Model
 * @package Joomla.Site
 * @subpackage Frontendeditor
 */

class FrontendeditorModelArticle extends JModel
{
	/**
	 * Article ID
	 * 
	 * @var int
	 */
	var $_id = null;
	
	/**
	 * Constructor
	 */
	function __construct()
	{
		parent::__construct();

		$id = JRequest::getVar('id', 0, '', 'int');
		$this->setId((int)$id);
	}

	/**
	 * Set the article id
	 *
	 * @access	public
	 * @param	int	Article ID 
	 */
	function setId($id)
	{
		// Set new article ID and wipe data
		$this->_id		= $id;
	}
	
	/**
	 * Store the article title
	 *
	 * @access public
	 * @param array GET/POST data
	 * @return string Article title
 	 */
	function storeTitle($data)
	{
		// Get database object
		$db = &JFactory::getDBO();

		// Getting the new value of title
		$newtitle = $data['value'];

		// Creating the query to update article title
		$query = "UPDATE #__content SET title = '".$newtitle."' WHERE id = ".$this->_id;
		$db->setQuery($query);
		if(!$db->query()){
			$this->setError($db->getErrorMsg());
			return false;
		}
		$is_alias = JRequest::getInt('alias',0,'get');
		if($is_alias)
		{
			$search  = array('/[^a-z0-9 ]+/i', '/ +/');
			$replace = array('', '-');
			$alias = strtolower(preg_replace($search, $replace, $newtitle));
			$query = "UPDATE #__content SET alias = '".$alias."' WHERE id = ".$this->_id;
			$db->setQuery($query);
			if(!$db->query()){
				$this->setError($db->getErrorMsg());
				return false;
			}
		}
		return $this->getTitle();
	}
	
	/** 
	 * Get the article title
	 * @return string Article title
	 * @access public
	 */
	function getTitle()
	{
		$db = &JFactory::getDBO();
		$query = "SELECT title FROM #__content WHERE id = ".$this->_id;
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
	
}