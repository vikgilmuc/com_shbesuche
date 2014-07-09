<?php 

/**
* @package		PurpleBeanie.PBBooking
* @license		GNU General Public License version 2 or later; see LICENSE.txt
* @link		http://www.purplebeanie.com
*/

?>

<table class="diary-table">

	<tr>
		<th></th><th colspan = "<?php echo count($this->cals);?>"><?php echo JHtml::_('date',$this->date->format(DATE_ATOM),'d F Y');?></th>
	</tr>
	<tr>
		<th></th>
		<?php foreach ($this->cals as $cal):?>
			<th><?php echo $cal->name;?></th>
		<?php endforeach;?>
	</tr>


	<!-- build table content-->
	<?php while($this->dt_start<=$this->dt_end) :?>
		<?php $dt_slot_end = date_create($this->dt_start->format(DATE_ATOM),new DateTimeZone(SHBESUCHE_TIMEZONE));?>
		<?php $dt_slot_end->modify('+ '.$this->config->time_increment.' minutes');?>
		<tr>
			<th><?php echo Jhtml::_('date',$this->dt_start->format(DATE_ATOM),JText::_('COM_SHBESUCHE_SUCCESS_TIME_FORMAT'));?></th>
			<?php foreach ($this->cal_objs as $cal) :?>
				<td>
					<?php $event = $cal->is_free_from_to($this->dt_start,$dt_slot_end,true);?>
					<?php if ($event && is_bool($event)!= true) :?>
						<a href="<?php echo JURI::root(false);?>administrator/index.php?option=com_shbesuche&controller=manage&task=edit&id=<?php echo $event->id;?>">
							<?php echo $event->admin_summary();?>
						</a>
					<?php endif;?>
				</td>
			<?php endforeach;?>
		</tr>
		<?php $this->dt_start->modify('+ '.$this->config->time_increment.' minutes');?>
	<!-- end table content-->

	<?php endwhile;?>

</table>
