<?php
 


// No direct access
 
defined('_JEXEC') or die('Restricted access'); 

?>


<h1><?php echo ($this->config->validation == 'admin') ? Jtext::_('COM_SHBESUCHE_SUCCESS_PENDING_ADMIN_VALIDATION') : JTEXT::_('COM_SHBESUCHE_SUCCESS_HEADING');?></h1>
<p><?php echo ($this->config->validation == 'admin') ? JText::_('COM_SHBESUCHE_SUCCESS_PENDNG_ADMIN_VALIDATION_MSG') : JTEXT::_('COM_SHBESUCHE_SUCCESS_MSG');?></p>

<p><em><?php echo JTEXT::_('COM_SHBESUCHE_SUCCESS_SUB_HEADING');?></em></p>

<table>
	<tr>
		<th>
			<?php echo JTEXT::_('COM_SHBESUCHE_SUCCESS_DATE');?>
		</th>
		<td>
			<?php 
			echo($this->pending->dtstart);
			echo($this->config->date_format_message);
            
			echo JHtml::_('date',date_create($this->pending->dtstart,new DateTimeZone(SHBESUCHE_TIMEZONE))->format(DATE_ATOM),$this->config->date_format_message);?>
		</td>
	<tr><th><?php echo JTEXT::_('COM_SHBESUCHE_SUCCESS_TIME');?></th><td><?php echo Jhtml::_('date',date_create($this->pending->dtstart,new DateTimeZone(SHBESUCHE_TIMEZONE))->format(DATE_ATOM),JText::_('COM_SHBESUCHE_SUCCESS_TIME_FORMAT'));?></td></tr>
	<tr><th><?php echo JTEXT::_('COM_SHBESUCHE_BOOKINGTYPE');?></th><td>Besuch</td></tr>
	<?php if (isset($this->calendar)) {?>
		<tr><th><?php echo JTEXT::_('COM_SHBESUCHE_SUCCESS_CALENDAR');?></th><td><?php echo $this->calendar->name;?></td></tr>
	<?php
	}?>
</table>

<p><?php echo ($this->config->validation != 'admin') ? JTEXT::_('COM_SHBESUCHE_SUCCESS_VALIDATION_MESSAGE') : JText::_('COM_SHBESUCHE_SUCCESS_PENDING_ADMIN_VALIDATION_NOTICE');?></p>