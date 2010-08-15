<?php defined('_JEXEC') or die('Restricted access'); ?>
<?php JHTML::_('behavior.formvalidation'); ?>
<?php JHTML::_('behavior.tooltip'); ?>
<script type="text/javascript">
	function submitbutton(pressbutton){
		var form = document.adminForm;
		if(pressbutton == 'cancel')
		{
			submitform(pressbutton);
			return;
		}
		submitform(pressbutton);
	}
	window.addEvent('domready',function(){
		if($('color_edit0').checked == true)
		{
			$('color').disabled = true;
			$('color_text').setStyle('visibility','hidden');
		}
		$('color_edit0').addEvent('click',function(){
			$('color').disabled = true;
			$('color_text').setStyle('visibility','hidden');
		});
		$('color_edit1').addEvent('click',function(){
			$('color').disabled = false;
			$('color_text').setStyle('visibility','visible');
		});
		
	});
	
</script>
<form action="index.php" method="post" name="adminForm" id="adminForm">
	<fieldset class="adminForm">
		<legend><?php echo JText::_( 'Ready your template' ); ?></legend>
		<?php echo JText::_("Before you start using this extension, it'll need to make changes to your active template's <b>index.php</b>, add a module chrome and an article form layout.<br/><br/> <i>Do not modify/delete <strong>index.php.backup</strong>, <strong>html/modules.php.backup</strong> or <strong>html/com_content/article/form.php</strong> in your template's folder</i> as they'll be required to revert any changes. <br/><br/>If you want to undo the changes anytime , click on Revert Changes. To learn how to make these changes manually, read the documentation."); ?>
		<br/><br/>
		<input type="button" onclick="submitbutton('applyhack');" value="<?php echo JText::_('Apply Changes'); ?>" />
		<input type="button" onclick="submitbutton('reverthack');" value="<?php echo JText::_('Revert Changes'); ?>" />		
	</fieldset>
	<fieldset class="adminForm">
		<legend><?php echo JText::_( 'Settings' ); ?></legend>
			<table class="admintable">
		        <tr>
		            <td width="100" align="right" class="key">
		                <label for="module">
		                    <?php echo JText::_( 'At the start of a new session, editing should be: ' ); ?>
		                </label>
		            </td>
		            <td>
						<?php echo $this->lists['toggle'] ?>
		            </td>
		        </tr>
		        <tr>
		            <td width="100" align="right" class="key">
		                <label for="module">
		                    <?php echo JText::_( 'Enable editing of modules:' ); ?>
		                </label>
		            </td>
		            <td>
						<?php echo $this->lists['module'] ?>
		            </td>
		        </tr>
				<tr>
		            <td width="100" align="right" class="key">
		                <label for="menuitem">
		                    <?php echo JText::_( 'Enable editing of menuitems and page title:' ); ?>
		                </label>
		            </td>
		            <td>
						<?php echo $this->lists['menuitem'] ?>
		            </td>
		        </tr>
				<tr>
		            <td width="100" align="right" class="key">
		                <label for="dragdrop">
		                    <?php echo JText::_( 'Enable drag and drop ordering of modules:' ); ?>
		                </label>
		            </td>
		            <td>
						<?php echo $this->lists['dragdrop'] ?>
		            </td>
		        </tr>
				
				<tr>
		            <td width="100" align="right" class="key">
		                <label for="article_class">
		                    <?php echo JText::_( 'Unique selector for article titles ( default is .contentheading ):' ); ?>
		                </label>
		            </td>
		            <td>
						<input class="text_area" type="text" name="article_class" id="article_class" value="<?php echo $this->data->article_class ?>"/> 
		            </td>
		        </tr>
		
				<tr>
		            <td width="100" align="right" class="key">
		                <label for="pagetitle_sel">
		                    <?php echo JText::_( 'Unique selector for page titles ( default is .componentheading ):' ); ?>
		                </label>
		            </td>
		            <td>
						<input class="text_area" type="text" name="pagetitle_sel" id="pagetitle_sel" value="<?php echo $this->data->pagetitle_sel ?>"/> 
		            </td>
		        </tr>

				<tr>
		            <td width="100" align="right" class="key">
		                <label for="pagetitle_sel">
		                    <?php echo JText::_( 'Unique selector for article edit icons ( default is .contentpaneopen img[alt=edit]):' ); ?>
		                </label>
		            </td>
		            <td>
						<input size="40" class="text_area" type="text" name="editicon_sel" id="editicon_sel" value="<?php echo $this->data->editicon_sel ?>"/>
		            </td>
		        </tr>
				
		        <tr>
		            <td width="100" align="right" class="key">
		                <label for="color">
		                    <?php echo JText::_( 'Hover color for editable titles:' ); ?>
		                </label>
		            </td>
					<td>
						<?php echo $this->lists['color'] ?>
					</td>
		            <td>
						<input class="text_area color" type="text" name="color" id="color" value="<?php echo $this->data->color ?>"/><span id="color_text">(<i>Click on field to display color picker</i>)</span>
		            </td>
		        </tr>
				<tr>
		            <td width="100" align="right" class="key">
		                <label for="alias">
		                    <?php echo JText::_( 'Automatically set alias on article title save:' ); ?>
		                </label>
		            </td>
					<td>
						<?php echo $this->lists['alias'] ?>
					</td>
		        </tr>
				<tr>
		            <td width="100" align="right" class="key">
		                <label for="module_params">
		                    <?php echo JText::_( 'Display Advanced, Legacy and Other Params in module editor:' ); ?>
		                </label>
		            </td>
					<td>
						<?php echo $this->lists['module_params'] ?>
					</td>
		        </tr>
		    </table>
	</fieldset>
	<div class="clr"></div>
	<input type="hidden" name="option" value="com_frontendeditor" />
	<input type="hidden" name="id" value="1" />
	<input type="hidden" name="task" value="" />
</form>