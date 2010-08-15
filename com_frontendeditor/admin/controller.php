<?php
/**
 * @version		$Id: controller.php 388 2009-06-02 15:06:55Z ankit.ahuja $
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
defined('_JEXEC') or die('Restricted Access');

jimport('joomla.application.component.controller');

/**
 * Frontendeditor Component Controller
 *
 * @package Joomla.Administrator
 * @subpackage Frontendeditor
 */
class FrontendeditorController extends JController{
	
	/**
	 * Display the admin screen
	 *
	 * @access public
	 */
	function display()
	{
		parent::display();
	}
	
	/**
	 * Save the settings
	 *
	 */
	function save()
	{
		$model = $this->getModel('frontendeditor');
		if($model->store())
		{
			$msg = JText::_('Settings saved!');
		}
		else
		{
			$msg = JText::_('There was an error saving settings!');
		}
		$link = 'index.php?option=com_frontendeditor';
		$this->setRedirect($link,$msg);
	}
	
	/**
	 * Cancel any changes and exit the admin screen
	 *
	 * @access public
	 */
	function cancel()
	{
		$link = 'index.php';
		$this->setRedirect($link);
	}
	
	/**
	 * Apply the hack to the current active template
	 *
	 * @access public
	 */
	function applyHack()
	{
		$db =& JFactory::getDBO();
		// Get the current default template
		$query = ' SELECT template '
				.' FROM #__templates_menu '
				.' WHERE client_id = ' . (int) $clientId
				.' AND menuid = 0 ';
		$db->setQuery($query);
		$dtemplate = $db->loadResult();
		$templateDir = JPATH_ROOT.DS.'templates'.DS.$dtemplate.DS;
		
		// If the index.php.backup file exists, then first revert index.php
		if(file_exists($templateDir.'index.php.backup'))
		{
			$txt = file_get_contents($templateDir.'index.php.backup');
			file_put_contents($templateDir.'index.php',$txt);
		}
		$txt = file_get_contents($templateDir.'index.php');

		// Backing up current index.php
		file_put_contents($templateDir.'index.php.backup',$txt);
		
		// Making changes to index.php
		function check2($matches2){
			foreach($matches2 as $match)
			{
				$match = trim($match);
			}		
			if(stristr($matches2[2],"freditor"))
			{
				return $matches2[1].' style="'.$matches2[2].'" '.$matches2[3];
			}
			else
			{
				return $matches2[1].' style="'.$matches2[2].' freditor" '.$matches2[3];
			}
		}
		
		function check1($matches){
			foreach($matches as $match)
			{
				$match = trim($match);
			}
			$output = '<jdoc:include type="'.$matches[3].'" ';
			$pattern = "/(.*)style=\"(.*?)\"/i";	

			if(stristr($matches[2],"style"))
			{
				$output = $output.preg_replace_callback($pattern,"check2",$matches[2]).$matches[4];
			}
			else if(stristr($matches[4],"style"))
			{
				$output = $output.$matches[2].preg_replace_callback($pattern,"check2",$matches[4]);
			}
			else
			{
				$output .= $matches[2].$matches[4].' style="freditor"';
			}
			$pattern = "/(.*)name=\"(.*?)\"/i";
			$position = null;
			if(stristr($matches[2],"name"))
			{
				if(preg_match($pattern,$matches[2],$matches2)){
					$position = $matches2[2];
				}
			}
			else if(stristr($matches[4],"name"))
			{
				if(preg_match($pattern,$matches[4],$matches2)){
					$position = $matches2[2];
				}			
			}
			return '<div class="frpos.'.$position.'">'.$output.' /></div>';
		}

		// regex for jdoc:include statements for modules
	    $pattern = "/(\< *?jdoc:include(.*?)type=\"(modules|module)+\"(.*?)\/(\>)+?)/i";
	    $txt = preg_replace_callback($pattern,"check1",$txt);
		if(file_put_contents($templateDir.'index.php',$txt))
		{
			$msg = "Successfully made changes to ".$templateDir."index.php";
		}
		
		//Adding module chrome

		$chrome = file_get_contents(JPATH_COMPONENT.DS.'hacks'.DS.'chrome.php');
		
		if(file_exists($templateDir.'html'.DS.'modules.php'))
		{
			$txt = file_get_contents($templateDir.'html'.DS.'modules.php');
			if(!strstr($txt,'modChrome_freditor'))
			{
				file_put_contents($templateDir.'html'.DS.'modules.php.backup',$txt);
				$pattern = "/(\<\?php(.*)(\?\>)+?(.*))/s";
				if(preg_match($pattern,$txt,$matches))
				{
					if(strstr($matches[4],'<?php'))
					{
						$txt .= "?>";
					}
				}
				else if($txt)
				{
					$txt .= "?>";
				}
				$txt .= $chrome;
				if(file_put_contents($templateDir.'html'.DS.'modules.php',$txt))
				{
					$msg .= ", ".$templateDir."html".DS."modules.php";
				}
			}
			else
			{
				$neg_msg .= ". Module chrome already exists in ".$templateDir."html".DS."modules.php.";
			}	
		}
		else
		{
			$txt = '<?php defined("_JEXEC") or die("Restricted access") ?>'.$chrome;
			if(file_put_contents($templateDir.'html'.DS.'modules.php',$txt))
			{
				$msg .= ", ".$templateDir."html".DS."modules.php";
			}
		}
		// Adding form.php for article form layout
		
		// Check if form.php already exists. If yes, then back it up
		
		$form_loc = $templateDir.'html'.DS.'com_content'.DS.'article'.DS;
		if(file_exists($form_loc.'form.php') && !file_exists($form_loc.'form.php.backup'))
		{
			$txt = file_get_contents($form_loc.'form.php');
			file_put_contents($form_loc.'form.php.backup',$txt);
		}
		//Get the content from the hack form.php file
		$txt = file_get_contents(JPATH_COMPONENT.DS.'hacks'.DS.'form.php');
		if($txt)
		{
			if(!is_dir($form_loc))
			{
				mkdir($form_loc,0777,true);
			}
			file_put_contents($form_loc.'form.php',$txt);
			$msg .= " and ".$form_loc.'form.php';
		}
		$msg .= $neg_msg;
		$this->setRedirect("index.php?option=com_frontendeditor",$msg);
	}
	
	/**
	 * Revert any changes made by the hack on the current active template
	 *
	 * @access public
	 */
	function revertHack()
	{
		$db =& JFactory::getDBO();
		// Get the current default template
		$query = ' SELECT template '
				.' FROM #__templates_menu '
				.' WHERE client_id = ' . (int) $clientId
				.' AND menuid = 0 ';
		$db->setQuery($query);
		$dtemplate = $db->loadResult();
		$templateDir = JPATH_ROOT.DS.'templates'.DS.$dtemplate.DS;
		// Check if the backup file index.php.backup exists
		if(file_exists($templateDir.'index.php.backup'))
		{
			$txt = file_get_contents($templateDir.'index.php.backup');
			file_put_contents($templateDir.'index.php',$txt);
			$msg = $templateDir."index.php.backup restored. ";
			unlink($templateDir."index.php.backup");
		}
		else
		{
			$msg = "Backup file ".$templateDir."index.php.backup does not exist. ";
		}
		// Check if the backup file modules.php.backup exists
		if(file_exists($templateDir.'html'.DS.'modules.php.backup'))
		{
			$txt = file_get_contents($templateDir.'html'.DS.'modules.php.backup');
			file_put_contents($templateDir.'html'.DS.'modules.php',$txt);
			unlink($templateDir.'html'.DS.'modules.php.backup');	
			$msg .= $templateDir."html".DS."modules.php.backup restored.";
		}
		else
		{
			$msg .= "Backup file ".$templateDir."html".DS."modules.php.backup does not exist. ";
		}
		// Check if backup file form.php exits
		$form_loc = $templateDir.'html'.DS.'com_content'.DS.'article'.DS;
		if(file_exists($form_loc."form.php.backup"))
		{
			$txt = file_get_contents($form_loc."form.php.backup");
			file_put_contents($form_loc."form.php",$txt);
			unlink($form_loc."form.php.backup");
			$msg .= $form_loc."form.php.backup restored. ";
		}
		else
		{
			$msg .= "Backup file ".$form_loc."form.php.backup does not exist. ";
		}
		$this->setRedirect("index.php?option=com_frontendeditor",$msg);
	}
	
}
?>