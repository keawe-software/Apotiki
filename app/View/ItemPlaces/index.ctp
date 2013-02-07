<div class="actions">
	<?php echo $this->Html->link(__('Logout'),array('controller'=>'users','action'=>'logout'))?>
</div>
<div class="itemPlaces index">
	<h2>
		<?php echo __('Item Places'); ?>
	</h2>
	<table cellpadding="0" cellspacing="0">
		<tr>
			<th><?php echo $this->Paginator->sort('id'); ?></th>
			<th><?php echo $this->Paginator->sort('item_id',__('Item')); ?></th>
			<th><?php echo $this->Paginator->sort('place_id',__('Location')); ?>
			</th>
			<th><?php echo $this->Paginator->sort('count',__('Count')); ?></th>
			<th class="actions"><?php echo __('Actions'); ?></th>
		</tr>
		<?php
	foreach ($itemPlaces as $itemPlace): ?>
		<tr>
			<td><?php echo h($itemPlace['ItemPlace']['id']); ?>&nbsp;</td>
			<td><?php echo $this->Html->link($itemPlace['Item']['name'], array('controller' => 'items', 'action' => 'view', $itemPlace['Item']['id'])); ?>
			</td>
			<td><?php echo $this->Html->link($itemPlace['Place']['description'], array('controller' => 'places', 'action' => 'view', $itemPlace['Place']['id'])); ?>
			</td>
			<td><?php echo h($itemPlace['ItemPlace']['count']); ?>&nbsp;</td>
			<td class="actions"><?php echo $this->Html->link(__('View'), array('action' => 'view', $itemPlace['ItemPlace']['id'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $itemPlace['ItemPlace']['id'])); ?>
				<?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $itemPlace['ItemPlace']['id']), null, __('Are you sure you want to delete # %s?', $itemPlace['ItemPlace']['id'])); ?>
			</td>
		</tr>
		<?php endforeach; ?>
	</table>
	<p>
		<?php
		echo $this->Paginator->counter(array(
	'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
	));
	?>
	</p>

	<div class="paging">
		<?php
		echo $this->Paginator->prev('< ' . __('previous'), array(), null, array('class' => 'prev disabled'));
		echo $this->Paginator->numbers(array('separator' => ''));
		echo $this->Paginator->next(__('next') . ' >', array(), null, array('class' => 'next disabled'));
		?>
	</div>
</div>
<div class="actions">
	<h3>
		<?php echo __('Actions'); ?>
	</h3>
	<ul>
		<li><?php echo $this->Html->link(__('New Item'), array('controller' => 'items', 'action' => 'add')); ?>
		</li>
		<li><?php echo $this->Html->link(__('New Item Place'), array('controller' => 'item_places', 'action' => 'add')); ?>
		</li>
		<li><?php echo $this->Html->link(__('New Property'), array('controller' => 'properties', 'action' => 'add')); ?>
		</li>
		<li><?php echo $this->Html->link(__('New User'), array('controller' => 'users', 'action' => 'add')); ?>
		</li>
	</ul>
</div>
<div class="actions">
	<h3>
		<?php echo __('Lists'); ?>
	</h3>
	<ul>
		<li><?php echo $this->Html->link(__('List Items'), array('controller' => 'items', 'action' => 'index')); ?>
		</li>
		<li><?php echo $this->Html->link(__('List Events'), array('controller' => 'events', 'action' => 'index')); ?>
		</li>
		<li><?php echo $this->Html->link(__('List Places'), array('controller' => 'places', 'action' => 'index')); ?>
		</li>
		<li><?php echo $this->Html->link(__('List Properties'), array('controller' => 'properties', 'action' => 'index')); ?>
		</li>
		<li><?php echo $this->Html->link(__('List Users'), array('controller' => 'users', 'action' => 'index')); ?>
		</li>
	</ul>
</div>