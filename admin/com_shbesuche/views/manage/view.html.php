<?php


// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();
 
jimport( 'joomla.application.component.view' );
 

class ShbesucheViewmanage extends ShAdminView
{

    function display($tpl = null)
    {
		JToolBarHelper::title( 'Shbesucche Manager' , 'generic.png' );
		
		$db=JFactory::getDBO();
		$config =JFactory::getConfig();
    	date_default_timezone_set($config->get('offset'));	

		
		$date = JRequest::getVar('date');
 		if (!$date) {
			$date = date_create("now",new DateTimeZone(SHBESUCHE_TIMEZONE));
			$this->date = $date;
			parent::display($tpl);
		}
		else if ($date) {
			$this->date = date_create($date,new DateTimeZone(SHBESUCHE_TIMEZONE));
	 		parent::display($tpl);
		}
    }
}


