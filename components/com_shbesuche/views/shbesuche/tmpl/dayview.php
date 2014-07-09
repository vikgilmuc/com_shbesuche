<?php 
    
    $doc = JFactory::getDocument();
    $doc->addStyleSheet(JURI::root(false)."components/com_pbbooking/user_view.css");
    jimport('cms.html.html');
?>
<h1><?php echo JText::_('COM_SHBESUCHE_DAY_VIEW_HEADING').' '.Jhtml::_('date',$this->dateparam->format(DATE_ATOM),JText::_('COM_SHBESUCHE_DAY_VIEW_DATE_FORMAT'));?></h1>
<table id="pbbooking">
<!-- Draw header row showing calendars across the top....-->
    <tr>
        <th></th> <!-- first column left blank to display time slots -->
        <?php foreach ($this->cals as $cal) :?>
            <th><?php echo $cal->name;?></th>
        <?php endforeach;?>
    </tr>


    <!-- draw table data rows -->

    <?php while ($this->day_dt_start <= $this->dt_last_slot) :?>
        <?php $slot_end = date_create($this->day_dt_start->format(DATE_ATOM),new DateTimezone(SHBESUCHE_TIMEZONE));?>
        <?php $slot_end->modify('+ '.$this->config->time_increment.' minutes');?>
        <tr>
            <th><?php echo Jhtml::_('date',$this->day_dt_start->format(DATE_ATOM),JText::_('COM_SHBESUCHE_SUCCESS_TIME_FORMAT'));?></th>
            <?php foreach ($this->cals as $cal) :?>
                
                <td class="pbbooking-<?php echo (!$cal->is_free_from_to($this->day_dt_start,$slot_end)) ? 'free' : 'busy';?>-cell">
                    <?php if ($this->day_dt_start>date_create("now",new DateTimeZone(SHBESUCHE_TIMEZONE)) && !$cal->is_free_from_to($this->day_dt_start,$slot_end)) :?>
                        <a href="<?php echo JRoute::_('index.php?option=com_shbesuche&task=create&dtstart='.$this->day_dt_start->format('YmdHi').'&cal_id='.$cal->cal_id);?>">
                            <?php echo (!$cal->is_free_from_to($this->day_dt_start,$slot_end)) ? JText::_('COM_SHBESUCHE_FREE') : JText::_('COM_SHBESUCHE_BUSY');?>
                        </a>
                    <?php else :?>
                        <?php echo JText::_('COM_SHBESUCHE_BUSY');?>
                    <?php endif;?>
                </td>
            <?php endforeach;?>
        </tr>
        <?php $this->day_dt_start->modify('+ '.$this->config->time_increment.' minutes');?>
    <?php endwhile;?>

    <!-- end draw table data rows-->

</table>