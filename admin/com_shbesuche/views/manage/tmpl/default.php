<?php 

	

	$bom = date_create($this->date->format(DATE_ATOM),new DateTimeZone(SHBESUCHE_TIMEZONE));
	$bom->setDate($this->date->format('Y'),$this->date->format('m'),1);

	$start_selected_day = date_create($this->date->format(DATE_ATOM),new DateTimeZone(SHBESUCHE_TIMEZONE));
	$end_selected_day = clone $this->date;	
	$eom = date_create($bom->format(DATE_ATOM),new DateTimeZone(SHBESUCHE_TIMEZONE));
	$curr_day = date_create($bom->format(DATE_ATOM),new DateTimeZone(SHBESUCHE_TIMEZONE));
	$prev_month = date_create($bom->format(DATE_ATOM),new DateTimeZone(SHBESUCHE_TIMEZONE));

	$eom->modify('+1 month');
	$prev_month->modify('-1 day');

	$start_selected_day->setTime(0,0,0);
	$end_selected_day->setTime(23,59,59);
	
	$version = new JVersion;
?>




<style>

table.calendar-table {
	border:1px solid black;
	border-collapse:collapse;
}


table.calendar-table > tr,td,th{
	border:1px solid black;
	padding: 5px;
	width:45px;
	height:45px;
	text-align:center;
	font-size:13px;
}

td.selected-date {
	background-color:yellow;
}

table.diary-table {
	width:100%;
	border-collapse: collapse;
}

td.bookings {
	background-color:green;
	color:white;
}

td.bookings a {
	color:white;
}

.icon-32-printer {
	background:url('components/com_pbbooking/images/printer.png');
}


</style>

<div class="bootstrap-wrap">
<div class="row-fluid">


	<div class="span4">

		<table class="calendar-table">
			<tr>
				<th colspan=2>
					<a href="<?php echo JURI::root(false);?>administrator/index.php?option=com_pbbooking&controller=manage&task=display&date=<?php echo $prev_month->format('Y-m-d');?>">
						<<
					</a>
				</th>
				<th colspan=3><?php echo JHtml::_('date',$bom->format(DATE_ATOM),'F Y');?></th>
				<th colspan=2>
					<a href="<?php echo JURI::root(false);?>administrator/index.php?option=com_pbbooking&controller=manage&task=display&date=<?php echo $eom->format('Y-m-d');?>">
						>>
					</a>
				</th>
			</tr>
			<tr>
				<th><?php echo Jtext::_('COM_SHBESUCHE_SUNDAY_ABBR');?></th>
				<th><?php echo Jtext::_('COM_SHBESUCHE_MONDAY_ABBR');?></th>
				<th><?php echo Jtext::_('COM_SHBESUCHE_TUESDAY_ABBR');?></th>
				<th><?php echo Jtext::_('COM_SHBESUCHE_WEDNESDAY_ABBR');?></th>
				<th><?php echo Jtext::_('COM_SHBESUCHE_THURSDAY_ABBR');?></th>
				<th><?php echo Jtext::_('COM_SHBESUCHE_FRIDAY_ABBR');?></th>
				<th><?php echo Jtext::_('COM_SHBESUCHE_SATURDAY_ABBR');?></th>
			</tr>
			<!-- draw date rows -->
			<tr>
						
				<!-- calc padding -->
				<?php for ($i=0;$i<$bom->format('w');$i++): ?>
					<td></td>
				<?php endfor;?>
				
				<?php while ($curr_day < $eom) :?>
					<?php if ($curr_day >= $start_selected_day && $curr_day <= $end_selected_day) :?>
						<td class="selected-date">
					<?php else:?>
						<td <?php echo (ShbesucheHelper::booking_for_day($curr_day)) ? 'class="bookings"' : '';?>  >
					<?php endif;?>
					<?php if (!Shbesuchehelper::is_blocked_date($curr_day)) :?>
						<a href="<?php echo JURI::root(false);?>administrator/index.php?option=com_shbesuche&controller=manage&task=display&date=<?php echo $curr_day->format('Y-m-d');?>">
							<?php echo JHtml::_('date',$curr_day->format(DATE_ATOM),$this->config->date_format_cell);?></td>
						</a>				
					<?php else :?>
						<?php echo JHtml::_('date',$curr_day->format(DATE_ATOM),$this->config->date_format_cell);?></td>
					<?php endif;?>
					<?php if ($curr_day->format('w') == 6) :?>
						</tr><tr>
					<?php endif;?>
					<?php $curr_day->modify('+1 day');?>
				<?php endwhile;?>
			</tr>
			<!-- end draw date rows -->
		
		</table>
	</div>

	<div class="span8">
		
		<?php if ($this->config->use_freeflow == 1 ):?>
			<?php include_once('default_freeflow_calendar.php');?>
		<?php else:?>
			<?php include_once('default_slot_based_calendar.php');?>
		<?php endif;?>

	</div>

	<form action="index.php" method="get" name="adminForm" id="adminForm"/>
	<input type="hidden" name="option" value="com_shbesuche"/>
	<input type="hidden" name="controller" value="manage"/>
	<input type="hidden" name="task" value=""/>
	<input type="hidden" name="date" value="<?php echo $this->date->format('Y-m-d');?>"/>
	</form>

</div>

</div>