<?php 
    
    $doc = JFactory::getDocument();
   // $doc->addStyleSheet(JURI::root(false)."components/com_pbbooking/user_view.css");
jimport('cms.html.html');
    //push in mootools.
    Jhtml::_('behavior.framework');
    Jhtml::_('behavior.framework','more');
?>
<link rel="stylesheet" href="<?php echo JURI::root(false);?>components/com_pshbesuche/user_view.css" type="text/css"/>


<form action="<?php echo JRoute::_('index.php?option=com_shbesuche&task=save');?>" method="POST" id = "shbesuche-reservation-form">

<?php
        echo '<input type="hidden" name="date" id="text-date" value='.$this->dateparam->format('Ymd').'>';
    ?>
    
    <h2><?php echo JText::_('COM_SHBESUCHE_YOURDETAILS');?></h2>

<table style="width:80%;" class="pbbooking-data-table">
    <tr>
        <td>Name</td><td><input type="text" name="name"/></td>
    </tr>
     <tr>
            <td>Nachame</td><td><input type="text" name="nachname"/></td>
            </tr>
            
         <tr>
            <td>Tel</td><td><input type="text" name="tel"/></td>
            </tr>
            
            <tr>
            <td>email</td><td><input type="text" name="email"/></td>
            </tr>
            
               


</table>

<h2><?php echo JText::_('COM_SHBESUCHE_BOOKINGDETAILS');?></h2>
    <table class="pbbooking-data-table">
        <tr>
            <td style="width:200px;"><?php echo JText::_('COM_SHBESUCHE_SUCCESS_DATE');?></td>
            <td><?php echo JHtml::_('date',$this->dateparam->format(DATE_ATOM),JText::_('COM_SHBESUCHE_SUCCESS_DATE_FORMAT'));?></td>
        </tr>
        <tr>
            <td><?php echo JText::_('COM_SHBESUCHE_SUCCESS_TIME');?></td>
            <td><?php echo JHtml::_('date',$this->dateparam->format(DATE_ATOM),JText::_('COM_SHBESUCHE_SUCCESS_TIME_FORMAT'));?></td>
        </tr>
        <tr>
            <td><?php echo JText::_('COM_SHBESUCHE_SUCCESS_CALENDAR');?></td>
            <td><?php echo $this->cal->name;?></td>
        </tr>
       
    </table>



<div style="text-align:center;">
        <p></p><input type="submit" value="<?php echo JText::_('COM_SHBESUCHE_SUBMIT_BUTTON');?>" id="pbbooking-submit"></p>
    </div>
    
    
    
    <input type="hidden" name="cal_id" id="text-cal-id" value="1"/> 
    <input type="hidden" name="date" value="<?php echo $this->dateparam->format(DATE_ATOM);?>"/>
    <input type="hidden" name="treatment_time" value="20"/>
</form>