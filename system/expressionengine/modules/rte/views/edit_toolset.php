	<?=form_open($action.($is_private ? AMP.'private=true' : ''))?>

<?php	if ( ! $is_private): ?>
	<table class="rte-toolset-settings" cellspacing="0" cellpadding="0" border="0">
		<tr>
			<td><label for="rte-toolset-name"><?=lang('toolset_name')?></label></td>
			<td><?=form_input(array('name'=>'rte_toolset_name','id'=>'rte-toolset-name','value'=>$toolset_name))?></td>
		</tr>
	</table>
<?php 	else: ?>
	<input type="hidden" name="private" value="true"/>
	<input type="hidden" name="rte_toolset_name" value="<?=lang('my_custom_toolset')?>"/>
<?php 	endif; ?>

	<div class="rte-toolset-builder ui-widget">
		<input type="hidden" id="rte-toolset-tools" name="rte_selected_tools" value="<?=implode('|',$toolset_tools)?>"/>

		<div class="notice"></div>	

		<table cellspacing="0" cellpadding="0" border="0">
			<tr>
				<td class="rte-toolset-builder-pane">
					<strong><?=lang('available_tools')?></strong>
					<div class="rte-toolset-builder-scrollpane" tabindex="0">
						<ul id="rte-tools-unused" class="rte-tools-connected">
<?php	foreach ($unused_tools as $tool_id): ?>
							<li class="rte-tool" data-tool-id="<?=$tool_id?>"><?=$available_tools[$tool_id]?></li>
<?php	endforeach; ?>
						</ul>
					</div>
				</td>

				<td class="rte-toolset-builder-divider">
				</td>

				<td class="rte-toolset-builder-pane">
					<strong><?=lang('tools_in_toolset')?></strong>
					<div class="rte-toolset-builder-scrollpane" tabindex="0">
						<ul id="rte-tools-selected" class="rte-tools-connected ui-sortable">
<?php	foreach ($toolset_tools as $tool_id): ?>
							<li class="rte-tool" data-tool-id="<?=$tool_id?>"><?=$available_tools[$tool_id]?></li>
<?php	endforeach; ?>
						</ul>
					</div>
				</td>
			</tr>
		</table>
	</div>

	<p>
		<?=form_submit(array('name' => 'submit', 'value' => lang('submit'), 'class' => 'submit'));?>
		&nbsp; or
		<a id="rte-builder-closer" href="<?=$module_base?>"><?=lang('cancel')?></a>
	</p>
	
	<?=form_close();?>