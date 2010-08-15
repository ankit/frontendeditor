<?php
/**
 * @version		$Id: frontendeditor.php 388 2009-06-02 15:06:55Z ankit.ahuja $
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

defined ('_JEXEC') or die ('Restricted Access');

$controllerName = JRequest::getCmd('c',null);
if($controllerName)
{
	require_once JPATH_COMPONENT.DS.'controllers'.DS.$controllerName.'.php';
	$controllerName = 'FrontendeditorController'.$controllerName;

	// Create the controller
	$controller = new $controllerName();

	// Perform the Request task
	$controller->execute(JRequest::getVar('task'));

	// Redirect if set by controller
	$controller->redirect();
}
