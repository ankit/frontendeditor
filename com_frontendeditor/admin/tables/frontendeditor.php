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

// no direct access
defined ('_JEXEC') or die ('Restricted Access');

/** 
 * Frontendeditor Table Class
 * @package Joomla.Administrator
 * @subpackage Frontendeditor
 */
class TableFrontendeditor extends JTable
{
	/** 
	 * Primary key
	 * @var id
	 */
	 var $id = null;
	
	 /**
	  * Color code
	  * @var string
	  */
	 var $color = null;
	
	 /**
	  * Article selector
	  * @var string
	  */
	 var $article_class = null;
	
	 /**
	  * Page title selector
	  * @var string
	  */
	 var $pagetitle_sel = null;
	
	 /**
	  * Edit icon selector
	  * @var string
	  */
	 var $editicon_sel = null;
	
	/**
	 * Module editing toggle
	 * @var bool
	 */
	 var $module_edit = null;
	
	/**
	 * Menuitem editing toggle
	 * @var bool
	 */
	 var $menuitem_edit = null;
	
	/**
	 * Drag and drop toggle
	 * @var bool
	 */
	 var $dragdrop_edit = null;
	
	/**
	 * Color choice toggle
	 * @var bool
	 */
	 var $color_edit = null;
	
	/**
	 * Advanced module params visibility toggle
	 * @var bool
	 */
	 var $moduleparams_visible = null;
	
	/**
	 * Toggle for if alias should be set on article title save
	 * @var bool
	 */
	 var $alias_edit = null;
	
	/**
	 * Default value of editing toggle 
	 * @var bool
	 */
	 var $toggle = null;
	
	/** 
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function TableFrontendeditor(&$db){
		parent::__construct('#__frsettings','id',$db);
	}
}