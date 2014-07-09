<?php


 
// No direct access
 
defined('_JEXEC') or die('Restricted access'); 
	
?>

<h1><?php echo JTEXT::_('COM_SHBESUCHE_SUCCESS_HEADING');?></h1>
<p><?php echo JTEXT::_('COM_SHBESUCHE_VALIDATED_MESSAGE');?></p>

<p><em><?php echo JTEXT::_('COM_SHBESUCHE_SUCCESS_SUB_HEADING');?></em></p>

<table>
	<tr>
		<th>
			<?php echo JTEXT::_('COM_SHBESUCHE_SUCCESS_DATE');?>
		</th>
		<td>
			<?php echo JHtml::_('date',date_create($this->pending->dtstart,new DateTimeZone(SHBESUCHE_TIMEZONE))->format(DATE_ATOM),$this->config->date_format_message);?>
		</td>
	<tr><th><?php echo JTEXT::_('COM_SHBESUCHE_SUCCESS_TIME');?></th><td><?php echo JHtml::_('date',date_create($this->pending->dtstart,new DateTimezone(SHBESUCHE_TIMEZONE))->format(DATE_ATOM),JText::_('COM_SHBESUCHE_SUCCESS_TIME_FORMAT'));?></td></tr>
	
	<?php if (isset($this->calendar)) :?>
		<tr><th><?php echo JTEXT::_('COM_SHBESUCHE_SUCCESS_CALENDAR');?></th><td><?php echo $this->calendar->name;?></td></tr>
	<?php endif;?>
</table>
