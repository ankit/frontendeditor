<?php
function modChrome_freditor($module,&$params,&$attribs)
{
	// Check if user has permissions to manage modules
	$user = &JFactory::getUser();
	$canEditModule = $user->authorize('com_modules','manage');
	
	// Check if module editing is enabled
	$db =& JFactory::getDBO();
	$query = "SELECT * FROM #__frsettings WHERE id = 1";
	$db->setQuery($query);
	$data = $db->loadObject();
	
	if($data->module_edit && $canEditModule)
	{
		if($module->showtitle)
		{
			$style = "position:relative;float:right;margin-right:5px";
		}
		else
		{
			$style = "position:absolute;display:none";
		}
		?>
		<div class="mod-edit" id = "module.<?php echo $module->id ?>" name="<?php echo $module->title ?>">
			<a class='fr_editicon' style="<?php echo $style; ?>" href='index.php?option=com_frontendeditor&c=module&task=edit&tmpl=component&cid[]=<?php echo $module->id?>' rel="{handler:'iframe',iframePreload:true,size:{x:800,y:570},sizeLoading:{x:800,y:570},onOpen:JEdit.onModalOpen}">
			</a>
			<?php echo $module->content; ?>
		</div>
	<?php
	}
	else
	{
		echo $module->content;
	}
}
?>