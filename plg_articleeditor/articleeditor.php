<?php
/**
 * @version		$Id: articleeditor.php 388 2009-06-02 15:06:55Z ankit.ahuja $
 * @package		Joomla
 * @subpackage	plgContentArticleeditor
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
 * Frontend Article Editor Plugin
 * 
 * @package Joomla
 * @subpackage plgContentArticleeditor
 */
class plgContentArticleeditor extends JPlugin
{
	/**
	 * Constructor
	 *
	 * @access	protected
	 * @param	object $subject The object to observe
	 * @param 	array  $config  An array that holds the plugin configuration
	 */
	function plgContentArticleeditor( &$subject, $config )
	{
		parent::__construct( $subject, $config );
	}
	
	function onBeforeDisplayContent(&$article, &$params, &$limitstart)
	{
		global $mainframe;
		$user = &JFactory::getUser();

		// Check if the user has permissions to edit article
		$canEditAll	= $user->authorize('com_content', 'edit', 'content', 'all');
		$canEditOwn = $user->authorize('com_content', 'edit', 'content', 'own');
		$title = htmlspecialchars($article->title,ENT_QUOTES);
		$classname = 'contentheading';
		
		if($canEditAll)
		{
			$html = "<span id='article.$article->id' class='fr_article_id' style='visibility:hidden;' type='$classname' title='$title'";
			if(isset($article->params))
			{
				$html .= "rel='".$article->params->get('show_title')."'></span>";
			} 
			else
			{
				$html .= "></span>";
			}
			return $html;
		}
		else if($canEditOwn)
		{
			if($user->id == $article->created_by)
			{
				return "<span id='article.".$article->id."' class='fr_article_id' style='visibility:hidden;' title='$title'></span>";
			}
			else
			{
				return '';
			}
		}
	}
}
?>