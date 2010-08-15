<?php // no direct access
defined('_JEXEC') or die('Restricted access');
JHTML::_('behavior.tooltip');
JHTML::_('behavior.combobox');
// clean item data
JFilterOutput::objectHTMLSafe( $row, ENT_QUOTES, 'content' );
?>
<script type="text/javascript">
	function allselections() {
		var e = document.getElementById('selections');
			e.disabled = true;
		var i = 0;
		var n = e.options.length;
		for (i = 0; i < n; i++) {
			e.options[i].disabled = true;
			e.options[i].selected = true;
		}
	}
	function disableselections() {
		var e = document.getElementById('selections');
			e.disabled = true;
		var i = 0;
		var n = e.options.length;
		for (i = 0; i < n; i++) {
			e.options[i].disabled = true;
			e.options[i].selected = false;
		}
	}
	function enableselections() {
		var e = document.getElementById('selections');
			e.disabled = false;
		var i = 0;
		var n = e.options.length;
		for (i = 0; i < n; i++) {
			e.options[i].disabled = false;
		}
	}
	function cancelEditing()
	{
		var form = document.adminForm;
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
		
		$('combobox-position-select').addEvent('change', function() {
			changeDynaList('ordering', orders, document.adminForm.position.value, 0, 0);
		});
		$('position').addEvent('change', function() {
			changeDynaList('ordering', orders, document.adminForm.position.value, 0, 0);
		});
		
		$('adminForm').addEvent('submit',function(e){
			new Event(e).stop();
			
			this.task.value = "save";
			// do field validation
			if(this.title.value=="")
			{
				return alert("<?php echo JText::_('Module must have a title!',true); ?>");
			}		
			else
			{
				<?php
				if ($this->row->module == '' || $this->row->module == 'mod_custom') {
					echo $this->editor->save( 'content' );
				}
				?>
				this.send({
					onRequest:function(){
					parent.JEdit.loadingIcon();
					$('tabbed_box').setStyles({
					'visibility':'hidden'
					});
				},
				onComplete:function(response){
					parent.JEdit.refreshDoc();
				}
				});
			}
		});
	});
	
	var originalOrder 	= '<?php echo $this->row->ordering;?>';
	var originalPos 	= '<?php echo $this->row->position;?>';
	var orders 			= new Array();	// array in the format [key,value,text]
	
	<?php	$i = 0;
	foreach ($this->orders2 as $k=>$items) {
		foreach ($items as $v) {
			echo "\n	orders[".$i++."] = new Array( \"$k\",\"$v->value\",\"$v->text\" );";
		}
	}
	?>
</script>
<div id="tabbed_box">
	<div class="tabbed_area">
		<form action="index.php" method="post" name="adminForm" id="adminForm">
			<ul class="tabs">
				<li><a href="javascript:tabSwitch(1);" onclick="this.blur();" rel="1" class="fr_tabs active"><?php echo JText::_( 'Details' ); ?></a></li>
				<li><a href="javascript:tabSwitch(2);" onclick="this.blur();" rel="2" class="fr_tabs"><?php echo JText::_( 'Menu Assignment' ); ?></a></li>
				<li><a href="javascript:tabSwitch(3);" onclick="this.blur();" rel="3" class="fr_tabs"><?php echo JText::_( 'Module Parameters' ); ?></a></li>
				<?php if($this->adv_params) { ?>
				<li><a href="javascript:tabSwitch(4);" onclick="this.blur();" rel="4" class="fr_tabs"><?php echo JText::_( 'Additional Params' ); ?></a></li>
				<?php } ?>
				<?php
				if ( !$this->row->module || $this->row->module == 'custom' || $this->row->module == 'mod_custom' ) {
				?>
				<li><a href="javascript:tabSwitch(5);" onclick="this.blur();" rel="5" class="fr_tabs"><?php echo JText::_( 'Custom Output' ); ?></a></li>
				<?php }?>
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
				<fieldset class="adminform">
					<table class="admintable" cellspacing="1">
						<tr>
							<td valign="top" class="key">
								<label><?php echo JText::_( 'Module Type' ); ?>:</label>
							</td>
							<td>
								<strong>
									<?php echo JText::_($this->row->module); ?>
								</strong>
							</td>
						</tr>
						<tr>
							<td class="key">
								<label for="title">
									<?php echo JText::_( 'Title' ); ?>:
								</label>
							</td>
							<td>
								<input class="text_area" type="text" name="title" id="title" size="35" value="<?php echo $this->row->title; ?>" />
							</td>
						</tr>
						<tr>
							<td width="100" class="key">
								<label><?php echo JText::_( 'Show title' ); ?>:</label>
							</td>
							<td>
								<label><?php echo $this->lists['showtitle']; ?></label>
							</td>
						</tr>
						<tr>
							<td valign="top" class="key">
								<label><?php echo JText::_( 'Published' ); ?>:</label>
							</td>
							<td>
								<label><?php echo $this->lists['published']; ?></label>
							</td>
						</tr>
						<tr>
							<td valign="top" class="key">
								<label for="position" class="hasTip" title="">
									<?php echo JText::_( 'Position' ); ?>:
								</label>
							</td>
							<td>
								<input type="text" id="position" class="combobox" name="position" value="<?php echo $this->row->position; ?>" />
								<span class='small' style="position:fixed;margin-left:5px;display:inline"><?php echo JText::_('Choose from list or specify your own position')?></span>
								<ul id="combobox-position" style="display:none">
								<?php
									$positions = $this->model->getPositions();
									foreach ($positions as $position) {
										echo '<li>'.$position.'</li>';
									}
								?>
								</ul>
							</td>
						</tr>
						<tr>
							<td valign="top"  class="key">
								<label for="ordering">
									<?php echo JText::_( 'Order' ); ?>:
								</label>
							</td>
							<td>
								<script language="javascript" type="text/javascript">
								<!--
								writeDynaList( 'class="inputbox" name="ordering" id="ordering" size="1"', orders, originalPos, originalPos, originalOrder );
								//-->
								</script>
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
							<td valign="top" class="key">
								<label><?php echo JText::_( 'ID' ); ?>:</label>
							</td>
							<td>
								<?php echo $this->row->id; ?>
							</td>
						</tr>
						<tr>
							<td valign="top" class="key">
								<label><?php echo JText::_( 'Description' ); ?>:</label>
							</td>
							<td>
								<?php echo JText::_($this->row->description); ?>
							</td>
						</tr>
					</table>
				</fieldset>
			</div>
			<div id="content_2" class="tab_content">
				<fieldset>
					<table class="admintable" cellspacing="1">
						<tr>
							<td valign="top" class="key">
								<label><?php echo JText::_( 'Menus' ); ?>:</label>
							</td>
							<td>
							<?php if ($this->row->client_id != 1) : ?>
								<?php if ($this->row->pages == 'all') { ?>
								<label for="menus-all"><input id="menus-all" type="radio" name="menus" value="all" onclick="allselections();" checked="checked" /><?php echo JText::_( 'All' ); ?></label>
								<label for="menus-none"><input id="menus-none" type="radio" name="menus" value="none" onclick="disableselections();" /><?php echo JText::_( 'None' ); ?></label>
								<label for="menus-select"><input id="menus-select" type="radio" name="menus" value="select" onclick="enableselections();" /><?php echo JText::_( 'Select From List' ); ?></label>
								<?php } elseif ($this->row->pages == 'none') { ?>
								<label for="menus-all"><input id="menus-all" type="radio" name="menus" value="all" onclick="allselections();" /><?php echo JText::_( 'All' ); ?></label>
								<label for="menus-none"><input id="menus-none" type="radio" name="menus" value="none" onclick="disableselections();" checked="checked" /><?php echo JText::_( 'None' ); ?></label>
								<label for="menus-select"><input id="menus-select" type="radio" name="menus" value="select" onclick="enableselections();" /><?php echo JText::_( 'Select From List' ); ?></label>
								<?php } else { ?>
								<label for="menus-all"><input id="menus-all" type="radio" name="menus" value="all" onclick="allselections();" /><?php echo JText::_( 'All' ); ?></label>
								<label for="menus-none"><input id="menus-none" type="radio" name="menus" value="none" onclick="disableselections();" /><?php echo JText::_( 'None' ); ?></label>
								<label for="menus-select"><input id="menus-select" type="radio" name="menus" value="select" onclick="enableselections();" checked="checked" /><?php echo JText::_( 'Select From List' ); ?></label>
								<?php } ?>
							<?php endif; ?>
							</td>
						</tr>
						<tr>
							<td valign="top" class="key">
								<label><?php echo JText::_( 'Menu Selection' ); ?>:</label>
							</td>
							<td>
								<?php echo $this->lists['selections']; ?>
							</td>
						</tr>
					</table>
					<?php if ($this->row->client_id != 1) : ?>
						<?php if ($this->row->pages == 'all') { ?>
						<script type="text/javascript">allselections();</script>
						<?php } elseif ($this->row->pages == 'none') { ?>
						<script type="text/javascript">disableselections();</script>
						<?php } else { ?>
						<?php } ?>
					<?php endif; ?>
				</fieldset>
			</div>
			<div id="content_3" class="tab_content">
				<fieldset class="adminform">
					<?php
						$p = $this->params;
						if($this->params = $p->render('params')) :
							echo $this->params;
						else :
							echo "<div style=\"text-align: center; padding: 5px; \">".JText::_('There are no parameters for this item')."</div>";
						endif;
						?>
						<?php if(!$this->adv_params) { ?>
						<div class='small' style="margin-top:10px">To edit advanced and other parameters, click <a href='<?php echo JURI::root().'administrator/index.php?option=com_modules&client=0&task=edit&cid[]='.$this->row->id ?>' target='_blank'>here</a></div>
						<?php } ?>
				</fieldset>
			</div>
			<div id="content_4" class="tab_content">
				<fieldset class="adminForm">
				<?php	
				if ($p->getNumParams('advanced')) {
					/* Advanced Parameters */
					echo "<h3>Advanced</h3>";
					if($params = $p->render('params', 'advanced')) :
						echo $params;
					else :
						echo "<div  style=\"text-align: center; padding: 5px; \">".JText::_('There are no advanced parameters for this item')."</div>";
					endif;
				}
				if ($p->getNumParams('legacy')) {
					echo "<h3>Legacy</h3>";
					/* Legacy Parameters */
					if($params = $p->render('params', 'legacy')) :
						echo $params;
					else :
						echo "<div  style=\"text-align: center; padding: 5px; \">".JText::_('There are no legacy parameters for this item')."</div>";
					endif;
				}
				
				if ($p->getNumParams('other')) {
				/* Other Parameters */
				if($params = $p->render('params', 'other')) :
					echo "<h3>Other</h3>";
					echo $params;
					else :
					echo "<div  style=\"text-align: center; padding: 5px; \">".JText::_('There are no other parameters for this item')."</div>";
					endif;
				}
				?>
				</fieldset>
			</div>
			<?php
			if ( !$this->row->module || $this->row->module == 'custom' || $this->row->module == 'mod_custom' ) {
				?>
			<div id="content_5" class="tab_content">
				<!-- Custom HTML Editor -->
				<fieldset class="adminform">

					<?php
					$editor 	=& JFactory::getEditor();
					// parameters : areaname, content, width, height, cols, rows
					 echo $editor->display( 'content', $this->row->content, '100%', '400', '60', '20', array('pagebreak', 'readmore') ) ;
					?>
				</fieldset>
			</div>
				<?php
			}
			?>
			<input type="hidden" name="option" value="com_frontendeditor" />
			<input type="hidden" name="c" value="module" />
			<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
			<input type="hidden" name="cid[]" value="<?php echo $this->row->id; ?>" />
			<input type="hidden" name="original" value="<?php echo $this->row->ordering; ?>" />
			<input type="hidden" name="module" value="<?php echo $this->row->module; ?>" />
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="client" value="<?php echo $this->client->id ?>" /> 
			<?php echo JHTML::_( 'form.token' ); ?>
		</form>
	</div>
</div>