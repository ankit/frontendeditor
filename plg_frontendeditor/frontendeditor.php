<?php
/**
 * @version		$Id: frontendeditor.php 373 2009-06-01 00:42:38Z ankit.ahuja $
 * @package		Joomla
 * @subpackage	plgSystemFrontendeditor
 * @copyright	Copyright (C) 2009 Open Source Matters. All rights reserved.
 * @author 		Ankit Ahuja ( as part of Google Summer Of Code 2009 )
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open 
 * source software licenses. See COPYRIGHT.php for copyright notices and
 * details.
 */

//no direct access
defined('_JEXEC') or die ('Restricted Access');

jimport('joomla.plugin.plugin');

/**
 * Frontend Editor Plugin
 * 
 * @package    Joomla
 * @subpackage plgSystemFrontendeditor
 */

class plgSystemFrontendeditor extends JPlugin
{
	/**
	 * Constructor
	 *
	 * @access	protected
	 * @param	object $subject The object to observe
	 * @param 	array  $config  An array that holds the plugin configuration
	 */
	function plgSystemFrontendeditor( &$subject, $config )
	{
		parent::__construct( $subject, $config );
	}
	
	/**
	 * Get Frontendeditor settings
	 */
	function getSettings()
	{
		$db =& JFactory::getDBO();
		$query = "SELECT * FROM #__frsettings WHERE id = 1";
		$db->setQuery($query);
 		$data = $db->loadObject();
		return $data;
	}
	
	function onAfterDispatch()
	{
		global $mainframe;
		
		$base_uri = JURI::root(true).'/plugins/system/';
		$js_dir = $base_uri."frontendeditor/js/";
		$css_dir = $base_uri."frontendeditor/css/";
		
		if($mainframe->isAdmin())
		{
			return; //Don't run in admin
		}
		
		$document = &JFactory::getDocument();
		$doctype = $document->getType();
		
		if ( $doctype != 'html' ) { return; }
		
		// Initializing some variables
		$view = JRequest::getString('view');
		$task = JRequest::getString('task');
		$layout = JRequest::getString('layout');
		$option = JRequest::getString('option','');
		$c = JRequest::getString('c','');
		
		if($view=="images" || $view=="imagesList" || $task=="ins_pagebreak")
		{
			return;
		}
				
		if($view =="article" && ($task == "edit" || $layout=="form"))
		{
			$document->addScript($js_dir."tabs.js");
			$document->addStylesheet($css_dir."editor.css");
			$document->addStylesheet($css_dir."tabs.css");
			return;
		}
		
		if($option=="com_frontendeditor" && $task=="edit" && $c=="module")
		{
			$document->addScript($js_dir."tabs.js");
			$document->addStylesheet($css_dir."editor.css");
			$document->addStylesheet($css_dir."tabs.css");
			return;
		}
		
		$user = &JFactory::getUser();
		$data = $this->getSettings();

		$canEditArticle = ($user->authorize('com_content','edit','content','all') || $user->authorize('com_content','edit','content','own'));
		$canEditModule = $user->authorize('com_modules','manage') && $data->module_edit;
		$canEditMenu = $user->authorize('com_menus','manage') && $data->menuitem_edit;

		if($canEditModule || $canEditMenu || $canEditArticle)
		{
			//To get the plugin to work under 1.5. Unloading mootools 1.11 and instead loading mootools 1.2
			//Thanks to Mostafa :)

			$newScriptArray = array();
			if($canEditArticle || $canEditModule || $canEditMenu)
			{
				$newScriptArray[$js_dir.'mootools-core.js'] = 'text/javascript';
				$newScriptArray[$js_dir.'mootools-more.js'] = 'text/javascript';
				$newScriptArray[$js_dir.'mootools-compat.js'] = 'text/javascript';
				foreach( $document->_scripts as $key => $value)
				{
					if( strpos($key, 'media/system/js/mootools.js') == false && strpos($key, 'media/system/js/mootools-uncompressed.js') == false)
					{
							$newScriptArray[$key] = $value;
					}
				}
				$document->_scripts = $newScriptArray;
			}
			//end of mootools hack
			
			$document->addScriptDeclaration("var fr_coloredit = $data->color_edit;var fr_toggle = $data->toggle;");
			$document->addStyleDeclaration("span.fr_title{ background-color:#".$data->color." !important;}");

			$document->addScript($js_dir."modal.js");
			$document->addStylesheet($css_dir."modal.css");
			$document->addScript($js_dir."common.js");
			$document->addStylesheet($css_dir."frontend.css");

			// Create a token for the editing forms
			$token = JUtility::getToken();
			$image_dir = $base_uri."/frontendeditor/images/";
			$document->addScriptDeclaration("var fr_jtoken = '$token';var fr_imagedir ='$image_dir';");
		
			if($canEditModule)
			{
				$document->addScript($js_dir."module.js");
				if($data->dragdrop_edit)
				{
					$document->addScript($js_dir."dragndrop.js");
					$document->addStylesheet($css_dir."dragndrop.css");
				}
				$document->addScriptDeclaration("var fr_dragdrop = $data->dragdrop_edit;");
			}
		
			if($canEditMenu)
			{
				$document->addScript($js_dir."menuitem.js");
				$document->addScriptDeclaration("var fr_pageTitleSelector = '$data->pagetitle_sel';");
			}
		
			if($canEditArticle)
			{
				$document->addScriptDeclaration("var fr_articleSelector = '$data->article_class';var fr_editIconSelector = '$data->editicon_sel'; var fr_alias = '$data->alias_edit'");
				$document->addScript($js_dir."article.js");
			}
		}
	}	
	
	function onAfterRender()
	{
		global $mainframe;
		
		if($mainframe->isAdmin())
		{
			return;
		}
		$document = &JFactory::getDocument();
		$doctype = $document->getType();
		if($doctype != "html")
		{
			return;
		}
		$user = &JFactory::getUser();
		$canEditMenu = $user->authorize('com_menus','manage');
		if($canEditMenu)
		{
			$itemid = $_GET['Itemid'];
			JResponse::appendBody('<span class="fr_component_id" id="component.'.$itemid.'" style="display:none"></span>');
		}
	}
	
}
?>