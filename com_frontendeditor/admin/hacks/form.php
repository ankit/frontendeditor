<?php // no direct access
defined('_JEXEC') or die('Restricted access');

$config =& JFactory::getConfig();
$publish_up =& JFactory::getDate($this->article->publish_up);
$publish_up->setOffset($config->getValue('config.offset'));
$publish_up = $publish_up->toFormat();

if (! isset($this->article->publish_down) || $this->article->publish_down == 'Never') {
	$publish_down = JText::_('Never');
} else {
	$publish_down =& JFactory::getDate($this->article->publish_down);
	$publish_down->setOffset($config->getValue('config.offset'));
	$publish_down = $publish_down->toFormat();
}

$document =& JFactory::getDocument();
?>

<script language="javascript" type="text/javascript">
<!--

function setgood() {
	// TODO: Put setGood back
	return true;
}

var sectioncategories = new Array;
<?php
$i = 0;
foreach ($this->lists['sectioncategories'] as $k=>$items) {
	foreach ($items as $v) {
		echo "sectioncategories[".$i++."] = new Array( '$k','".addslashes( $v->id )."','".addslashes( $v->title )."' );\n\t\t";
	}
}
?>

function cancelEditing()
{
	var form = document.adminForm;
	if(typeof(parent.fr_toggle) == "undefined")
	{
		submitform('cancel');
		return;
	}
	form.task.value = 'cancel';
	form.send({
		onRequest:function(){
			parent.JEdit.loadingIcon();
			$('tabbed_box').setStyles({
			'visibility':'hidden'
			});
		},
		onComplete:function(response){
			parent.JEdit.closeBox();
		}
	});
}
window.addEvent('domready',function(){
	$('adminForm').addEvent('submit',function(e){
		new Event(e).stop();
		// do field validation
		var text = <?php echo $this->editor->getContent( 'text' ); ?>
		if (this.title.value == '') {
			return alert ( "<?php echo JText::_( 'Article must have a title', true ); ?>" );
		} else if (text == '') {
			return alert ( "<?php echo JText::_( 'Article must have some text', true ); ?>");
		} else if (parseInt('<?php echo $this->article->sectionid;?>')) {
			// for articles
			if (this.catid && getSelectedValue('adminForm','catid') < 1) {
				return alert ( "<?php echo JText::_( 'Please select a category', true ); ?>" );
			}
		}
		<?php echo $this->editor->save( 'text' ); ?>
		if(typeof(parent.fr_toggle) == "undefined")
		{
			submitform('save');
			return;
		}
				
		this.task.value = 'save';
		this.send({
			onRequest:function(){
				parent.JEdit.loadingIcon();
				$('tabbed_box').setStyles({
				'visibility':'hidden'
				});
			},
			onComplete:function(response){
				parent.JEdit.loadArticle("<?php echo JRequest::getInt('id') ?>");
			}
		});
	});
	
	parent.addEvent('keydown',function(e){
		e = new Event(e);
		if(e.key == 'esc')
		{
			cancelEditing();
		}
	});
});
//-->
</script>
<div id='tabbed_box'>
	<div class='tabbed_area'>
<!--		<h2><?php// echo $this->article->title ?><span class='small'>  last edited on <?php// echo $this->article->modified	?>. Originally created by <?php //echo $this->article->created_by_alias?></span></h2>	-->
		<form action="<?php echo $this->action ?>" method="post" name="adminForm" id="adminForm">		
		<ul class="tabs">
			<li><a href="javascript:tabSwitch(1);" onclick="this.blur();" rel="1" class="fr_tabs active"><?php echo JText::_('Editor'); ?></a></li>
			<li><a href="javascript:tabSwitch(2);" onclick="this.blur();" rel="2" class="fr_tabs"><?php echo JText::_('Publish Settings'); ?></a></li>
			<li><a href="javascript:tabSwitch(3);" onclick="this.blur();" rel="3" class="fr_tabs"><?php echo JText::_('Meta'); ?></a></li>
			<li class='buttons'>
				<button type="submit" onclick="this.blur();" class="submit">
					<?php echo JText::_('Save') ?>
				</button>
				<button type="button" onclick="this.blur();cancelEditing();" class="cancel">
					<?php echo JText::_('Cancel') ?>
				</button>
			</li>
		</ul>
		<div id="content_1" class="tab_content" style="display:block">
			<!-- Article editor -->
			<fieldset>
			<table class="adminform" width="100%">
			<tr>
				<td>
					<div style="float: left;margin-bottom:2px">
						<label for="title">
							<?php echo JText::_( 'Title' ); ?>:
						</label>
						<input class="inputbox" type="text" id="title" name="title" size="50" maxlength="100" value="<?php echo $this->escape($this->article->title); ?>" />
						<input class="inputbox" type="hidden" id="alias" name="alias" value="<?php echo $this->escape($this->article->alias); ?>" />
					</div>
				</td>
			</tr>
			</table>

			<?php
			echo $this->editor->display('text', $this->article->text, '100%', '400', '70', '15');
			?>
			</fieldset>
		</div>
		<div id="content_2" class="tab_content">
			<!-- Publish settings -->
			<fieldset>
			<table class="adminform">
			<tr>
				<td class="key">
					<label for="sectionid">
						<?php echo JText::_( 'Section' ); ?>:
					</label>
				</td>
				<td>
					<?php echo $this->lists['sectionid']; ?>
				</td>
			</tr>
			<tr>
				<td class="key">
					<label for="catid">
						<?php echo JText::_( 'Category' ); ?>:
					</label>
				</td>
				<td>
					<?php echo $this->lists['catid']; ?>
				</td>
			</tr>
			<?php if ($this->user->authorize('com_content', 'publish', 'content', 'all')) : ?>
			<tr>
				<td class="key">
					<label for="state">
						<?php echo JText::_( 'Published' ); ?>:
					</label>
				</td>
				<td>
					<?php echo $this->lists['state']; ?>
				</td>
			</tr>
			<?php endif; ?>
			<tr>
				<td width="120" class="key">
					<label for="frontpage">
						<?php echo JText::_( 'Show on Front Page' ); ?>:
					</label>
				</td>
				<td>
					<?php echo $this->lists['frontpage']; ?>
				</td>
			</tr>
			<tr>
				<td class="key">
					<label for="created_by_alias">
						<?php echo JText::_( 'Author Alias' ); ?>:
					</label>
				</td>
				<td>
					<input type="text" id="created_by_alias" name="created_by_alias" size="50" maxlength="100" value="<?php echo $this->article->created_by_alias; ?>" class="inputbox" />
				</td>
			</tr>
			<tr>
				<td class="key">
					<label for="publish_up">
						<?php echo JText::_( 'Start Publishing' ); ?>:
					</label>
				</td>
				<td>
				    <?php echo JHTML::_('calendar', $publish_up, 'publish_up', 'publish_up', '%Y-%m-%d %H:%M:%S', array('class'=>'inputbox', 'size'=>'25',  'maxlength'=>'19')); ?>
				</td>
			</tr>
			<tr>
				<td class="key">
					<label for="publish_down">
						<?php echo JText::_( 'Finish Publishing' ); ?>:
					</label>
				</td>
				<td>
				    <?php echo JHTML::_('calendar', $publish_down, 'publish_down', 'publish_down', '%Y-%m-%d %H:%M:%S', array('class'=>'inputbox', 'size'=>'25',  'maxlength'=>'19')); ?>
				</td>
			</tr>
			<tr>
				<td valign="top" class="key">
					<label for="access">
						<?php echo JText::_( 'Access Level' ); ?>:
					</label>
				</td>
				<td>
					<?php echo $this->lists['access']; ?>
				</td>
			</tr>
			<tr>
				<td class="key">
					<label for="ordering">
						<?php echo JText::_( 'Ordering' ); ?>:
					</label>
				</td>
				<td>
					<?php echo $this->lists['ordering']; ?>
				</td>
			</tr>
			</table>
			</fieldset>
		</div>
		<div id="content_3" class="tab_content">
			<!-- Meta -->
			<fieldset>
			<table class="adminform">
			<tr>
				<td valign="top" class="key">
					<label for="metadesc">
						<?php echo JText::_( 'Description' ); ?>:
					</label>
				</td>
				<td>
					<textarea rows="5" cols="50" style="width:500px; height:120px" class="inputbox" id="metadesc" name="metadesc"><?php echo str_replace('&','&amp;',$this->article->metadesc); ?></textarea>
				</td>
			</tr>
			<tr>
				<td  valign="top" class="key">
					<label for="metakey">
						<?php echo JText::_( 'Keywords' ); ?>:
					</label>
				</td>
				<td>
					<textarea rows="5" cols="50" style="width:500px; height:50px" class="inputbox" id="metakey" name="metakey"><?php echo str_replace('&','&amp;',$this->article->metakey); ?></textarea>
				</td>
			</tr>
			</table>
			</fieldset>
		</div>
		<input type="hidden" name="option" value="com_content" />
		<input type="hidden" name="id" value="<?php echo $this->article->id; ?>" />
		<input type="hidden" name="version" value="<?php echo $this->article->version; ?>" />
		<input type="hidden" name="created_by" value="<?php echo $this->article->created_by; ?>" />
		<input type="hidden" name="referer" value="<?php echo @$_SERVER['HTTP_REFERER']; ?>" />
		<?php echo JHTML::_( 'form.token' ); ?>
		<input type="hidden" name="task" value="" />
		</form>
		<?php echo JHTML::_('behavior.keepalive'); ?>
	</div>
	<br/>
<!--	<span class='small'>Last edited on <?php //echo $this->article->modified	?>.</span> -->
</div>
